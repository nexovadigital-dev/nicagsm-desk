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

    /**
     * Obtiene el estado completo de la licencia desde el servidor.
     * Resultado cacheado 1 hora con transient de WP.
     *
     * @return array{ok:bool, active:bool, org_name:string, org_plan:string, is_partner:bool, is_edge:bool}|null
     *         null = no conectado; array con active=false = licencia desactivada.
     */
    public function get_license_status(): ?array {
        if ( ! $this->is_connected() ) return null;

        $cache_key = 'nexova_desk_license_' . md5( $this->token );
        $cached    = get_transient( $cache_key );
        if ( $cached !== false ) return $cached;

        $response = $this->get( '/api/wp/verify' );
        if ( is_wp_error( $response ) ) return null;

        $status = [
            'ok'         => (bool) ( $response['ok']         ?? false ),
            'active'     => (bool) ( $response['active']      ?? false ),
            'org_name'   => (string) ( $response['org_name']  ?? '' ),
            'org_plan'   => (string) ( $response['org_plan']  ?? 'free' ),
            'is_partner' => (bool) ( $response['is_partner'] ?? false ),
            'is_edge'    => (bool) ( $response['is_edge']    ?? false ),
        ];

        $ttl = $status['ok'] ? HOUR_IN_SECONDS : ( 5 * MINUTE_IN_SECONDS );
        set_transient( $cache_key, $status, $ttl );

        return $status;
    }

    /**
     * Fuerza refresco del caché de licencia (llama al conectar/desconectar).
     */
    public function flush_license_cache(): void {
        $cache_key = 'nexova_desk_license_' . md5( $this->token );
        delete_transient( $cache_key );
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

    // ── WooCommerce widget toggles ────────────────────────────────────────────

    /**
     * Sincroniza los toggles de WooCommerce al servidor Nexova Desk.
     * Llama PATCH /api/wp/widgets/{id} con woo_integration_enabled + woo_orders_enabled.
     * El servidor aplica la regla "one-woo-per-org" automáticamente.
     */
    public function sync_woo_toggles( int $widget_id, bool $woo_context_enabled, bool $orders_enabled ): bool {
        if ( ! $widget_id ) return false;

        $response = $this->patch( "/api/wp/widgets/{$widget_id}", [
            'woo_integration_enabled' => $woo_context_enabled,
            'woo_orders_enabled'      => $orders_enabled,
        ] );

        return ! is_wp_error( $response ) && ! empty( $response['ok'] );
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

    private function patch( string $endpoint, array $body = [] ): array|\WP_Error {
        if ( empty( $this->base_url ) || empty( $this->token ) ) {
            return new \WP_Error( 'not_connected', __( 'Plugin no conectado a Nexova Desk.', 'nexova-desk-chat' ) );
        }

        $response = wp_remote_request( $this->base_url . $endpoint, [
            'method'      => 'PATCH',
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
        $body = ltrim( wp_remote_retrieve_body( $response ), "\xEF\xBB\xBF" );
        $data = json_decode( $body, true );

        if ( $code >= 400 ) {
            $msg = $data['message'] ?? $data['error'] ?? "HTTP {$code}";
            return new \WP_Error( "nexova_http_{$code}", $msg );
        }

        return is_array( $data ) ? $data : [];
    }

    public function get_base_url(): string { return $this->base_url; }
}
