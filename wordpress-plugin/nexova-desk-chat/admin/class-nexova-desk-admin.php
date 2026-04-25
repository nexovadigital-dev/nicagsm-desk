<?php
/**
 * Panel de administración — Nexova Desk Chat
 * Tres pestañas: Conexión · Widgets · Visibilidad
 *
 * @package NexovaDeskChat
 * @copyright 2024-2026 Nexova Digital Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Nexova_Desk_Admin {

    private Nexova_Desk_API $api;

    public function __construct() {
        $this->api = new Nexova_Desk_API();

        add_action( 'admin_menu',            [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_nexova_desk_save_connection', [ $this, 'ajax_save_connection' ] );
        add_action( 'wp_ajax_nexova_desk_disconnect',      [ $this, 'ajax_disconnect' ] );
        add_action( 'wp_ajax_nexova_desk_save_config',     [ $this, 'ajax_save_config' ] );
        add_action( 'wp_ajax_nexova_desk_fetch_widgets',   [ $this, 'ajax_fetch_widgets' ] );
        add_action( 'wp_ajax_nexova_desk_search_pages',    [ $this, 'ajax_search_pages' ] );
    }

    // ── Menú ──────────────────────────────────────────────────────────────────

    public function register_menu(): void {
        add_menu_page(
            __( 'Nexova Desk Chat', 'nexova-desk-chat' ),
            __( 'Nexova Desk', 'nexova-desk-chat' ),
            'manage_options',
            'nexova-desk-chat',
            [ $this, 'render_page' ],
            $this->get_menu_icon(),
            58
        );
    }

    // ── Assets ────────────────────────────────────────────────────────────────

    public function enqueue_assets( string $hook ): void {
        if ( ! str_contains( $hook, 'nexova-desk-chat' ) ) return;

        wp_enqueue_style(
            'nexova-desk-admin',
            NEXOVA_DESK_URL . 'assets/css/nexova-desk-admin.css',
            [],
            NEXOVA_DESK_VERSION . '.' . filemtime( NEXOVA_DESK_PATH . 'assets/css/nexova-desk-admin.css' )
        );

        wp_enqueue_script(
            'nexova-desk-admin',
            NEXOVA_DESK_URL . 'assets/js/nexova-desk-admin.js',
            [ 'jquery' ],
            NEXOVA_DESK_VERSION . '.' . filemtime( NEXOVA_DESK_PATH . 'assets/js/nexova-desk-admin.js' ),
            true
        );

        wp_localize_script( 'nexova-desk-admin', 'NexovaDeskAdmin', [
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'nexova_desk_admin' ),
            'serverUrl'   => get_option( NEXOVA_DESK_OPTION_URL, '' ),
            'isConnected' => $this->api->is_connected() ? '1' : '0',
            'i18n'        => [
                'connecting'    => __( 'Conectando…', 'nexova-desk-chat' ),
                'connected'     => __( '¡Conectado!', 'nexova-desk-chat' ),
                'disconnecting' => __( 'Desconectando…', 'nexova-desk-chat' ),
                'saving'        => __( 'Guardando…', 'nexova-desk-chat' ),
                'saved'         => __( 'Guardado', 'nexova-desk-chat' ),
                'error'         => __( 'Error', 'nexova-desk-chat' ),
                'popupBlocked'  => __( 'El popup fue bloqueado. Permite popups para este sitio e intenta de nuevo.', 'nexova-desk-chat' ),
            ],
        ] );
    }

    // ── Render principal ──────────────────────────────────────────────────────

    public function render_page(): void {
        $active_tab  = sanitize_key( $_GET['tab'] ?? 'connection' );
        $is_connected = $this->api->is_connected();
        $org_data    = (array) get_option( NEXOVA_DESK_OPTION_ORG, [] );
        $cfg         = (array) get_option( NEXOVA_DESK_OPTION_CFG, [] );
        $server_url  = get_option( NEXOVA_DESK_OPTION_URL, '' );

        include NEXOVA_DESK_PATH . 'admin/views/page-main.php';
    }

    // ── AJAX: guardar conexión (viene del postMessage del popup) ──────────────

    public function ajax_save_connection(): void {
        check_ajax_referer( 'nexova_desk_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( -1 );

        $server_url = sanitize_url( $_POST['server_url'] ?? '' );
        $token      = sanitize_text_field( $_POST['token'] ?? '' );
        $org_name   = sanitize_text_field( $_POST['org_name'] ?? '' );
        $org_id     = absint( $_POST['org_id'] ?? 0 );
        $org_plan   = sanitize_key( $_POST['org_plan'] ?? 'free' );

        if ( empty( $server_url ) || empty( $token ) ) {
            wp_send_json_error( [ 'message' => __( 'Datos de conexión incompletos.', 'nexova-desk-chat' ) ] );
        }

        update_option( NEXOVA_DESK_OPTION_URL,   $server_url );
        update_option( NEXOVA_DESK_OPTION_TOKEN, $token );
        update_option( NEXOVA_DESK_OPTION_ORG,   [
            'name' => $org_name,
            'id'   => $org_id,
            'plan' => $org_plan,
        ] );

        wp_send_json_success( [
            'message'  => __( 'Conexión establecida correctamente.', 'nexova-desk-chat' ),
            'org_name' => $org_name,
        ] );
    }

    // ── AJAX: desconectar ─────────────────────────────────────────────────────

    public function ajax_disconnect(): void {
        check_ajax_referer( 'nexova_desk_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( -1 );

        delete_option( NEXOVA_DESK_OPTION_URL );
        delete_option( NEXOVA_DESK_OPTION_TOKEN );
        delete_option( NEXOVA_DESK_OPTION_ORG );
        // Mantenemos la config (widget elegido, visibilidad) por si reconecta

        wp_send_json_success( [ 'message' => __( 'Desconectado de Nexova Desk.', 'nexova-desk-chat' ) ] );
    }

    // ── AJAX: guardar configuración (widget + visibilidad) ────────────────────

    public function ajax_save_config(): void {
        check_ajax_referer( 'nexova_desk_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( -1 );

        $cfg = (array) get_option( NEXOVA_DESK_OPTION_CFG, [] );

        // Pestaña Widget
        if ( isset( $_POST['widget_token'] ) ) {
            $cfg['widget_token']  = sanitize_text_field( $_POST['widget_token'] );
            $cfg['widget_id']     = absint( $_POST['widget_id'] ?? 0 );
            $cfg['widget_name']   = sanitize_text_field( $_POST['widget_name'] ?? '' );
            // WooCommerce widget (may be different from the display widget)
            $cfg['woo_widget_id']      = absint( $_POST['woo_widget_id'] ?? 0 );
            $cfg['orders_enabled']     = ! empty( $_POST['orders_enabled'] );
            $cfg['woo_context_enabled'] = ! empty( $_POST['woo_context_enabled'] );
        }

        // Pestaña Visibilidad
        if ( isset( $_POST['visibility'] ) ) {
            $cfg['visibility']    = sanitize_key( $_POST['visibility'] );
            $cfg['show_page_ids'] = array_map( 'absint', explode( ',', $_POST['show_page_ids'] ?? '' ) );
            $cfg['hide_page_ids'] = array_map( 'absint', explode( ',', $_POST['hide_page_ids'] ?? '' ) );
        }

        update_option( NEXOVA_DESK_OPTION_CFG, $cfg );

        // Sincronizar WooCommerce al servidor: usa el woo_widget_id si está definido,
        // sino el widget de display.
        if ( isset( $_POST['widget_token'] ) ) {
            $woo_id = ! empty( $cfg['woo_widget_id'] ) ? (int) $cfg['woo_widget_id'] : (int) $cfg['widget_id'];
            if ( $woo_id ) {
                $this->api->sync_woo_toggles(
                    $woo_id,
                    (bool) $cfg['woo_context_enabled'],
                    (bool) $cfg['orders_enabled']
                );
            }
        }

        wp_send_json_success( [ 'message' => __( 'Configuración guardada.', 'nexova-desk-chat' ) ] );
    }

    // ── AJAX: refrescar lista de widgets ──────────────────────────────────────

    public function ajax_fetch_widgets(): void {
        check_ajax_referer( 'nexova_desk_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( -1 );

        $widgets = $this->api->get_widgets();

        if ( empty( $widgets ) ) {
            wp_send_json_error( [ 'message' => __( 'No se encontraron widgets o la conexión falló.', 'nexova-desk-chat' ) ] );
        }

        wp_send_json_success( [ 'widgets' => $widgets ] );
    }

    // ── AJAX: buscar páginas/posts (autocomplete visibilidad) ────────────

    public function ajax_search_pages(): void {
        check_ajax_referer( 'nexova_desk_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( -1 );

        $q = sanitize_text_field( $_POST['q'] ?? '' );
        if ( strlen( $q ) < 2 ) {
            wp_send_json_success( [ 'pages' => [] ] );
        }

        $posts = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            's'              => $q,
            'posts_per_page' => 10,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        $pages = array_map( fn( $p ) => [
            'id'    => $p->ID,
            'title' => $p->post_title,
        ], $posts );

        wp_send_json_success( [ 'pages' => $pages ] );
    }

    // ── Icon SVG en base64 ────────────────────────────────────────────────────

    private function get_menu_icon(): string {
        // Icono chat bubble — SVG inline en base64
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode( $svg );
    }
}
