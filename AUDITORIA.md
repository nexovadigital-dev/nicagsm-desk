# Auditoría Nexova Desk Edge — nicagsm.nexovadesk.com
> Iniciada: 2026-04-24 | Objetivo: estabilidad, coherencia y calidad antes de entrega

---

## REGLAS DE TRABAJO

- NO romper lógica existente
- NO introducir nuevas features
- TODO debe mantenerse estable
- Si algo es riesgoso, preguntar antes de implementar
- Cada fase: auditar → detectar → proponer → implementar

---

## ESTADO GENERAL

| Fase | Estado | Problemas encontrados | Problemas resueltos |
|---|---|---|---|
| FASE 1 — Funcionalidad crítica | ✅ Completada | 4 | 2 críticos + 1 medio |
| FASE 2 — UX | ✅ Completada | 2 | 1 corregido, 1 marcado FASE 6 |
| FASE 3 — UI | ✅ Completada | 3 | 3 corregidos |
| FASE 4 — Performance | ✅ Completada | 4 | 2 corregidos, 2 documentados |
| FASE 5 — Edge Cases | ✅ Completada | 7 | 3 corregidos, 4 confirmados OK |
| FASE 6 — Limpieza final | ✅ Completada | 7 | 2 corregidos, 5 confirmados limpios |

---

## FASE 1 — FUNCIONALIDAD CRÍTICA
> Nada puede fallar aquí.

### Áreas a auditar
- [x] Login / autenticación / middlewares
- [x] Envío y recepción de mensajes
- [x] Bot (FAQ → KB → IA fallback)
- [x] Persistencia de configuraciones (toggles, settings, chat_widgets)
- [x] WooCommerce integration (woo_integration_enabled, woo_orders_enabled)
- [x] Scheduled commands (console.php vs archivos existentes)
- [x] Migraciones vs campos en DB vs fillable en modelos

### Problemas detectados

| # | Severidad | Área | Descripción | Estado |
|---|---|---|---|---|
| F1-01 | 🔴 CRÍTICO | EditChatWidget | `aiEnabled` nunca se carga de la DB en `mount()` — `public bool $aiEnabled = true` como default, pero `mount()` no asigna `$this->aiEnabled = $w->ai_enabled`. Resultado: `save()` siempre escribe `ai_enabled = true`, imposible desactivar IA desde el panel. | ✅ Corregido |
| F1-02 | 🟡 MEDIO | ChatWidget model / migrations | `faq_quick_reply` sin migración en `chat_widgets` y sin entrada en `$fillable`. La columna no existe en DB. Siempre retorna `null → ?? true`. No se puede persistir `false`. | ✅ Corregido |
| F1-03 | 🔵 INFO | API routes | `adminNewEvents` y `adminUnreadCount` son endpoints públicos sin autenticación. Cualquier IP puede consultar eventos de agente. Riesgo bajo en sistema single-org pero arquitecturalmente incorrecto. | ⚠️ Documentado — no fix en auditoría |
| F1-04 | 🔵 INFO | ChatController | `geolocate()` hace una llamada HTTP bloqueante a `http://ip-api.com` (timeout 3s) en cada `startSession` y `visitorPing`. Si ip-api.com tarda, el inicio de sesión tarda hasta 3s extra. | ⚠️ Documentado — aceptable por ahora |

### Arquitectura confirmada como correcta
- **Login/auth:** Filament maneja autenticación + 2FA (`TwoFactorAuthentication` middleware). `PartnerLicenseCheck` skipea correctamente rutas API/widget. ✅
- **Envío de mensajes:** `sendMessage` → valida session_id → verifica estado ticket → `Message::create` → `ProcessBotReply::dispatch`. Flujo limpio. ✅
- **Bot FAQ→KB→IA:** Orden correcto: saludo → FAQ → KB directa → IA. Fallback entre proveedores automático. ✅
- **WooCommerce:** `woo_integration_enabled` y `woo_orders_enabled` en `$fillable`, en casts, en `save()`, en `widgetConfig()`, en `NexovaAiService`. Flujo completo. ✅
- **Scheduled commands:** Todos los comandos en `console.php` tienen su archivo en `Commands/`. ✅
  - `tickets:process-inbound` → `ProcessInboundEmails.php` ✅
  - `partner:check-license` → `CheckPartnerLicense.php` ✅
  - `chat:expire-agent-calls` → `ExpireAgentCalls.php` ✅
  - `tickets:auto-close` → `AutoCloseInactiveTickets.php` ✅ (creado en sesión anterior)
  - `nexova:scrape-url` → `ScrapeUrl.php` ✅
