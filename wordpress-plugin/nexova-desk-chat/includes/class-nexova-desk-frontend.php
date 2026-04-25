<?php
/**
 * Frontend — inyección del widget en el sitio público.
 *
 * @package NexovaDeskChat
 * @copyright 2024-2026 Nexova Digital Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Nexova_Desk_Frontend {

    private array  $cfg;
    private string $server_url;
    private string $token;

    public function __construct() {
        $this->cfg        = (array) get_option( NEXOVA_DESK_OPTION_CFG, [] );
        $this->server_url = rtrim( (string) get_option( NEXOVA_DESK_OPTION_URL, '' ), '/' );
        $this->token      = (string) get_option( NEXOVA_DESK_OPTION_TOKEN, '' );

        if ( empty( $this->token ) || empty( $this->server_url ) ) return;
        if ( empty( $this->cfg['widget_token'] ?? '' ) )            return;

        add_action( 'wp_footer',                 [ $this, 'inject_widget' ], 100 );
        add_action( 'wp_ajax_nexova_desk_orders',        [ $this, 'ajax_orders' ] );
        add_action( 'wp_ajax_nopriv_nexova_desk_orders', [ $this, 'ajax_orders' ] );
    }

    // ── Inyección principal ───────────────────────────────────────────────────

    public function inject_widget(): void {
        if ( ! $this->should_show() ) return;

        $widget_token = esc_js( $this->cfg['widget_token'] ?? '' );
        $api_url      = esc_js( $this->server_url );

        // Identidad del cliente logueado
        $customer_js = $this->build_customer_js();

        // Contexto de la tienda WooCommerce (JSON)
        $store_context_js = $this->build_store_context_js();

        // URL AJAX para consulta de pedidos
        $ajax_url = esc_js( admin_url( 'admin-ajax.php' ) );
        $nonce    = esc_js( wp_create_nonce( 'nexova_desk_orders' ) );

        echo <<<HTML
<!-- Nexova Desk Chat v{$this->get_version()} — nexova.digital -->
<script data-cfasync="false">
(function(){
    window.NexovaChatConfig = {
        token: "{$widget_token}",
        apiUrl: "{$api_url}",
        {$customer_js}
        {$store_context_js}
        _wp: {
            ajaxUrl: "{$ajax_url}",
            nonce: "{$nonce}",
            ordersEnabled: {$this->orders_enabled_js()}
        }
    };
})();
</script>
<script data-cfasync="false" src="{$api_url}/widget.js?v={$this->get_version()}" defer></script>
<!-- /Nexova Desk Chat -->
HTML;
    }

    // ── Identidad del cliente WooCommerce ─────────────────────────────────────

    private function build_customer_js(): string {
        if ( ! is_user_logged_in() || ! function_exists( 'WC' ) ) {
            return '';
        }

        $user     = wp_get_current_user();
        $customer = new WC_Customer( $user->ID );

        $id       = (int)    $user->ID;
        $email    = esc_js(  $user->user_email );
        $name     = esc_js(  trim( $user->first_name . ' ' . $user->last_name ) ?: $user->display_name );
        $phone    = esc_js(  $customer->get_billing_phone() );

        // HMAC firmado con el widget token (igual que el método manual de la installation page)
        $widget_token = $this->cfg['widget_token'] ?? '';
        $payload      = $id . '|' . $email;
        $hmac         = hash_hmac( 'sha256', $payload, $widget_token );

        return <<<JS
woo_customer: {
            id: {$id},
            email: "{$email}",
            name: "{$name}",
            phone: "{$phone}"
        },
        woo_token: "{$hmac}",
JS;
    }

    // ── Contexto de tienda WooCommerce ───────────────────────────────────────

    /**
     * Recopila información de la tienda y catálogo de WooCommerce para
     * inyectarla como storeContext en NexovaChatConfig.
     * Si WooCommerce no está activo, retorna cadena vacía.
     */
    private function build_store_context_js(): string {
        if ( ! function_exists( 'WC' ) ) return '';

        $ctx = [];

        // Info básica de la tienda
        $ctx['store_name']        = get_bloginfo( 'name' );
        $ctx['store_description'] = get_bloginfo( 'description' );
        $ctx['store_url']         = get_site_url();
        $ctx['currency']          = get_woocommerce_currency();

        // Categorías (máx 10)
        $cats = get_terms( [
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'number'     => 10,
            'parent'     => 0,
        ] );
        if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
            $ctx['categories'] = array_map( fn( $t ) => [
                'name' => $t->name,
                'slug' => $t->slug,
            ], $cats );
        }

        // Producto actual (si el visitante está en una página de producto)
        if ( is_product() ) {
            global $post;
            $product = wc_get_product( $post->ID );
            if ( $product ) {
                $ctx['current_product'] = $this->format_product( $product );
            }
        }

        // Catálogo: productos publicados (máx 20, ordenados por popularidad)
        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'orderby'        => 'popularity',
            'order'          => 'DESC',
        ];
        $query = new \WP_Query( $args );
        if ( $query->have_posts() ) {
            $ctx['products'] = [];
            while ( $query->have_posts() ) {
                $query->the_post();
                $product = wc_get_product( get_the_ID() );
                if ( $product && $product->is_visible() ) {
                    $ctx['products'][] = $this->format_product( $product );
                }
            }
            wp_reset_postdata();
        }

        if ( empty( $ctx ) ) return '';

        $json = wp_json_encode( $ctx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

        return "storeContext: {$json},";
    }

    /**
     * Convierte un WC_Product en array resumido para el contexto del bot.
     */
    private function format_product( \WC_Product $product ): array {
        $data = [
            'name'  => $product->get_name(),
            'price' => $product->get_price_html()
                ? wp_strip_all_tags( $product->get_price_html() )
                : (string) $product->get_price(),
            'sku'   => $product->get_sku() ?: null,
            'url'   => get_permalink( $product->get_id() ),
        ];

        // Stock
        if ( $product->managing_stock() ) {
            $qty = $product->get_stock_quantity();
            $data['stock'] = $qty > 0 ? "Disponible ({$qty} unidades)" : 'Sin stock';
        } else {
            $data['stock'] = $product->is_in_stock() ? 'Disponible' : 'Sin stock';
        }

        // Descripción corta (máx 200 chars)
        $desc = $product->get_short_description() ?: $product->get_description();
        if ( $desc ) {
            $data['description'] = mb_substr( wp_strip_all_tags( $desc ), 0, 200 );
        }

        return array_filter( $data, fn( $v ) => $v !== null && $v !== '' );
    }

    // ── Visibilidad ───────────────────────────────────────────────────────────

    private function should_show(): bool {
        $visibility = $this->cfg['visibility'] ?? 'all';
        $hide_ids   = array_map( 'intval', (array) ( $this->cfg['hide_page_ids'] ?? [] ) );
        $show_ids   = array_map( 'intval', (array) ( $this->cfg['show_page_ids'] ?? [] ) );

        // Páginas excluidas explícitamente
        if ( ! empty( $hide_ids ) && is_page( $hide_ids ) ) return false;

        switch ( $visibility ) {
            case 'all':
                return true;

            case 'shop_only':
                return is_woocommerce() || is_cart() || is_checkout() || is_account_page();

            case 'selected':
                if ( empty( $show_ids ) ) return false;
                $current = get_queried_object_id();
                return in_array( $current, $show_ids, true );

            default:
                return true;
        }
    }

    // ── AJAX: pedidos del cliente ─────────────────────────────────────────────

    public function ajax_orders(): void {
        check_ajax_referer( 'nexova_desk_orders', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => 'No autenticado' ], 401 );
        }

        if ( ! ( $this->cfg['orders_enabled'] ?? false ) ) {
            wp_send_json_error( [ 'message' => 'Consulta de pedidos deshabilitada' ], 403 );
        }

        $api    = new Nexova_Desk_API();
        $email  = wp_get_current_user()->user_email;
        $orders = $api->get_customer_orders( $email );

        wp_send_json_success( [ 'orders' => $orders ] );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function orders_enabled_js(): string {
        return function_exists( 'WC' ) ? 'true' : 'false';
    }

    private function get_version(): string {
        return NEXOVA_DESK_VERSION;
    }
}
