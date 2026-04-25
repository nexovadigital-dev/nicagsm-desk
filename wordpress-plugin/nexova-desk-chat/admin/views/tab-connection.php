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

        <?php
        $plan       = $org_data['plan'] ?? 'free';
        $is_partner = ! empty( $org_data['is_partner'] );
        $is_edge    = ! empty( $org_data['is_edge'] );
        $is_active  = isset( $org_data['active'] ) ? (bool) $org_data['active'] : true;

        $plan_label = [
            'free'         => __( 'Gratuito', 'nexova-desk-chat' ),
            'trial'        => __( 'Prueba', 'nexova-desk-chat' ),
            'pro'          => 'Pro',
            'enterprise'   => 'Enterprise',
            'partner'      => 'Nexova Desk',
            'partner_edge' => 'Nexova Desk Edge',
        ][ $plan ] ?? ucfirst( $plan );

        $plan_colors = [
            'free'         => [ 'bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb' ],
            'trial'        => [ 'bg' => '#fef3c7', 'color' => '#92400e', 'border' => '#fde68a' ],
            'pro'          => [ 'bg' => '#dcfce7', 'color' => '#166534', 'border' => '#86efac' ],
            'enterprise'   => [ 'bg' => '#ede9fe', 'color' => '#5b21b6', 'border' => '#c4b5fd' ],
            'partner'      => [ 'bg' => '#dcfce7', 'color' => '#166534', 'border' => '#86efac' ],
            'partner_edge' => [ 'bg' => '#dcfce7', 'color' => '#14532d', 'border' => '#4ade80' ],
        ][ $plan ] ?? [ 'bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb' ];

        $is_premium = in_array( $plan, [ 'pro', 'enterprise', 'partner', 'partner_edge' ], true );
        ?>

        <div class="nexova-desk-connected-state">

            <!-- Icono central -->
            <div class="nexova-desk-connected-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" width="36" height="36">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h2><?php esc_html_e( 'Cuenta conectada', 'nexova-desk-chat' ); ?></h2>

            <!-- Pills: org + plan -->
            <div class="nexova-desk-connected-pills">
                <?php if ( ! empty( $org_data['name'] ) ) : ?>
                <div class="nexova-desk-org-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <?php echo esc_html( $org_data['name'] ); ?>
                </div>
                <?php endif; ?>

                <div class="nexova-desk-plan-badge"
                     style="background:<?php echo esc_attr( $plan_colors['bg'] ); ?>;color:<?php echo esc_attr( $plan_colors['color'] ); ?>;border-color:<?php echo esc_attr( $plan_colors['border'] ); ?>">
                    <?php if ( $is_premium ) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="12" height="12" style="flex-shrink:0">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <?php endif; ?>
                    <?php echo esc_html( sprintf( __( 'Plan: %s', 'nexova-desk-chat' ), $plan_label ) ); ?>
                </div>
            </div>

            <!-- Licencia status -->
            <div class="nexova-desk-license-status <?php echo $is_active ? 'nexova-desk-license-status--ok' : 'nexova-desk-license-status--error'; ?>">
                <?php if ( $is_active ) : ?>
                <div class="nexova-desk-license-status__dot"></div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><?php esc_html_e( 'Licencia activa — el widget está operativo', 'nexova-desk-chat' ); ?></span>
                <?php else : ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="flex-shrink:0">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><?php esc_html_e( 'Licencia desactivada. El widget no se mostrará. Contacta con Nexova.', 'nexova-desk-chat' ); ?></span>
                <?php endif; ?>
            </div>

            <p class="nexova-desk-description" style="margin-top:16px;margin-bottom:0">
                <?php esc_html_e( 'Tu tienda está vinculada a Nexova Desk. Configura el widget en la pestaña Widget y controla su visibilidad en la pestaña Visibilidad.', 'nexova-desk-chat' ); ?>
            </p>

            <div class="nexova-desk-actions" style="justify-content:center;margin-top:20px">
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

            <!-- Selector de tipo de cuenta -->
            <div id="nexova-desk-login-selector" style="margin:28px auto 0;max-width:420px">
                <p style="font-size:12px;font-weight:600;color:#374151;text-align:center;margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em">
                    <?php esc_html_e( '¿Con qué cuenta vas a conectar?', 'nexova-desk-chat' ); ?>
                </p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <!-- Nexova Desk estándar -->
                    <button type="button" id="nexova-desk-select-standard"
                            class="nexova-desk-login-type-btn nexova-desk-login-type-btn--active"
                            style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;border:1.5px solid #22c55e;border-radius:10px;background:#f0fdf4;cursor:pointer;transition:all .15s">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.8" width="22" height="22">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <span style="font-size:12.5px;font-weight:600;color:#166534;line-height:1.2;text-align:center">Nexova Desk</span>
                        <span style="font-size:10.5px;color:#6b7280;text-align:center;line-height:1.3"><?php esc_html_e( 'Cuenta estándar', 'nexova-desk-chat' ); ?></span>
                    </button>

                    <!-- Nexova Desk Edge (Partner) -->
                    <button type="button" id="nexova-desk-select-edge"
                            class="nexova-desk-login-type-btn"
                            style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;cursor:pointer;transition:all .15s">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.8" width="22" height="22">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span style="font-size:12.5px;font-weight:600;color:#374151;line-height:1.2;text-align:center">Nexova Desk Edge</span>
                        <span style="font-size:10.5px;color:#6b7280;text-align:center;line-height:1.3"><?php esc_html_e( 'Cuenta Partner', 'nexova-desk-chat' ); ?></span>
                    </button>
                </div>
            </div>

            <!-- Panel: login estándar -->
            <div id="nexova-desk-panel-standard" style="margin-top:20px;text-align:center">
                <div class="nexova-desk-actions" style="justify-content:center">
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
                <!-- hidden: server URL siempre apunta al estándar -->
                <input type="hidden" id="nexova-desk-server-url" value="https://nexovadesk.com">
            </div>

            <!-- Panel: login Edge -->
            <div id="nexova-desk-panel-edge" style="margin-top:20px;display:none;max-width:420px;margin-left:auto;margin-right:auto">
                <!-- Paso 1: email -->
                <div id="nexova-desk-edge-step-email">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                        <?php esc_html_e( 'Correo electrónico de tu cuenta Partner', 'nexova-desk-chat' ); ?>
                    </label>
                    <div style="display:flex;gap:8px">
                        <input type="email" id="nexova-desk-edge-email"
                               placeholder="info@tuempresa.com"
                               style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;outline:none;box-sizing:border-box">
                        <button type="button" id="nexova-desk-edge-find-btn" class="nexova-desk-btn nexova-desk-btn--primary" style="white-space:nowrap;flex-shrink:0">
                            <?php esc_html_e( 'Buscar', 'nexova-desk-chat' ); ?>
                        </button>
                    </div>
                    <p style="font-size:11.5px;color:#6b7280;margin:5px 0 0">
                        <?php esc_html_e( 'Introduce el email con el que te registraste como Partner en Nexova.', 'nexova-desk-chat' ); ?>
                    </p>
                    <div id="nexova-desk-edge-find-notice" style="display:none;margin-top:10px"></div>
                </div>

                <!-- Paso 2: email enviado — esperando autorización -->
                <div id="nexova-desk-edge-step-confirm" style="display:none;margin-top:16px;padding:16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;text-align:center">
                    <div style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#dcfce7;border-radius:50%;margin-bottom:10px">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" width="22" height="22">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p style="margin:0 0 4px;font-size:13px;font-weight:600;color:#166534" id="nexova-desk-edge-org-name"><?php esc_html_e( 'Email enviado', 'nexova-desk-chat' ); ?></p>
                    <p style="margin:0 0 12px;font-size:12px;color:#6b7280" id="nexova-desk-edge-org-domain"><?php esc_html_e( 'Revisa tu bandeja de entrada y haz clic en el enlace para autorizar.', 'nexova-desk-chat' ); ?></p>
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:#6b7280">
                        <span style="display:inline-block;width:10px;height:10px;border:2px solid #d1d5db;border-top-color:#22c55e;border-radius:50%;animation:spin .7s linear infinite"></span>
                        <?php esc_html_e( 'Esperando autorización…', 'nexova-desk-chat' ); ?>
                    </div>
                    <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
                    <button type="button" id="nexova-desk-edge-connect-btn" style="display:none"></button>
                    <button type="button" id="nexova-desk-edge-back-btn" style="margin-top:12px;background:none;border:none;cursor:pointer;font-size:12px;color:#6b7280;padding:4px">
                        <?php esc_html_e( '← Usar otro email', 'nexova-desk-chat' ); ?>
                    </button>
                </div>
            </div>

            <div id="nexova-desk-connect-notice" class="nexova-desk-notice" style="display:none;margin-top:16px"></div>

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