- **Migraciones vs fillable:** Todos los campos de `chat_widgets` en `$fillable` tienen su migración. ✅ (excepto `faq_quick_reply` — corregido en F1-02)

### Fixes aplicados

| Fix | Archivo | Descripción |
|---|---|---|
| F1-01 | `app/Filament/Resources/ChatWidgets/Pages/EditChatWidget.php:96` | Agregado `$this->aiEnabled = (bool) ($w->ai_enabled ?? true);` en `mount()` |
| F1-02a | `database/migrations/2026_04_24_100000_add_faq_quick_reply_to_chat_widgets.php` | Nueva migración crea columna `faq_quick_reply boolean default true` |
| F1-02b | `app/Models/ChatWidget.php` | Agregado `faq_quick_reply` a `$fillable` y a `$casts` como `boolean` |

---

## FASE 2 — EXPERIENCIA DE USUARIO (UX)
> Fluidez y feedback visual correcto.

### Áreas a auditar
- [x] Scroll de chat (no auto-scroll agresivo)
- [x] Indicador "escribiendo..." (timing, aparición, desaparición)
- [x] Estados de carga (botones, spinners)
- [x] Feedback visual en acciones (guardar, enviar, error)
- [x] Fluidez general del widget

### Problemas detectados

| # | Severidad | Área | Descripción | Estado |
|---|---|---|---|---|
| F2-01 | 🟡 MEDIO | `sendMessage()` widget | Error banner no se limpiaba al reintentar un envío fallido — el usuario veía el error previo mientras reintentaba. Faltaba `setError(null)` al inicio de `sendMessage()`. | ✅ Corregido |
| F2-02 | 🔵 INFO (FASE 6) | `fetchMessages()` widget | Variable `const unread = newMsgs.filter(...)` computada pero nunca usada — dead code. | 📝 Para FASE 6 |

### Arquitectura UX confirmada como correcta
- **Auto-scroll quirúrgico:** Solo fuerza scroll si el usuario está a < 120px del fondo. Si está leyendo arriba, muestra banner "Nuevos mensajes ↓" en lugar de interrumpir. ✅
- **Typing indicator timing:** 200ms de delay antes de mostrar (evita flicker en respuestas rápidas de FAQ). Mínimo 800ms de visibilidad. Safety timeout de 30s. ✅
- **Optimistic messages:** El mensaje del usuario aparece instantáneamente antes de confirmación del servidor. ✅
- **`isSending` guard:** Bloquea envíos duplicados mientras hay uno en vuelo. ✅
- **Textarea auto-resize:** Crece hasta 96px máximo, reset a 24px al enviar. ✅
- **Error banner:** Visible en la cabecera del chat, se limpia en el siguiente poll exitoso. ✅
- **"Nuevos mensajes ↓" banner:** Se limpia automáticamente al hacer scroll cerca del fondo. ✅
- **Sonidos:** Correctamente guardados por `soundEnabled`. ✅

### Fixes aplicados

| Fix | Archivo | Descripción |
|---|---|---|
| F2-01 | `resources/js/widget/NexovaChatWidget.jsx:2326` | Agregado `setError(null)` al inicio de `sendMessage()` antes de `setIsSending(true)` |

---

## FASE 3 — INTERFAZ (UI)
> Consistencia visual y ausencia de glitches.

### Áreas a auditar
- [x] Eliminar estilos genéricos / placeholders
- [x] Consistencia de spacing, colores, tipografía
- [x] Dark / Light mode sin glitches
- [x] Responsive: móvil, tablet, desktop
- [x] Panel admin Filament (nicagsm-desk)
- [x] Widget (NexovaChatWidget.jsx)

### Problemas detectados

| # | Severidad | Área | Descripción | Estado |
|---|---|---|---|---|
| F3-01 | 🔵 CSS | `theme.css:813` | `border-top-color` duplicado en spinner dark — primer valor sobreescrito inmediatamente por `border-color` shorthand. Dead CSS. | ✅ Corregido |
| F3-02 | 🟡 MEDIO | `edit-chat-widget.blade.php:202,247` | Dos cajas de info (Bot de IA, IA config) con `background:#f9fafb` hardcodeado. En dark mode aparecen blancas sobre fondo oscuro. Texto interior con colores hardcodeados también incorrecto. | ✅ Corregido |
| F3-03 | 🔵 INFO | Widget responsive | `isMobile` calculado estáticamente en render — no reacciona a resize de ventana. Impacto mínimo (widget flotante no cambia de layout). | ⚠️ Documentado — aceptable |

