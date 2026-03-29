<?php
/**
 * Plugin Name:  Nexova Desk Chat para WooCommerce
 * Plugin URI:   https://nexova.digital
 * Description:  Conecta tu tienda WooCommerce con Nexova Desk. Inyecta el widget de chat, identifica automáticamente a clientes logueados y permite consultar el estado de sus pedidos desde el chat.
 * Version:      1.0.0
 * Author:       Nexova Digital Solutions
 * Author URI:   https://nexova.digital
 * Text Domain:  nexova-desk-chat
 * Domain Path:  /languages
 * Requires WP:  6.0
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 * WC tested up to: 9.0
 *
 * Copyright (C) 2024-2026 Nexova Digital Solutions.
 * Todos los derechos reservados. Este plugin es software propietario
 * desarrollado y distribuido por Nexova Digital Solutions.
 * No está permitida su redistribución o modificación sin autorización expresa.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Constantes ────────────────────────────────────────────────────────────────
define( 'NEXOVA_DESK_VERSION',     '1.0.0' );
define( 'NEXOVA_DESK_PATH',        plugin_dir_path( __FILE__ ) );
define( 'NEXOVA_DESK_URL',         plugin_dir_url( __FILE__ ) );
define( 'NEXOVA_DESK_BASENAME',    plugin_basename( __FILE__ ) );
define( 'NEXOVA_DESK_OPTION_URL',  'nexova_desk_server_url' );
define( 'NEXOVA_DESK_OPTION_TOKEN','nexova_desk_plugin_token' );
define( 'NEXOVA_DESK_OPTION_ORG',  'nexova_desk_org_data' );
define( 'NEXOVA_DESK_OPTION_CFG',  'nexova_desk_config' );

/**
 * Clase principal — singleton.
 */
final class Nexova_Desk_Chat {

    private static ?Nexova_Desk_Chat $instance = null;

    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    // ── Dependencias ─────────────────────────────────────────────────────────

    private function load_dependencies(): void {
        require_once NEXOVA_DESK_PATH . 'includes/class-nexova-desk-api.php';
        require_once NEXOVA_DESK_PATH . 'includes/class-nexova-desk-frontend.php';

        if ( is_admin() ) {
            require_once NEXOVA_DESK_PATH . 'admin/class-nexova-desk-admin.php';
        }
    }

    // ── Hooks ─────────────────────────────────────────────────────────────────

    private function init_hooks(): void {
        add_action( 'init',          [ $this, 'load_textdomain' ] );
        add_action( 'admin_notices', [ $this, 'setup_notice' ] );

        // Compatibilidad HPOS WooCommerce
        add_action( 'before_woocommerce_init', function () {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                    'custom_order_tables', __FILE__, true
                );
            }
        } );

        // Enlace rápido en lista de plugins
        add_filter(
            'plugin_action_links_' . NEXOVA_DESK_BASENAME,
            [ $this, 'plugin_action_links' ]
        );

        // Frontend + Admin
        new Nexova_Desk_Frontend();

        if ( is_admin() ) {
            new Nexova_Desk_Admin();
        }
    }

    // ── Textdomain ────────────────────────────────────────────────────────────

    public function load_textdomain(): void {
        load_plugin_textdomain(
            'nexova-desk-chat',
            false,
            dirname( NEXOVA_DESK_BASENAME ) . '/languages/'
        );
    }

    // ── Aviso de configuración ────────────────────────────────────────────────

    public function setup_notice(): void {
        if ( get_option( NEXOVA_DESK_OPTION_TOKEN ) ) {
            return; // Ya está conectado
        }
        $screen = get_current_screen();
        if ( $screen && str_contains( $screen->id, 'nexova-desk' ) ) {
            return;
        }
        $url = admin_url( 'admin.php?page=nexova-desk-chat' );
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Nexova Desk Chat:</strong> ' .
            sprintf(
                __( 'El plugin no está conectado. <a href="%s">Conectar ahora &rarr;</a>', 'nexova-desk-chat' ),
                esc_url( $url )
            ) .
            '</p></div>';
    }

    // ── Enlace rápido ─────────────────────────────────────────────────────────

    public function plugin_action_links( array $links ): array {
        $url = admin_url( 'admin.php?page=nexova-desk-chat' );
        array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . __( 'Configurar', 'nexova-desk-chat' ) . '</a>' );
        return $links;
    }
}

// ── Activación / Desactivación ────────────────────────────────────────────────

register_activation_hook( __FILE__, function () {
    // Guardar versión para futuras migraciones
    add_option( 'nexova_desk_version', NEXOVA_DESK_VERSION );
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );

register_uninstall_hook( __FILE__, 'nexova_desk_uninstall' );

function nexova_desk_uninstall(): void {
    delete_option( NEXOVA_DESK_OPTION_URL );
    delete_option( NEXOVA_DESK_OPTION_TOKEN );
    delete_option( NEXOVA_DESK_OPTION_ORG );
    delete_option( NEXOVA_DESK_OPTION_CFG );
    delete_option( 'nexova_desk_version' );
}

// ── Inicializar después de cargar WooCommerce ─────────────────────────────────

add_action( 'plugins_loaded', function () {
    Nexova_Desk_Chat::get_instance();
} );
