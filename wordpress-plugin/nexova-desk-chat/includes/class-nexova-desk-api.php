<?php
/**
 * API Client — Nexova Desk
 * Gestiona todas las peticiones HTTP hacia el servidor de Nexova Desk.
 *
 * @package NexovaDeskChat
 * @copyright 2024-2026 Nexova Digital Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Nexova_Desk_API {

    private string $base_url;
    private string $token;

    public function __construct() {
        $this->base_url = rtrim( (string) get_option( NEXOVA_DESK_OPTION_URL, '' ), '/' );
        $this->token    = (string) get_option( NEXOVA_DESK_OPTION_TOKEN, '' );
    }

    // ── Conexión ──────────────────────────────────────────────────────────────

    public function is_connected(): bool {
        return ! empty( $this->base_url ) && ! empty( $this->token );
    }

    /**
     * Verifica que el token guardado sigue siendo válido en el servidor.
     */
    public function verify_token(): bool {
        $response = $this->get( '/api/wp/verify' );
        return ! is_wp_error( $response ) && isset( $response['ok'] ) && $response['ok'];
    }

    // ── Widgets ───────────────────────────────────────────────────────────────

    /**
     * Retorna los widgets disponibles para la organización conectada.
     * [ ['id'=>1, 'name'=>'Widget Principal', 'token'=>'xxx', 'is_active'=>true], ... ]
     */
    public function get_widgets(): array {
        $response = $this->get( '/api/wp/widgets' );
        if ( is_wp_error( $response ) ) return [];
        return $response['widgets'] ?? [];
    }

    /**
     * Retorna los datos de un widget específico por su ID.
     */
    public function get_widget( int $id ): ?array {
        $response = $this->get( "/api/wp/widgets/{$id}" );
        if ( is_wp_error( $response ) ) return null;
        return $response['widget'] ?? null;
    }

    // ── Pedidos WooCommerce ───────────────────────────────────────────────────

    /**
     * Consulta los pedidos de un cliente en WooCommerce via Nexova Desk.
     * El servidor hace el lookup en la base de datos de WC usando los datos del contacto.
     */
    public function get_customer_orders( string $customer_email ): array {
        // Los pedidos se consultan localmente en WC (no via Nexova Desk)
        // Este método es un helper para el frontend JS
        if ( ! function_exists( 'wc_get_orders' ) ) return [];

        $orders = wc_get_orders( [
            'billing_email' => $customer_email,
            'limit'         => 5,
            'orderby'       => 'date',
            'order'         => 'DESC',
            'status'        => [ 'processing', 'completed', 'on-hold', 'pending' ],
        ] );

        $result = [];
        foreach ( $orders as $order ) {
            $result[] = [
                'id'     => $order->get_id(),
                'number' => $order->get_order_number(),
                'status' => wc_get_order_status_name( $order->get_status() ),
                'total'  => $order->get_formatted_order_total(),
                'date'   => $order->get_date_created()?->date_i18n( get_option( 'date_format' ) ),
                'items'  => array_map( fn( $item ) => $item->get_name(), array_values( $order->get_items() ) ),
            ];
        }
        return $result;
    }

    // ── HTTP Helpers ──────────────────────────────────────────────────────────

    private function get( string $endpoint ): array|\WP_Error {
        if ( empty( $this->base_url ) || empty( $this->token ) ) {
            return new \WP_Error( 'not_connected', __( 'Plugin no conectado a Nexova Desk.', 'nexova-desk-chat' ) );
        }

        $response = wp_remote_get( $this->base_url . $endpoint, [
            'timeout' => 15,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept'        => 'application/json',
                'X-WP-Plugin'   => 'nexova-desk-chat/' . NEXOVA_DESK_VERSION,
            ],
        ] );

        return $this->parse_response( $response );
    }

    private function post( string $endpoint, array $body = [] ): array|\WP_Error {
        if ( empty( $this->base_url ) || empty( $this->token ) ) {
            return new \WP_Error( 'not_connected', __( 'Plugin no conectado a Nexova Desk.', 'nexova-desk-chat' ) );
        }

        $response = wp_remote_post( $this->base_url . $endpoint, [
            'timeout'     => 15,
            'headers'     => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'X-WP-Plugin'   => 'nexova-desk-chat/' . NEXOVA_DESK_VERSION,
            ],
            'body'        => wp_json_encode( $body ),
        ] );

        return $this->parse_response( $response );
    }

    private function parse_response( $response ): array|\WP_Error {
        if ( is_wp_error( $response ) ) return $response;

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $code >= 400 ) {
            $msg = $data['message'] ?? $data['error'] ?? "HTTP {$code}";
            return new \WP_Error( "nexova_http_{$code}", $msg );
        }

        return is_array( $data ) ? $data : [];
    }

    public function get_base_url(): string { return $this->base_url; }
}