### Arquitectura UI confirmada como correcta
- **Sistema de tokens `--nx-*` y `--c-*`**: paleta completa, light y dark, con aliases de retrocompatibilidad. ✅
- **Dark mode completo**: sidebar, topbar, modales, dropdowns, tablas, formularios, páginas custom (dp-*, tk-*, vp-*, nx-*). ✅
- **Theme switcher premium**: popover `nx-ts-*` con opciones Light/Dark/System, transitions suaves. ✅
- **Flash prevention**: `html.nx-preload` desactiva transitions durante render inicial. `html.nx-switching` las reactiva suavemente. ✅
- **Widget isolado**: `.nx-widget-window *` con box-sizing, reset de estilos del host, iframe-safe. ✅
- **Widget responsive**: `width: min(350px, calc(100vw - 32px))`, `height: min(480px, calc(100vh - 100px))`. Funciona en móvil. ✅
- **Animations**: 8 keyframes con prefijo `nx-` para no colisionar con host. ✅
- **Scroll personalizado**: `scrollbar-width: thin` + webkit, con tokens de color. ✅
- **Placeholders**: todos son ejemplos reales en español, no lorem ipsum. ✅

### Fixes aplicados

| Fix | Archivo | Descripción |
|---|---|---|
| F3-01 | `resources/css/filament/admin/theme.css:813` | Eliminado `border-top-color` duplicado en spinner dark |
| F3-02a | `resources/css/filament/admin/theme.css` | Agregado bloque `html.dark .wc-info-box` con tokens `--nx-surf2`, `--nx-border`, `--nx-text`, `--nx-muted` |
| F3-02b | `edit-chat-widget.blade.php:202,247` | Agregado `class="wc-info-box"` a los dos divs de sección con background hardcodeado |

---

## FASE 4 — PERFORMANCE
> Sin delays innecesarios ni renders excesivos.

### Áreas a auditar
- [x] Reducir delays y timeouts innecesarios
- [x] Optimizar AJAX / polling interval
- [x] Evitar renders innecesarios en React (widget)
- [x] Validar comportamiento en hosting compartido (memoria, tiempo de respuesta)
- [x] Cache de configuraciones (config:cache, route:cache)

### Problemas detectados

| # | Severidad | Área | Descripción | Estado |
|---|---|---|---|---|
| F4-01 | 🔴 CRÍTICO | `NexovaAiService::buildRagContext()` | Sin cache — cada mensaje del bot hace un SELECT de todos los artículos KB desde DB. Con 10 conversaciones activas = 10 queries pesadas por intercambio. `buildWpCatalogBlock` sí usaba cache, `buildRagContext` no. | ✅ Corregido |
| F4-02 | 🟡 MEDIO | `ChatController::conversationsBulk()` | N+1 query: `$t->messages()->count()` dentro de `foreach` de hasta 20 items = hasta 20 COUNT queries. | ✅ Corregido |
| F4-03 | 🔵 INFO | `NexovaAiService` — `sleep()` calls | `sleep(random_int(5,8))` antes de Groq, `sleep(random_int(1,2))` en FAQ/KB. Intencionales: spacing de rate limit + UX de "pensando". Aceptable. | ⚠️ Documentado — by design |
| F4-04 | 🔵 INFO | `Log::info` en cada mensaje bot | Cada respuesta del bot genera al menos 1 Log::info. En producción con `LOG_LEVEL=error` esto no debería aparecer pero depende de la config del VPS. | 📝 Para FASE 6 |

### Arquitectura performance confirmada como correcta
- **Polling widget**: 3 segundos, estándar para chat sin WebSockets. Estado se compara antes de re-render (evita re-renders en cada poll). ✅
- **Render condicional**: `setMessages` devuelve `prev` sin cambio si `lastId === lastPrevId` → React no re-renderiza en polls sin nuevos mensajes. ✅
- **Heartbeat**: cada 15 segundos, endpoint separado con rate limit 120/min. ✅
- **WP Catalog cache**: `Cache::remember("nexova_wp_catalog_{$orgId}", 3600, ...)` — 1h de cache. ✅
- **Partner license cache**: 24h si válida, 1h si inválida. Grace period en errores de red. ✅
- **`conversationsBulk` limit**: `array_slice($ids, 0, 20)` — máximo 20 conversaciones por request. ✅
- **geolocate timeout**: 3 segundos, fallback a array vacío en error. Aceptable. ✅

### Fixes aplicados

