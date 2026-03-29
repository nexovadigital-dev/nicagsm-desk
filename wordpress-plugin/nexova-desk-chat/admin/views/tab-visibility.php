<?php
/**
 * Pestaña Visibilidad — control de dónde aparece el widget
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

$visibility   = $cfg['visibility'] ?? 'all';
$show_ids_raw = implode( ',', array_filter( (array) ( $cfg['show_page_ids'] ?? [] ) ) );
$hide_ids_raw = implode( ',', array_filter( (array) ( $cfg['hide_page_ids'] ?? [] ) ) );

// Obtener nombres legibles de las páginas guardadas
function nexova_desk_get_page_names( array $ids ): array {
    $pages = [];
    foreach ( array_filter( $ids ) as $id ) {
        $title = get_the_title( $id );
        if ( $title ) $pages[] = [ 'id' => $id, 'title' => $title ];
    }
    return $pages;
}

$show_pages = nexova_desk_get_page_names( array_map( 'absint', $show_ids_raw ? explode( ',', $show_ids_raw ) : [] ) );
$hide_pages = nexova_desk_get_page_names( array_map( 'absint', $hide_ids_raw ? explode( ',', $hide_ids_raw ) : [] ) );
?>

<div class="nexova-desk-card">
    <h2><?php esc_html_e( 'Visibilidad del widget', 'nexova-desk-chat' ); ?></h2>
    <p class="nexova-desk-description">
        <?php esc_html_e( 'Controla en qué páginas aparece el chat.', 'nexova-desk-chat' ); ?>
    </p>

    <div class="nexova-desk-radio-group">

        <label class="nexova-desk-radio <?php echo $visibility === 'all' ? 'is-selected' : ''; ?>">
            <input type="radio" name="nexova_visibility" value="all"
                   <?php checked( $visibility, 'all' ); ?> />
            <div class="nexova-desk-radio__body">
                <strong><?php esc_html_e( 'Todo el sitio', 'nexova-desk-chat' ); ?></strong>
                <span><?php esc_html_e( 'El widget aparece en todas las páginas.', 'nexova-desk-chat' ); ?></span>
            </div>
        </label>

        <label class="nexova-desk-radio <?php echo $visibility === 'shop_only' ? 'is-selected' : ''; ?>">
            <input type="radio" name="nexova_visibility" value="shop_only"
                   <?php checked( $visibility, 'shop_only' ); ?> />
            <div class="nexova-desk-radio__body">
                <strong><?php esc_html_e( 'Solo tienda', 'nexova-desk-chat' ); ?></strong>
                <span><?php esc_html_e( 'Tienda, carrito, checkout y Mi cuenta (requiere WooCommerce).', 'nexova-desk-chat' ); ?></span>
            </div>
        </label>

        <label class="nexova-desk-radio <?php echo $visibility === 'selected' ? 'is-selected' : ''; ?>">
            <input type="radio" name="nexova_visibility" value="selected"
                   <?php checked( $visibility, 'selected' ); ?> />
            <div class="nexova-desk-radio__body">
                <strong><?php esc_html_e( 'Páginas específicas', 'nexova-desk-chat' ); ?></strong>
                <span><?php esc_html_e( 'Solo en las páginas que indiques a continuación.', 'nexova-desk-chat' ); ?></span>
            </div>
        </label>

    </div>
</div>

<div class="nexova-desk-card nexova-desk-page-selector <?php echo $visibility === 'selected' ? 'is-visible' : ''; ?>"
     id="nexova-desk-show-pages-card">
    <h3><?php esc_html_e( 'Mostrar solo en estas páginas', 'nexova-desk-chat' ); ?></h3>

    <div id="nexova-desk-show-pages-list" class="nexova-desk-page-chips">
        <?php foreach ( $show_pages as $page ) : ?>
        <span class="nexova-desk-chip" data-id="<?php echo esc_attr( $page['id'] ); ?>">
            <?php echo esc_html( $page['title'] ); ?>
            <button type="button" class="nexova-desk-chip__remove" aria-label="<?php esc_attr_e( 'Eliminar', 'nexova-desk-chat' ); ?>">×</button>
        </span>
        <?php endforeach; ?>
    </div>

    <div class="nexova-desk-page-search">
        <input type="text" id="nexova-desk-show-page-search"
               class="regular-text"
               placeholder="<?php esc_attr_e( 'Buscar página…', 'nexova-desk-chat' ); ?>" />
        <div id="nexova-desk-show-page-results" class="nexova-desk-page-results" style="display:none;"></div>
    </div>

    <input type="hidden" id="nexova-desk-show-page-ids" value="<?php echo esc_attr( $show_ids_raw ); ?>" />
</div>

<div class="nexova-desk-card">
    <h3><?php esc_html_e( 'Ocultar en estas páginas', 'nexova-desk-chat' ); ?></h3>
    <p class="nexova-desk-description">
        <?php esc_html_e( 'El widget no aparecerá en las páginas indicadas, independientemente de la configuración de visibilidad.', 'nexova-desk-chat' ); ?>
    </p>

    <div id="nexova-desk-hide-pages-list" class="nexova-desk-page-chips">
        <?php foreach ( $hide_pages as $page ) : ?>
        <span class="nexova-desk-chip" data-id="<?php echo esc_attr( $page['id'] ); ?>">
            <?php echo esc_html( $page['title'] ); ?>
            <button type="button" class="nexova-desk-chip__remove" aria-label="<?php esc_attr_e( 'Eliminar', 'nexova-desk-chat' ); ?>">×</button>
        </span>
        <?php endforeach; ?>
    </div>

    <div class="nexova-desk-page-search">
        <input type="text" id="nexova-desk-hide-page-search"
               class="regular-text"
               placeholder="<?php esc_attr_e( 'Buscar página…', 'nexova-desk-chat' ); ?>" />
        <div id="nexova-desk-hide-page-results" class="nexova-desk-page-results" style="display:none;"></div>
    </div>

    <input type="hidden" id="nexova-desk-hide-page-ids" value="<?php echo esc_attr( $hide_ids_raw ); ?>" />
</div>

<div class="nexova-desk-actions nexova-desk-actions--sticky">
    <button id="nexova-desk-save-visibility" class="nexova-desk-btn nexova-desk-btn--primary">
        <?php esc_html_e( 'Guardar visibilidad', 'nexova-desk-chat' ); ?>
    </button>
    <span id="nexova-desk-save-visibility-notice" class="nexova-desk-inline-notice" style="display:none;"></span>
</div>
