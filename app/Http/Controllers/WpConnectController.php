<?php

namespace App\Http\Controllers;

use App\Models\WpPluginToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Maneja el flujo de autenticación del plugin WooCommerce.
 *
 * Flujo:
 *  1. WP abre popup → GET /wp-connect?origin=https://tienda.com
 *  2. Usuario ve pantalla de confirmación (ya tiene sesión) o login
 *  3. POST /wp-connect/authorize → genera token → postMessage al opener → cierra popup
 */
class WpConnectController extends Controller
{
    /**
     * Muestra la página de autorización del plugin.
     */
    public function show(Request $request)
    {
        $origin = $request->query('origin', '');

        return view('wp-connect.authorize', [
            'origin'      => $origin,
            'isLoggedIn'  => Auth::check(),
            'user'        => Auth::user(),
        ]);
    }

    /**
     * Genera el token y lo envía de vuelta al plugin via postMessage.
     */
    public function authorize(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('auth.login');
        }

        $user   = Auth::user();
        $origin = $request->input('origin', '');

        if (! $user->organization_id) {
            return back()->withErrors(['Sin organización asociada a este usuario.']);
        }

        // Un token por organización (reutilizar o regenerar)
        $pluginToken = WpPluginToken::firstOrCreate(
            ['organization_id' => $user->organization_id],
            [
                'user_id'  => $user->id,
                'site_url' => $origin,
            ]
        );

        // Actualizar site_url si cambia
        if ($pluginToken->site_url !== $origin) {
            $pluginToken->update(['site_url' => $origin, 'user_id' => $user->id]);
        }

        $org = $user->organization;

        return view('wp-connect.success', [
            'origin'   => $origin,
            'token'    => $pluginToken->token,
            'orgName'  => $org->name,
            'orgId'    => $org->id,
            'serverUrl'=> rtrim(config('app.url'), '/'),
        ]);
    }
}
