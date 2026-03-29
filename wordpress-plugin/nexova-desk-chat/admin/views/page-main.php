<?php
/**
 * Vista principal del panel de administración — Nexova Desk Chat
 * Variables disponibles: $active_tab, $is_connected, $org_data, $cfg, $server_url
 *
 * @package NexovaDeskChat
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$logo_path = NEXOVA_DESK_PATH . 'assets/images/logo-banner.png';
$logo_url  = NEXOVA_DESK_URL  . 'assets/images/logo-banner.png';
?>
<div class="wrap nexova-desk-wrap">

    <div class="nexova-desk-header">
        <div class="nexova-desk-header__brand">
            <?php if ( file_exists( $logo_path ) ) : ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="Nexova Desk" class="nexova-desk-logo-img">
            <?php else : ?>
                <span class="nexova-desk-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </span>
            <?php endif; ?>
            <h1>Nexova <span style="color:#17c671">Desk</span></h1>
        </div>

        <?php if ( $is_connected ) : ?>
            <div class="nexova-desk-status nexova-desk-status--connected">
                <span class="nexova-desk-status__dot"></span>
                <?php
                $org_name = $org_data['name'] ?? '';
                echo $org_name
                    ? esc_html( sprintf( __( 'Conectado · %s', 'nexova-desk-chat' ), $org_name ) )
                    : esc_html__( 'Conectado', 'nexova-desk-chat' );
                ?>
            </div>
        <?php else : ?>
            <div class="nexova-desk-status nexova-desk-status--disconnected">
                <span class="nexova-desk-status__dot"></span>
                <?php esc_html_e( 'No conectado', 'nexova-desk-chat' ); ?>
            </div>
        <?php endif; ?>
    </div>

    <nav class="nexova-desk-tabs">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=nexova-desk-chat&tab=connection' ) ); ?>"
           class="nexova-desk-tab <?php echo $active_tab === 'connection' ? 'is-active' : ''; ?>">
            <?php esc_html_e( 'Conexión', 'nexova-desk-chat' ); ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=nexova-desk-chat&tab=widgets' ) ); ?>"
           class="nexova-desk-tab <?php echo $active_tab === 'widgets' ? 'is-active' : ''; ?> <?php echo ! $is_connected ? 'is-disabled' : ''; ?>">
            <?php esc_html_e( 'Widget', 'nexova-desk-chat' ); ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=nexova-desk-chat&tab=visibility' ) ); ?>"
           class="nexova-desk-tab <?php echo $active_tab === 'visibility' ? 'is-active' : ''; ?> <?php echo ! $is_connected ? 'is-disabled' : ''; ?>">
            <?php esc_html_e( 'Visibilidad', 'nexova-desk-chat' ); ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=nexova-desk-chat&tab=support' ) ); ?>"
           class="nexova-desk-tab <?php echo $active_tab === 'support' ? 'is-active' : ''; ?>">
            <?php esc_html_e( 'Soporte', 'nexova-desk-chat' ); ?>
        </a>
    </nav>

    <div class="nexova-desk-tab-content">
        <?php
        switch ( $active_tab ) {
            case 'widgets':
                include NEXOVA_DESK_PATH . 'admin/views/tab-widgets.php';
                break;
            case 'visibility':
                include NEXOVA_DESK_PATH . 'admin/views/tab-visibility.php';
                break;
            case 'support':
                include NEXOVA_DESK_PATH . 'admin/views/tab-support.php';
                break;
            default:
                include NEXOVA_DESK_PATH . 'admin/views/tab-connection.php';
        }
        ?>
    </div>

</div>
