<?php
/**
 * Pestaña Conexión — estado de conexión con Nexova Desk
 *
 * @package NexovaDeskChat
 * @var bool   $is_connected
 * @var array  $org_data
 * @var string $server_url
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="nexova-desk-card">

    <?php if ( $is_connected ) : ?>

        <div class="nexova-desk-connected-state">
            <div class="nexova-desk-connected-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" width="40" height="40">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h2><?php esc_html_e( 'Cuenta conectada', 'nexova-desk-chat' ); ?></h2>

            <?php
            $plan       = $org_data['plan'] ?? 'free';
            $plan_label = [
                'free'       => __( 'Gratuito', 'nexova-desk-chat' ),
                'trial'      => __( 'Prueba', 'nexova-desk-chat' ),
                'pro'        => 'Pro',
                'enterprise' => 'Enterprise',
            ][ $plan ] ?? ucfirst( $plan );
            $plan_colors = [
                'free'       => [ 'bg' => '#f3f4f6', 'color' => '#6b7280' ],
                'trial'      => [ 'bg' => '#fef3c7', 'color' => '#92400e' ],
                'pro'        => [ 'bg' => '#dcfce7', 'color' => '#166534' ],
                'enterprise' => [ 'bg' => '#ede9fe', 'color' => '#5b21b6' ],
            ][ $plan ] ?? [ 'bg' => '#f3f4f6', 'color' => '#6b7280' ];
            ?>

            <?php if ( ! empty( $org_data['name'] ) ) : ?>
            <div class="nexova-desk-org-pill">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <?php echo esc_html( $org_data['name'] ); ?>
            </div>
            <?php endif; ?>

            <div class="nexova-desk-plan-badge"
                 style="background:<?php echo esc_attr( $plan_colors['bg'] ); ?>;color:<?php echo esc_attr( $plan_colors['color'] ); ?>">
                <?php
                if ( $plan === 'pro' || $plan === 'enterprise' ) {
                    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="13" height="13" style="flex-shrink:0"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>';
                }
                echo esc_html( sprintf( __( 'Plan: %s', 'nexova-desk-chat' ), $plan_label ) );
                ?>
            </div>

            <p class="nexova-desk-description" style="margin-top:14px">
                <?php esc_html_e( 'Tu tienda está vinculada a Nexova Desk. Configura el widget en la pestaña Widget y controla su visibilidad en la pestaña Visibilidad.', 'nexova-desk-chat' ); ?>
            </p>

            <div class="nexova-desk-actions" style="justify-content:center">
                <button id="nexova-desk-disconnect-btn" class="nexova-desk-btn nexova-desk-btn--danger">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <?php esc_html_e( 'Desconectar cuenta', 'nexova-desk-chat' ); ?>
                </button>
            </div>
        </div>

    <?php else : ?>

        <div class="nexova-desk-connect-state">

            <div class="nexova-desk-connect-hero">
                <div class="nexova-desk-connect-hero__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" width="32" height="32">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <h2><?php esc_html_e( 'Conecta tu tienda con Nexova Desk', 'nexova-desk-chat' ); ?></h2>
                <p class="nexova-desk-description">
                    <?php esc_html_e( 'Vincula tu cuenta de Nexova Desk para activar el chat inteligente en tu tienda WooCommerce. El proceso es seguro y solo toma un momento.', 'nexova-desk-chat' ); ?>
                </p>
            </div>

            <div class="nexova-desk-connect-features">
                <div class="nexova-desk-connect-feature">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?php esc_html_e( 'Chat con IA entrenada en tu catálogo WooCommerce', 'nexova-desk-chat' ); ?>
                </div>
                <div class="nexova-desk-connect-feature">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?php esc_html_e( 'Identificación automática de clientes con sesión activa', 'nexova-desk-chat' ); ?>
                </div>
                <div class="nexova-desk-connect-feature">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?php esc_html_e( 'Control total de visibilidad por página o producto', 'nexova-desk-chat' ); ?>
                </div>
            </div>

            <div class="nexova-desk-actions" style="justify-content:center;margin-top:28px">
                <button id="nexova-desk-connect-btn" class="nexova-desk-btn nexova-desk-btn--primary nexova-desk-btn--lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <?php esc_html_e( 'Iniciar sesión en Nexova Desk', 'nexova-desk-chat' ); ?>
                </button>
            </div>

            <p class="nexova-desk-connect-hint">
                <?php esc_html_e( 'Se abrirá una ventana segura para autorizar la conexión.', 'nexova-desk-chat' ); ?>
            </p>

            <div id="nexova-desk-connect-notice" class="nexova-desk-notice" style="display:none;"></div>

            <input type="hidden" id="nexova-desk-server-url" value="<?php echo esc_attr( $server_url ?: 'http://localhost:8000' ); ?>">
        </div>

    <?php endif; ?>

</div>

<?php if ( $is_connected ) : ?>
<div class="nexova-desk-card nexova-desk-card--muted">
    <h3><?php esc_html_e( '¿Qué hace este plugin?', 'nexova-desk-chat' ); ?></h3>
    <ul class="nexova-desk-feature-list">
        <li><?php esc_html_e( 'Inyecta el widget de chat de Nexova Desk en tu tienda', 'nexova-desk-chat' ); ?></li>
        <li><?php esc_html_e( 'Identifica automáticamente a clientes con sesión iniciada', 'nexova-desk-chat' ); ?></li>
        <li><?php esc_html_e( 'Envía el contexto de tu tienda (productos, categorías) a la IA', 'nexova-desk-chat' ); ?></li>
        <li><?php esc_html_e( 'Control total sobre en qué páginas aparece el widget', 'nexova-desk-chat' ); ?></li>
    </ul>
</div>
<?php endif; ?>
