<?php
/**
 * Pestaña Soporte — información, licenciamiento y contacto
 *
 * @package NexovaDeskChat
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="nexova-desk-card">
    <div class="nexova-desk-card__header">
        <h2><?php esc_html_e( 'Soporte técnico', 'nexova-desk-chat' ); ?></h2>
        <span class="nexova-desk-badge nexova-desk-badge--active">v<?php echo esc_html( NEXOVA_DESK_VERSION ); ?></span>
    </div>

    <p class="nexova-desk-description">
        <?php esc_html_e( '¿Tienes preguntas o necesitas ayuda? Contáctanos directamente por WhatsApp o visita nuestro sitio.', 'nexova-desk-chat' ); ?>
    </p>

    <div class="nexova-desk-support-buttons">
        <a href="https://wa.me/message/GXMDON7MEALCG1"
           target="_blank" rel="noopener noreferrer"
           class="nexova-desk-btn nexova-desk-btn--whatsapp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <?php esc_html_e( 'Contactar por WhatsApp', 'nexova-desk-chat' ); ?>
        </a>
        <a href="mailto:info@nexovadesk.com"
           class="nexova-desk-btn nexova-desk-btn--secondary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            info@nexovadesk.com
        </a>
        <a href="https://nexovadesk.com"
           target="_blank" rel="noopener noreferrer"
           class="nexova-desk-btn nexova-desk-btn--secondary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            nexovadesk.com
        </a>
    </div>
</div>

<div class="nexova-desk-card nexova-desk-card--muted">
    <h3 style="margin-bottom:12px"><?php esc_html_e( 'Licenciamiento y uso', 'nexova-desk-chat' ); ?></h3>
    <div class="nexova-desk-disclaimer">
        <p>
            <?php esc_html_e( 'Este software es propiedad intelectual de Nexova Digital Solutions y se distribuye bajo un acuerdo de licencia comercial. Su instalación y uso están sujetos a la posesión de una cuenta activa en Nexova Desk.', 'nexova-desk-chat' ); ?>
        </p>
        <p>
            <?php esc_html_e( 'Queda estrictamente prohibido: modificar el código fuente con el fin de eludir las medidas de autenticación o licenciamiento, redistribuir el plugin sin autorización expresa, realizar ingeniería inversa, o utilizar el software en más sitios de los autorizados por tu plan. El incumplimiento de estos términos resultará en la revocación inmediata del acceso y podrá dar lugar a acciones legales.', 'nexova-desk-chat' ); ?>
        </p>
        <p style="margin-bottom:0; color:#9ca3af; font-size:12px">
            &copy; <?php echo date('Y'); ?> Nexova Digital Solutions &mdash; <?php esc_html_e( 'Todos los derechos reservados.', 'nexova-desk-chat' ); ?>
        </p>
    </div>
</div>