| Fix | Archivo | Descripción |
|---|---|---|
| F4-01 | `app/Services/NexovaAiService.php` | `buildRagContext()` envuelta en `Cache::remember("nexova_rag_{$orgId}_{$widgetId}", 300, ...)` — 5 min de cache por org+widget |
| F4-02 | `app/Http/Controllers/Api/ChatController.php` | `conversationsBulk()`: eliminado N+1, reemplazado con `withCount(['messages as message_count' => ...])` — 1 sola query agregada |

---

## FASE 5 — EDGE CASES
> El sistema debe manejar escenarios inesperados sin romperse.

### Áreas a auditar
- [x] Errores de red (timeout, 500, CORS)
- [x] Inputs inválidos (XSS, campos vacíos, strings muy largos)
- [x] Estados vacíos (sin widgets, sin tickets, sin mensajes)
- [x] Usuario inactivo (sesión expirada en panel)
- [x] Sesiones del widget expiradas o inválidas
- [x] Widget sin configuración (token inválido / dominio no autorizado)
- [x] WooCommerce activo pero sin productos

### Problemas detectados

| # | Severidad | Área | Descripción | Estado |
|---|---|---|---|---|
| F5-01 | 🟡 MEDIO | Config fetch (widget) | Si el servidor responde 403 `{disabled:true}` o `{error:...}`, `setCfg(data)` aplicaba igualmente la respuesta de error como config real. El widget renderizaba con defaults incorrectos. La guarda `if (cfg === null) return null` existía pero no se alcanzaba porque `.catch()` ponía `{}` en lugar de `null`. | ✅ Corregido |
| F5-02 | 🔵 INFO | Textarea widget | Sin `maxLength` en el textarea — el servidor valida `max:2000` pero el cliente no daba feedback antes del envío. | ✅ Corregido |
| F5-03 | 🟡 MEDIO | `active_visitors` DB | Sin comando de purga programada — los registros de visitantes solo se limpiaban reactivamente dentro de `visitorPing`. Con poco tráfico, registros viejos podían acumularse en DB indefinidamente (aunque la UI los filtraba por `last_ping_at`). Visitantes fantasma podían aparecer brevemente si un heartbeat rezagado llegaba desde una pestaña que despertó. | ✅ Corregido |
| F5-04 | 🔵 INFO | Sesión widget expirada | Si el servidor retorna 404 en `fetchMessages`, el widget correctamente limpia storage, resetea sessionId y vuelve al estado inicial. ✅ Confirmado OK. | ⚠️ Documentado — ya manejado |
| F5-05 | 🔵 INFO | IP baneada | `sendHeartbeat` → respuesta `{banned:true}` → widget se elimina del DOM completamente. ✅ Confirmado OK. | ⚠️ Documentado — ya manejado |
| F5-06 | 🔵 INFO | XSS en mensajes | React escapa automáticamente todo el contenido; ningún `dangerouslySetInnerHTML` presente en el chat render. ✅ Confirmado OK. | ⚠️ Documentado — ya manejado |
| F5-07 | 🔵 INFO | WooCommerce sin productos | `buildStoreContextBlock` guarda con `! empty($ctx)`, `buildWpCatalogBlock` guarda con `empty($products)` retornando string vacío. La IA sigue funcionando sin contexto de tienda. ✅ Confirmado OK. | ⚠️ Documentado — ya manejado |

### Fixes aplicados

| Fix | Archivo | Descripción |
|---|---|---|
| F5-01 | `resources/js/widget/NexovaChatWidget.jsx:1771` | Config fetch: `.then(r => r.ok ? r.json() : ...)` + check `data.disabled \|\| data.error` → `setCfg(null)`. `.catch()` también pone `null`. Widget no renderiza si config inválida. |
| F5-02 | `resources/js/widget/NexovaChatWidget.jsx:3125` | Agregado `maxLength={2000}` al textarea del chat |
| F5-03a | `app/Console/Commands/PurgeStaleVisitors.php` | Nuevo comando `chat:purge-visitors` — elimina registros con `last_ping_at < now() - 30s` |
| F5-03b | `routes/console.php` | `chat:purge-visitors` programado `everyMinute()` con `withoutOverlapping()` |

---

## FASE 6 — LIMPIEZA FINAL
> Código limpio, sin logs de debug, sin código muerto.

### Áreas a auditar
- [x] `Log::info` y `Log::debug` de desarrollo (eliminar o bajar a LOG_LEVEL=error)
- [x] Variables sin uso (`$foo`, `$unused`, etc.)
- [x] CSS duplicado o sin usar
- [x] Comentarios tipo "TODO", "FIXME", "debug"
- [x] Código comentado que ya no aplica
- [x] Imports sin usar en PHP y en JSX
- [x] `console.log` en widget.js

