<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WpPluginToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API endpoints para el plugin WooCommerce de Nexova Desk.
 * Autenticación: Bearer token (wp_plugin_tokens.token)
 */
class WpApiController extends Controller
{
    /**
     * Resuelve el WpPluginToken a partir del header Authorization.
     */
    private function resolveToken(Request $request): ?WpPluginToken
    {
        $bearer = $request->bearerToken();
        if (! $bearer) return null;

        $token = WpPluginToken::with('organization')->where('token', $bearer)->first();
        if (! $token) return null;

        $token->touch('last_used_at');
        return $token;
    }

    /**
     * GET /api/wp/verify
     * Verifica que el token es válido.
     */
    public function verify(Request $request): JsonResponse
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return response()->json(['ok' => false, 'message' => 'Token inválido.'], 401);
        }

        $org = $token->organization;

        if (! $org->is_active) {
            return response()->json([
                'ok'      => false,
                'active'  => false,
                'message' => 'Cuenta desactivada.',
            ], 403);
        }

        // Determine plan label for WP plugin display
        $plan = $org->plan ?? 'free';
        if ($org->is_partner) {
            // Check if this is an Edge installation (partner_domain set)
            $plan = $org->partner_domain ? 'partner_edge' : 'partner';
        }

        return response()->json([
            'ok'         => true,
            'active'     => true,
            'org_name'   => $org->name,
            'org_id'     => $token->organization_id,
            'org_plan'   => $plan,
            'is_partner' => (bool) $org->is_partner,
            'is_edge'    => (bool) ($org->is_partner && $org->partner_domain),
        ]);
    }

    /**
     * GET /api/wp/widgets
     * Lista los widgets de la organización.
     */
    public function widgets(Request $request): JsonResponse
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $widgets = $token->organization
            ->chatWidgets()
            ->select('id', 'name', 'token', 'is_active')
            ->orderBy('name')
            ->get()
            ->map(fn ($w) => [
                'id'        => $w->id,
                'name'      => $w->name,
                'token'     => $w->token,
                'is_active' => (bool) $w->is_active,
            ]);

        return response()->json(['widgets' => $widgets]);
    }

    /**
     * GET /api/wp/widgets/{id}
     * Datos de un widget específico incluyendo toggles de integración WooCommerce.
     */
    public function widget(Request $request, int $id): JsonResponse
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $widget = $token->organization
            ->chatWidgets()
            ->find($id);

        if (! $widget) {
            return response()->json(['message' => 'Widget no encontrado.'], 404);
        }

        return response()->json([
            'widget' => [
                'id'                      => $widget->id,
                'name'                    => $widget->name,
                'token'                   => $widget->token,
                'is_active'               => (bool) $widget->is_active,
                'woo_integration_enabled' => (bool) ($widget->woo_integration_enabled ?? false),
                'woo_orders_enabled'      => (bool) ($widget->woo_orders_enabled ?? false),
            ],
        ]);
    }

    /**
     * PATCH /api/wp/widgets/{id}
     * El plugin WP sincroniza sus toggles de WooCommerce al servidor.
     */
    public function updateWidget(Request $request, int $id): JsonResponse
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $widget = $token->organization->chatWidgets()->find($id);

        if (! $widget) {
            return response()->json(['message' => 'Widget no encontrado.'], 404);
        }

        // Los toggles woo_integration_enabled / woo_orders_enabled se controlan
        // exclusivamente desde el panel Nexova — el plugin WP no puede sobreescribirlos.

        return response()->json(['ok' => true]);
    }
}
