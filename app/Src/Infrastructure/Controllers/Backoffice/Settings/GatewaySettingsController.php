<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GatewaySettingsController
{
    public function index(): Response
    {
        $gateways = DB::table('payment_gateways')
            ->select('id', 'name', 'slug', 'is_active', 'updated_at')
            ->get();

        return Inertia::render('settings/gateways', [
            'gateways' => $gateways,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        DB::table('payment_gateways')
            ->where('slug', $slug)
            ->update([
                'is_active' => $request->boolean('is_active'),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Gateway updated successfully.');
    }
}