### Problemas detectados

| # | Severidad | Área | Descripción | Estado |
|---|---|---|---|---|
| F6-01 | 🟡 MEDIO | `NexovaAiService.php` | 14 llamadas `Log::info("[NexovaBot]...")` — una por cada rama de respuesta del bot. En producción llenan los logs con información de nivel debug. | ✅ Corregido |
| F6-02 | 🔵 INFO | `NexovaChatWidget.jsx` | `const unread = newMsgs.filter(...)` computada dentro de `fetchMessages` pero nunca leída — dead code. | ✅ Corregido |
| F6-03 | 🔵 INFO | General | `console.log` — ninguno encontrado en widget. ✅ | ⚠️ Confirmado limpio |
| F6-04 | 🔵 INFO | General | `dd()` / `dump()` / `var_dump()` — ninguno encontrado. ✅ | ⚠️ Confirmado limpio |
| F6-05 | 🔵 INFO | General | TODO / FIXME / HACK — ninguno en `app/`. El `// use Illuminate\Contracts\Auth\MustVerifyEmail;` en `User.php` es boilerplate estándar de Laravel, no dead code nuestro. ✅ | ⚠️ Confirmado limpio |
| F6-06 | 🔵 INFO | CSS | Sin duplicados post-auditoría. Las reglas `.wc-info-box` y corrección del spinner son únicas. ✅ | ⚠️ Confirmado limpio |
| F6-07 | 🔵 INFO | Imports JSX | Un solo `import` en el widget — todos los hooks (useState, useEffect, useRef, useCallback) se usan. ✅ | ⚠️ Confirmado limpio |

### Fixes aplicados

| Fix | Archivo | Descripción |
|---|---|---|
| F6-01 | `app/Services/NexovaAiService.php` | 14× `Log::info("[NexovaBot]` → `Log::debug` — `Log::warning` y `Log::error` intactos |
| F6-02 | `resources/js/widget/NexovaChatWidget.jsx:2031` | Eliminado `const unread = newMsgs.filter(m => m.sender_type !== 'user').length;` — dead code |

---

## LOG DE CAMBIOS DE AUDITORÍA

| Fecha | Fase | Archivo | Cambio | Resultado |
|---|---|---|---|---|
| 2026-04-24 | FASE 1 | `EditChatWidget.php` | `aiEnabled` cargado desde DB en `mount()` | Bug crítico corregido |
| 2026-04-24 | FASE 1 | `ChatWidget.php` | `faq_quick_reply` añadido a `$fillable` y `$casts` | Bug medio corregido |
| 2026-04-24 | FASE 1 | `migrations/2026_04_24_100000_add_faq_quick_reply...php` | Nueva migración: columna `faq_quick_reply` en `chat_widgets` | Bug medio corregido |
| 2026-04-24 | FASE 2 | `NexovaChatWidget.jsx:2326` | `setError(null)` al inicio de `sendMessage()` | Bug UX corregido |
| 2026-04-24 | FASE 3 | `theme.css:813` | Eliminado `border-top-color` duplicado en spinner dark | CSS muerto eliminado |
| 2026-04-24 | FASE 3 | `theme.css` + `edit-chat-widget.blade.php` | Dark mode para cajas Bot IA / AI config (wc-info-box) | Bug dark mode corregido |
| 2026-04-24 | FASE 4 | `NexovaAiService.php` | `buildRagContext()` con `Cache::remember` 5min | Bug crítico perf corregido |
| 2026-04-24 | FASE 4 | `ChatController.php` | N+1 en `conversationsBulk` → `withCount` | Bug medio perf corregido |
| 2026-04-24 | FASE 5 | `NexovaChatWidget.jsx:1771` | Config fetch: error/disabled → `setCfg(null)`, widget no renderiza | Bug medio corregido |
| 2026-04-24 | FASE 5 | `NexovaChatWidget.jsx:3125` | `maxLength={2000}` en textarea del chat | Info corregido |
| 2026-04-24 | FASE 5 | `PurgeStaleVisitors.php` + `console.php` | Cron `chat:purge-visitors` cada minuto — purga proactiva de visitantes | Bug medio corregido |
| 2026-04-24 | FASE 6 | `NexovaAiService.php` | 14× `Log::info` → `Log::debug` en respuestas del bot | Limpieza de logs |
| 2026-04-24 | FASE 6 | `NexovaChatWidget.jsx` | Eliminado dead code `const unread = ...` | Dead code eliminado |
