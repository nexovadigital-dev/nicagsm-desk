<?php
/**
 * Pestaña Widget — selección del widget a inyectar
 *
 * @package NexovaDeskChat
 * @var bool  $is_connected
 * @var array $cfg
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! $is_connected ) {
    echo '<div class="nexova-desk-card"><p>' . esc_html__( 'Conecta tu tienda primero en la pestaña Conexión.', 'nexova-desk-chat' ) . '</p></div>';
    return;
}

$selected_token = $cfg['widget_token'] ?? '';
$selected_id    = $cfg['widget_id'] ?? 0;
$selected_name  = $cfg['widget_name'] ?? '';
$orders_enabled = $cfg['orders_enabled'] ?? false;

$org_data   = (array) get_option( NEXOVA_DESK_OPTION_ORG, [] );
$server_url = rtrim( get_option( NEXOVA_DESK_OPTION_URL, '' ), '/' );
$plan       = $org_data['plan'] ?? 'free';
$plan_label = [
    'free'         => __( 'Gratuito', 'nexova-desk-chat' ),
    'trial'        => __( 'Prueba', 'nexova-desk-chat' ),
    'pro'          => 'NexovaDesk PRO',
    'enterprise'   => 'Enterprise',
    'partner'      => 'NexovaDesk Edge',
    'partner_edge' => 'NexovaDesk Edge',
][ $plan ] ?? ucfirst( $plan );
$plan_colors = [
    'free'         => [ 'bg' => '#f3f4f6', 'color' => '#6b7280' ],
    'trial'        => [ 'bg' => '#fef3c7', 'color' => '#92400e' ],
    'pro'          => [ 'bg' => '#dcfce7', 'color' => '#166534' ],
    'enterprise'   => [ 'bg' => '#ede9fe', 'color' => '#5b21b6' ],
    'partner'      => [ 'bg' => '#fef3c7', 'color' => '#92400e' ],
    'partner_edge' => [ 'bg' => '#fef3c7', 'color' => '#92400e' ],
][ $plan ] ?? [ 'bg' => '#f3f4f6', 'color' => '#6b7280' ];

$edit_widget_url = $selected_id && $server_url
    ? $server_url . '/app/chat-widgets/' . intval( $selected_id ) . '/edit'
    : $server_url . '/app/chat-widgets';
?>

<div class="nexova-desk-card">
    <div class="nexova-desk-plan-strip">
        <span class="nexova-desk-plan-badge"
              style="background:<?php echo esc_attr( $plan_colors['bg'] ); ?>;color:<?php echo esc_attr( $plan_colors['color'] ); ?>">
            <?php if ( $plan === 'pro' || $plan === 'enterprise' || $plan === 'partner' || $plan === 'partner_edge' ) : ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="12" height="12">
                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            <?php endif; ?>
            <?php echo esc_html( sprintf( __( 'Plan: %s', 'nexova-desk-chat' ), $plan_label ) ); ?>
        </span>
        <?php if ( $selected_id && $server_url ) : ?>
        <a href="<?php echo esc_url( $edit_widget_url ); ?>" target="_blank" rel="noopener noreferrer"
           class="nexova-desk-btn nexova-desk-btn--secondary nexova-desk-btn--sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" width="13" height="13">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <?php esc_html_e( 'Editar widget en Nexova Desk', 'nexova-desk-chat' ); ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="nexova-desk-card">
    <div class="nexova-desk-card__header">
        <h2><?php esc_html_e( 'Seleccionar widget', 'nexova-desk-chat' ); ?></h2>
        <button id="nexova-desk-refresh-widgets" class="nexova-desk-btn nexova-desk-btn--secondary nexova-desk-btn--sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <?php esc_html_e( 'Actualizar lista', 'nexova-desk-chat' ); ?>
        </button>
    </div>

    <p class="nexova-desk-description">
        <?php esc_html_e( 'Elige el widget de Nexova Desk que deseas mostrar en tu tienda.', 'nexova-desk-chat' ); ?>
    </p>

    <div id="nexova-desk-widgets-list" class="nexova-desk-widgets-grid">
        <?php if ( $selected_token ) : ?>
        <div class="nexova-desk-widget-card is-selected"
             data-token="<?php echo esc_attr( $selected_token ); ?>"
             data-id="<?php echo esc_attr( $selected_id ); ?>"
             data-name="<?php echo esc_attr( $selected_name ); ?>">
            <div class="nexova-desk-widget-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div class="nexova-desk-widget-card__body">
                <strong><?php echo esc_html( $selected_name ?: __( 'Widget seleccionado', 'nexova-desk-chat' ) ); ?></strong>
                <span class="nexova-desk-badge nexova-desk-badge--active">
                    <?php esc_html_e( 'Activo', 'nexova-desk-chat' ); ?>
                </span>
            </div>
            <div class="nexova-desk-widget-card__check">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
        <?php else : ?>
        <p class="nexova-desk-empty-state">
            <?php esc_html_e( 'Haz clic en "Actualizar lista" para cargar los widgets disponibles.', 'nexova-desk-chat' ); ?>
        </p>
        <?php endif; ?>
    </div>

    <input type="hidden" id="nexova-desk-widget-token"   value="<?php echo esc_attr( $selected_token ); ?>" />
    <input type="hidden" id="nexova-desk-widget-id"     value="<?php echo esc_attr( $selected_id ); ?>" />
    <input type="hidden" id="nexova-desk-widget-name"   value="<?php echo esc_attr( $selected_name ); ?>" />
    <input type="hidden" id="nexova-desk-woo-widget-id" value="<?php echo esc_attr( $cfg['woo_widget_id'] ?? $selected_id ); ?>" />
    <p style="font-size:12px;color:#6b7280;margin-top:10px;line-height:1.5">
        <?php esc_html_e( 'Haz clic en el logo WooCommerce de un widget para asignarle la integración con tu tienda.', 'nexova-desk-chat' ); ?>
    </p>

    <div id="nexova-desk-widgets-notice" class="nexova-desk-notice" style="display:none;"></div>
</div>

<div class="nexova-desk-card">
    <h2><?php esc_html_e( 'Integración WooCommerce', 'nexova-desk-chat' ); ?></h2>
    <p class="nexova-desk-description">
        <?php esc_html_e( 'La tienda está conectada. El plugin envía automáticamente el catálogo y los pedidos al widget cuando WooCommerce está activo.', 'nexova-desk-chat' ); ?>
    </p>
    <p class="description" style="background:#f0fdf4;color:#166534;padding:10px 14px;border-radius:8px;border:1px solid #bbf7d0;margin-top:8px">
        ✓ <?php esc_html_e( 'Para activar o desactivar la IA de WooCommerce, ve a tu panel Nexova Desk → Mis Widgets → Editar widget → Integración WooCommerce.', 'nexova-desk-chat' ); ?>
    </p>
</div>


<div class="nexova-desk-actions nexova-desk-actions--sticky">
    <button id="nexova-desk-save-widget" class="nexova-desk-btn nexova-desk-btn--primary">
        <?php esc_html_e( 'Guardar configuración', 'nexova-desk-chat' ); ?>
    </button>
    <span id="nexova-desk-save-widget-notice" class="nexova-desk-inline-notice" style="display:none;"></span>
</div>
