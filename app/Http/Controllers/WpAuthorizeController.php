<?php

namespace App\Http\Controllers;

use App\Models\WpPluginToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WpAuthorizeController extends Controller
{
    private const MAIN_URL = 'https://nexovadesk.com';

    public function show(Request $request, string $token)
    {
        $res = Http::get(self::MAIN_URL . '/api/partner/verify-email-token', ['token' => $token]);

        if (! $res->ok() || ! $res->json('valid')) {
            return view('wp-connect.link-expired');
        }

        $data = $res->json();

        session([
            'wp_auth_request_id'     => $data['request_id'],
            'wp_auth_callback_token' => $data['callback_token'],
        ]);

        if (! Auth::check()) {
            $redirectTo = urlencode('/wp-authorize/' . $token);
            return redirect('/login?redirect=' . $redirectTo);
        }

        return view('wp-connect.authorize', [
            'origin'      => '',
            'isLoggedIn'  => true,
            'user'        => Auth::user(),
            'emailFlow'   => true,
        ]);
    }

    public function confirm(Request $request)
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $requestId     = session('wp_auth_request_id');
        $callbackToken = session('wp_auth_callback_token');

        if (! $requestId || ! $callbackToken) {
            return view('wp-connect.link-expired');
        }

        $user = Auth::user();
        $org  = $user->organization;

        $pluginToken = WpPluginToken::firstOrCreate(
            ['organization_id' => $org->id],
            ['user_id' => $user->id, 'site_url' => '']
        );

        $plan = $org->plan ?? 'free';
        if ($org->is_partner) {
            $plan = $org->partner_domain ? 'partner_edge' : 'partner';
        }

        Http::post(self::MAIN_URL . '/api/partner/complete-connect', [
            'request_id'     => $requestId,
            'callback_token' => $callbackToken,
            'plugin_token'   => $pluginToken->token,
            'org_id'         => $org->id,
            'org_name'       => $org->name,
            'org_plan'       => $plan,
            'server_url'     => rtrim(config('app.url'), '/'),
        ]);

        session()->forget(['wp_auth_request_id', 'wp_auth_callback_token']);

        return view('wp-connect.email-success', ['orgName' => $org->name]);
    }
}
