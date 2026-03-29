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
        return response()->json([
            'ok'       => true,
            'org_name' => $org->name,
            'org_id'   => $token->organization_id,
            'org_plan' => $org->plan ?? 'free',
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
     * Datos de un widget específico.
     */
    public function widget(Request $request, int $id): JsonResponse
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $widget = $token->organization
            ->chatWidgets()
            ->select('id', 'name', 'token', 'is_active')
            ->find($id);

        if (! $widget) {
            return response()->json(['message' => 'Widget no encontrado.'], 404);
        }

        return response()->json([
            'widget' => [
                'id'        => $widget->id,
                'name'      => $widget->name,
                'token'     => $widget->token,
                'is_active' => (bool) $widget->is_active,
            ],
        ]);
    }
}
