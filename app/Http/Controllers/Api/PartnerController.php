<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Verify a partner license token.
     * Called daily by partner installations to confirm their license is active.
     *
     * GET /api/partner/verify/{token}
     */
    public function verify(Request $request, string $token): JsonResponse
    {
        $org = Organization::where('partner_token', $token)
            ->where('is_partner', true)
            ->where('is_active', true)
            ->first();

        if (! $org) {
            return response()->json([
                'valid'   => false,
                'message' => 'Partner license not found or inactive.',
            ], 403);
        }

        return response()->json([
            'valid'      => true,
            'partner'    => $org->name,
            'plan'       => 'partner',
            'expires_at' => null, // Partner = lifetime
            'checked_at' => now()->toIso8601String(),
        ]);
    }
}
