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

    var $connectBtn      = $('#nexova-desk-connect-btn');
    var $disconnectBtn   = $('#nexova-desk-disconnect-btn');
    var $serverInput     = $('#nexova-desk-server-url');
    var $connectNotice   = $('#nexova-desk-connect-notice');
    var connectPopup     = null;
    var edgePartnerDomain = null;

    // ── Selector de tipo (Estándar / Edge) ──
    $('#nexova-desk-select-standard').on('click', function () {
        $(this).css({ border: '1.5px solid #22c55e', background: '#f0fdf4' })
               .find('span:first').css('color', '#166534');
        $(this).find('svg').attr('stroke', '#22c55e');
        $('#nexova-desk-select-edge').css({ border: '1.5px solid #e5e7eb', background: '#fff' })
               .find('span:first').css('color', '#374151');
        $('#nexova-desk-select-edge').find('svg').attr('stroke', '#6b7280');
        $('#nexova-desk-panel-standard').show();
        $('#nexova-desk-panel-edge').hide();
        $connectNotice.hide();
    });

    $('#nexova-desk-select-edge').on('click', function () {
        $(this).css({ border: '1.5px solid #22c55e', background: '#f0fdf4' })
               .find('span:first').css('color', '#166534');
        $(this).find('svg').attr('stroke', '#22c55e');
        $('#nexova-desk-select-standard').css({ border: '1.5px solid #e5e7eb', background: '#fff' })
               .find('span:first').css('color', '#374151');
        $('#nexova-desk-select-standard').find('svg').attr('stroke', '#6b7280');
        $('#nexova-desk-panel-edge').show();
        $('#nexova-desk-panel-standard').hide();
        $connectNotice.hide();
    });

    // ── Flujo Edge: buscar por email y enviar enlace ──
    var edgePollInterval = null;
    var edgeRequestId    = null;

    function stopEdgePoll() {
        if (edgePollInterval) { clearInterval(edgePollInterval); edgePollInterval = null; }
    }

    $('#nexova-desk-edge-find-btn').on('click', function () {
        var email   = $.trim($('#nexova-desk-edge-email').val());
        var $notice = $('#nexova-desk-edge-find-notice');
        var $btn    = $(this);

        if (!email) {
            showNotice($notice, 'Ingresa tu correo electrónico.', 'error');
            $notice.show();
            return;
        }

        setBtn($btn, 'Enviando…', true);
        $notice.hide();
        $('#nexova-desk-edge-step-confirm').hide();
        stopEdgePoll();
        edgeRequestId    = null;
        edgePartnerDomain = null;

        $.ajax({
            url: 'https://nexovadesk.com/api/partner/request-wp-connect',
            method: 'POST',
            data: { email: email },
            dataType: 'json',
        })
        .done(function (res) {
            setBtn($btn, 'Buscar', false);
            if (res.sent && res.request_id) {
                edgeRequestId = res.request_id;
                // Mostrar panel "revisa tu email"
                $('#nexova-desk-edge-step-confirm').show();
                $('#nexova-desk-edge-org-name').text('');
                $('#nexova-desk-edge-org-domain').text('Revisa tu bandeja de entrada y haz clic en el enlace del email.');
                $('#nexova-desk-edge-connect-btn').hide();
                $('#nexova-desk-edge-back-btn').show();
                // Cambiar ícono a email
                showNotice($notice, '¡Email enviado! Tienes 15 minutos para autorizar desde tu email.', 'success');
                $notice.show();
                startEdgePoll();
            } else {
                showNotice($notice, res.message || 'No se encontró una cuenta Partner Edge con ese email.', 'error');
                $notice.show();
            }
        })
        .fail(function (xhr) {
            setBtn($btn, 'Buscar', false);
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Error al enviar. Intenta de nuevo.';
            showNotice($notice, msg, 'error');
            $notice.show();
        });
    });

    function startEdgePoll() {
        stopEdgePoll();
        edgePollInterval = setInterval(function () {
            if (!edgeRequestId) { stopEdgePoll(); return; }

            $.getJSON('https://nexovadesk.com/api/partner/connect-status', { request_id: edgeRequestId })
                .done(function (res) {
                    if (res.status === 'completed') {
                        stopEdgePoll();
                        // Guardar conexión igual que popup flow
                        ajax(
                            'nexova_desk_save_connection',
                            {
                                server_url: res.server_url,
                                token:      res.plugin_token,
                                org_name:   res.org_name  || '',
                                org_id:     res.org_id    || 0,
                                org_plan:   res.org_plan  || 'partner',
                                is_partner: 1,
                                is_edge:    (res.org_plan === 'partner_edge') ? 1 : 0,
                            },
                            function () { window.location.reload(); },
                            function (msg) {
                                showNotice($connectNotice, msg, 'error');
                                $connectNotice.show();
                            }
                        );
                    } else if (res.status === 'expired' || res.status === 'not_found') {
                        stopEdgePoll();
                        showNotice($('#nexova-desk-edge-find-notice'), 'El enlace expiró. Vuelve a intentarlo.', 'error');
                        $('#nexova-desk-edge-find-notice').show();
                        $('#nexova-desk-edge-step-confirm').hide();
                    }
                });
        }, 4000);
    }

    // Buscar también al presionar Enter
    $('#nexova-desk-edge-email').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#nexova-desk-edge-find-btn').trigger('click'); }
    });

    // Volver a escribir otro email
    $('#nexova-desk-edge-back-btn').on('click', function () {
        stopEdgePoll();
        $('#nexova-desk-edge-step-confirm').hide();
        $('#nexova-desk-edge-find-notice').hide();
        $('#nexova-desk-edge-connect-btn').show();
        $('#nexova-desk-edge-email').val('').focus();
        edgeRequestId    = null;
        edgePartnerDomain = null;
    });

    // ── Helper: abrir popup de conexión ──
    function openConnectPopup(serverUrl, $triggerBtn) {
        serverUrl = serverUrl.replace(/\/+$/, '');
        var popupUrl  = serverUrl + '/connect';
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
            $connectNotice.show();
            return;
        }

        if ($triggerBtn) setBtn($triggerBtn, cfg.i18n.connecting, true);
        $connectNotice.hide();

        var checkClosed = setInterval(function () {
            if (connectPopup && connectPopup.closed) {
                clearInterval(checkClosed);
                if ($triggerBtn && $triggerBtn.is(':disabled')) {
                    setBtn($triggerBtn, $triggerBtn.data('original-label') || 'Conectar', false);
                }
            }
        }, 800);
    }

    // Abre popup estándar
    $connectBtn.on('click', function () {
        var serverUrl = $.trim($serverInput.val()) || 'https://nexovadesk.com';
        openConnectPopup(serverUrl, $connectBtn);
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
                    is_partner: data.is_partner ? 1 : 0,
                    is_edge:    (data.org_plan === 'partner_edge') ? 1 : 0,
                },
                function () {
                    window.location.reload();
                },
                function (msg) {
                    setBtn($connectBtn, 'Conectar con Nexova Desk', false);
                    showNotice($connectNotice, msg, 'error');
                    $connectNotice.show();
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

    var $widgetsList    = $('#nexova-desk-widgets-list');
    var $refreshBtn     = $('#nexova-desk-refresh-widgets');
    var $tokenInput     = $('#nexova-desk-widget-token');
    var $widgetIdInput  = $('#nexova-desk-widget-id');
    var $widgetNameIn   = $('#nexova-desk-widget-name');
    var $wooWidgetIdIn  = $('#nexova-desk-woo-widget-id');
    var $widgetNotice   = $('#nexova-desk-widgets-notice');
    var $saveWidgetBtn  = $('#nexova-desk-save-widget');
    var $saveWidgetMsg  = $('#nexova-desk-save-widget-notice');

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

        var currentToken  = $tokenInput.val();
        var currentWooId  = parseInt($wooWidgetIdIn.val() || '0', 10);
        // If no woo widget stored yet, detect from API data
        if (!currentWooId) {
            widgets.forEach(function (w) { if (w.woo_integration_enabled) currentWooId = w.id; });
        }
        var html = '';

        var wooLogoSvg =
            '<svg viewBox="0 0 50 30" width="36" height="22" xmlns="http://www.w3.org/2000/svg">' +
            '<rect width="50" height="30" rx="5" fill="#96588a"/>' +
            '<text x="25" y="21" font-family="Arial,sans-serif" font-size="13" font-weight="700" ' +
            'fill="#fff" text-anchor="middle">Woo</text>' +
            '</svg>';

        widgets.forEach(function (w) {
            var isSelected = (w.token === currentToken);
            var isWoo      = (w.id === currentWooId || w.woo_integration_enabled);
            var statusBadge = w.is_active
                ? '<span class="nexova-desk-badge nexova-desk-badge--active">Activo</span>'
                : '<span class="nexova-desk-badge nexova-desk-badge--inactive">Inactivo</span>';
            var wooBadge = isWoo
                ? '<span class="nexova-desk-badge nexova-desk-badge--woo" title="Widget con integración WooCommerce">' + wooLogoSvg + '</span>'
                : '';
            var wooBtn =
                '<button type="button" class="nexova-desk-woo-btn' + (isWoo ? ' is-woo' : '') + '"' +
                ' data-id="' + escAttr(w.id) + '"' +
                ' title="' + (isWoo ? 'WooCommerce activo en este widget' : 'Activar WooCommerce en este widget') + '">' +
                wooLogoSvg +
                '</button>';

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
                    wooBtn +
                    '<div class="nexova-desk-widget-card__check">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"' +
                    ' stroke="currentColor" stroke-width="2.5" width="16" height="16">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>' +
                    '</svg></div></div>';
        });

        $widgetsList.html(html);
        if (currentWooId) $wooWidgetIdIn.val(currentWooId);
    }

    // Seleccionar widget de display
    $(document).on('click', '.nexova-desk-widget-card', function (e) {
        if ($(e.target).closest('.nexova-desk-woo-btn').length) return; // handled separately
        var $card = $(this);
        $('.nexova-desk-widget-card').removeClass('is-selected');
        $card.addClass('is-selected');
        $tokenInput.val($card.data('token'));
        $widgetIdInput.val($card.data('id'));
        $widgetNameIn.val($card.data('name'));
    });

    // Seleccionar widget de WooCommerce
    $(document).on('click', '.nexova-desk-woo-btn', function (e) {
        e.stopPropagation();
        var wooId = parseInt($(this).data('id'), 10);
        $wooWidgetIdIn.val(wooId);
        // Update visual state
        $('.nexova-desk-woo-btn').removeClass('is-woo')
            .attr('title', 'Activar WooCommerce en este widget');
        $(this).addClass('is-woo')
            .attr('title', 'WooCommerce activo en este widget');
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
                widget_token:        token,
                widget_id:           $widgetIdInput.val(),
                widget_name:         $widgetNameIn.val(),
                woo_widget_id:       $wooWidgetIdIn.val() || $widgetIdInput.val(),
                orders_enabled:      $('#nexova-desk-orders-enabled').is(':checked') ? '1' : '',
                woo_context_enabled: $('#nexova-desk-woo-context-enabled').is(':checked') ? '1' : '',
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
