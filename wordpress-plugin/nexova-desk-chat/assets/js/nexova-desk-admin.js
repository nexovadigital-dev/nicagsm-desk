/**
 * Nexova Desk Chat — Admin JS
 * Maneja: popup de conexión, postMessage, selección de widgets, visibilidad
 *
 * @package NexovaDeskChat
 */
/* global NexovaDeskAdmin, jQuery */
(function ($) {
    'use strict';

    var cfg = NexovaDeskAdmin;

    /* ── Helpers ─────────────────────────────────────────────────────────── */

    function setBtn(btn, label, disabled) {
        btn.text(label).prop('disabled', !!disabled);
    }

    function showNotice(el, msg, type) {
        el.removeClass('nexova-desk-notice--success nexova-desk-notice--error')
          .addClass('nexova-desk-notice--' + type)
          .text(msg)
          .show();
    }

    function showInline(el, msg, type) {
        el.removeClass('nexova-desk-inline-notice--success nexova-desk-inline-notice--error')
          .addClass('nexova-desk-inline-notice--' + type)
          .text(msg)
          .show();
        setTimeout(function () { el.hide(); }, 3000);
    }

    function ajax(action, data, done, fail) {
        $.post(cfg.ajaxUrl, $.extend({ action: action, nonce: cfg.nonce }, data))
            .done(function (res) {
                if (res.success) {
                    done(res.data);
                } else {
                    fail((res.data && res.data.message) || cfg.i18n.error);
                }
            })
            .fail(function () { fail(cfg.i18n.error); });
    }

    /* ── Tab: Conexión ───────────────────────────────────────────────────── */

    var $connectBtn    = $('#nexova-desk-connect-btn');
    var $disconnectBtn = $('#nexova-desk-disconnect-btn');
    var $serverInput   = $('#nexova-desk-server-url');
    var $connectNotice = $('#nexova-desk-connect-notice');
    var connectPopup   = null;

    // Abre popup de autenticación
    $connectBtn.on('click', function () {
        var serverUrl = $.trim($serverInput.val());
        if (!serverUrl) {
            showNotice($connectNotice, 'Ingresa la URL del servidor.', 'error');
            return;
        }

        // Normalizar URL
        serverUrl = serverUrl.replace(/\/+$/, '');

        var popupUrl  = serverUrl + '/connect?origin=' + encodeURIComponent(window.location.origin);
        var popupW    = 520;
        var popupH    = 620;
        var popupLeft = Math.round(window.screenX + (window.outerWidth - popupW) / 2);
        var popupTop  = Math.round(window.screenY + (window.outerHeight - popupH) / 2);

        connectPopup = window.open(
            popupUrl,
            'nexova_desk_connect',
            'width=' + popupW + ',height=' + popupH +
            ',left=' + popupLeft + ',top=' + popupTop +
            ',scrollbars=yes,resizable=yes'
        );

        if (!connectPopup || connectPopup.closed || typeof connectPopup.closed === 'undefined') {
            showNotice($connectNotice, cfg.i18n.popupBlocked, 'error');
            return;
        }

        setBtn($connectBtn, cfg.i18n.connecting, true);
        $connectNotice.hide();

        // Cerrar si el usuario cierra el popup sin completar
        var checkClosed = setInterval(function () {
            if (connectPopup && connectPopup.closed) {
                clearInterval(checkClosed);
                if ($connectBtn.is(':disabled')) {
                    setBtn($connectBtn, 'Conectar con Nexova Desk', false);
                }
            }
        }, 800);
    });

    // Recibe token por postMessage desde el popup
    window.addEventListener('message', function (event) {
        if (!event.data || event.data.source !== 'nexova_desk_connect') return;

        var data = event.data;
        if (data.token && data.server_url) {
            if (connectPopup) connectPopup.close();

            ajax(
                'nexova_desk_save_connection',
                {
                    server_url: data.server_url,
                    token:      data.token,
                    org_name:   data.org_name  || '',
                    org_id:     data.org_id    || 0,
                    org_plan:   data.org_plan  || 'free',
                },
                function () {
                    // Reload para mostrar estado conectado
                    window.location.reload();
                },
                function (msg) {
                    setBtn($connectBtn, 'Conectar con Nexova Desk', false);
                    showNotice($connectNotice, msg, 'error');
                }
            );
        }
    });

    // Desconectar
    $disconnectBtn.on('click', function () {
        if (!confirm('¿Deseas desconectar Nexova Desk de esta tienda?')) return;
        setBtn($disconnectBtn, cfg.i18n.disconnecting, true);

        ajax(
            'nexova_desk_disconnect',
            {},
            function () { window.location.reload(); },
            function (msg) {
                setBtn($disconnectBtn, 'Desconectar', false);
                alert(msg);
            }
        );
    });

    /* ── Tab: Widgets ────────────────────────────────────────────────────── */

    var $widgetsList   = $('#nexova-desk-widgets-list');
    var $refreshBtn    = $('#nexova-desk-refresh-widgets');
    var $tokenInput    = $('#nexova-desk-widget-token');
    var $widgetIdInput = $('#nexova-desk-widget-id');
    var $widgetNameIn  = $('#nexova-desk-widget-name');
    var $widgetNotice  = $('#nexova-desk-widgets-notice');
    var $saveWidgetBtn = $('#nexova-desk-save-widget');
    var $saveWidgetMsg = $('#nexova-desk-save-widget-notice');

    // Cargar/refrescar lista de widgets desde el servidor
    $refreshBtn.on('click', function () {
        setBtn($refreshBtn, 'Cargando…', true);
        $widgetNotice.hide();

        ajax(
            'nexova_desk_fetch_widgets',
            {},
            function (data) {
                setBtn($refreshBtn, 'Actualizar lista', false);
                renderWidgets(data.widgets || []);
            },
            function (msg) {
                setBtn($refreshBtn, 'Actualizar lista', false);
                showNotice($widgetNotice, msg, 'error');
            }
        );
    });

    function renderWidgets(widgets) {
        if (!widgets.length) {
            $widgetsList.html(
                '<p class="nexova-desk-empty-state">No se encontraron widgets en tu organización.</p>'
            );
            return;
        }

        var currentToken = $tokenInput.val();
        var html = '';

        widgets.forEach(function (w) {
            var isSelected = (w.token === currentToken);
            var statusBadge = w.is_active
                ? '<span class="nexova-desk-badge nexova-desk-badge--active">Activo</span>'
                : '<span class="nexova-desk-badge nexova-desk-badge--inactive">Inactivo</span>';

            html += '<div class="nexova-desk-widget-card' + (isSelected ? ' is-selected' : '') + '"' +
                    ' data-token="' + escAttr(w.token) + '"' +
                    ' data-id="' + escAttr(w.id) + '"' +
                    ' data-name="' + escAttr(w.name) + '">' +
                    '<div class="nexova-desk-widget-card__icon">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"' +
                    ' stroke="currentColor" stroke-width="1.8" width="20" height="20">' +
                    '<path stroke-linecap="round" stroke-linejoin="round"' +
                    ' d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>' +
                    '</svg></div>' +
                    '<div class="nexova-desk-widget-card__body">' +
                    '<strong>' + escHtml(w.name) + '</strong>' + statusBadge +
                    '</div>' +
                    '<div class="nexova-desk-widget-card__check">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"' +
                    ' stroke="currentColor" stroke-width="2.5" width="16" height="16">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>' +
                    '</svg></div></div>';
        });

        $widgetsList.html(html);
    }

    // Seleccionar widget
    $(document).on('click', '.nexova-desk-widget-card', function () {
        var $card = $(this);
        $('.nexova-desk-widget-card').removeClass('is-selected');
        $card.addClass('is-selected');
        $tokenInput.val($card.data('token'));
        $widgetIdInput.val($card.data('id'));
        $widgetNameIn.val($card.data('name'));
    });

    // Guardar widget + pedidos
    $saveWidgetBtn.on('click', function () {
        var token = $tokenInput.val();
        if (!token) {
            showInline($saveWidgetMsg, 'Selecciona un widget primero.', 'error');
            return;
        }

        setBtn($saveWidgetBtn, cfg.i18n.saving, true);

        ajax(
            'nexova_desk_save_config',
            {
                widget_token:   token,
                widget_id:      $widgetIdInput.val(),
                widget_name:    $widgetNameIn.val(),
                orders_enabled: $('#nexova-desk-orders-enabled').is(':checked') ? '1' : '',
            },
            function () {
                setBtn($saveWidgetBtn, 'Guardar configuración', false);
                showInline($saveWidgetMsg, cfg.i18n.saved + ' ✓', 'success');
            },
            function (msg) {
                setBtn($saveWidgetBtn, 'Guardar configuración', false);
                showInline($saveWidgetMsg, msg, 'error');
            }
        );
    });

    /* ── Tab: Visibilidad ────────────────────────────────────────────────── */

    // Radio — mostrar/ocultar sección "páginas específicas"
    $('input[name="nexova_visibility"]').on('change', function () {
        var val = $(this).val();
        $('.nexova-desk-radio').removeClass('is-selected');
        $(this).closest('.nexova-desk-radio').addClass('is-selected');

        if (val === 'selected') {
            $('#nexova-desk-show-pages-card').addClass('is-visible');
        } else {
            $('#nexova-desk-show-pages-card').removeClass('is-visible');
        }
    });

    // Búsqueda de páginas (autocomplete via WP AJAX)
    function initPageSearch(inputId, resultsId, chipsId, hiddenId) {
        var $input   = $('#' + inputId);
        var $results = $('#' + resultsId);
        var $chips   = $('#' + chipsId);
        var $hidden  = $('#' + hiddenId);
        var timer    = null;

        $input.on('input', function () {
            clearTimeout(timer);
            var q = $.trim($(this).val());
            if (q.length < 2) { $results.hide(); return; }

            timer = setTimeout(function () {
                $.post(cfg.ajaxUrl, {
                    action: 'nexova_desk_search_pages',
                    nonce:  cfg.nonce,
                    q:      q,
                }, function (res) {
                    $results.empty();
                    if (res.success && res.data.pages.length) {
                        res.data.pages.forEach(function (p) {
                            $('<div class="nexova-desk-page-result-item">')
                                .text(p.title)
                                .data('page', p)
                                .appendTo($results);
                        });
                    } else {
                        $('<div class="nexova-desk-page-result-item no-results">')
                            .text('Sin resultados')
                            .appendTo($results);
                    }
                    $results.show();
                });
            }, 250);
        });

        $(document).on('click', '#' + resultsId + ' .nexova-desk-page-result-item:not(.no-results)', function () {
            var p = $(this).data('page');
            addChip($chips, $hidden, p.id, p.title);
            $input.val('');
            $results.hide();
        });

        $(document).on('click', '#' + chipsId + ' .nexova-desk-chip__remove', function () {
            var $chip = $(this).closest('.nexova-desk-chip');
            var id    = String($chip.data('id'));
            $chip.remove();

            var ids = $hidden.val().split(',').filter(function (v) {
                return v !== '' && v !== id;
            });
            $hidden.val(ids.join(','));
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#' + inputId + ', #' + resultsId).length) {
                $results.hide();
            }
        });
    }

    function addChip($chips, $hidden, id, title) {
        var ids = $hidden.val().split(',').filter(Boolean);
        if (ids.indexOf(String(id)) !== -1) return; // ya existe

        ids.push(id);
        $hidden.val(ids.join(','));

        $chips.append(
            '<span class="nexova-desk-chip" data-id="' + escAttr(id) + '">' +
            escHtml(title) +
            '<button type="button" class="nexova-desk-chip__remove" aria-label="Eliminar">×</button>' +
            '</span>'
        );
    }

    initPageSearch(
        'nexova-desk-show-page-search',
        'nexova-desk-show-page-results',
        'nexova-desk-show-pages-list',
        'nexova-desk-show-page-ids'
    );

    initPageSearch(
        'nexova-desk-hide-page-search',
        'nexova-desk-hide-page-results',
        'nexova-desk-hide-pages-list',
        'nexova-desk-hide-page-ids'
    );

    // Guardar visibilidad
    $('#nexova-desk-save-visibility').on('click', function () {
        var $btn = $(this);
        setBtn($btn, cfg.i18n.saving, true);

        ajax(
            'nexova_desk_save_config',
            {
                visibility:    $('input[name="nexova_visibility"]:checked').val() || 'all',
                show_page_ids: $('#nexova-desk-show-page-ids').val(),
                hide_page_ids: $('#nexova-desk-hide-page-ids').val(),
            },
            function () {
                setBtn($btn, 'Guardar visibilidad', false);
                showInline($('#nexova-desk-save-visibility-notice'), cfg.i18n.saved + ' ✓', 'success');
            },
            function (msg) {
                setBtn($btn, 'Guardar visibilidad', false);
                showInline($('#nexova-desk-save-visibility-notice'), msg, 'error');
            }
        );
    });

    /* ── Escape utils ────────────────────────────────────────────────────── */

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escAttr(str) {
        return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

}(jQuery));
