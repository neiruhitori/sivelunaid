<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'is_open' => ['required', 'boolean'],
        ]);

        SiteSetting::setStorefrontOpen((bool) $data['is_open']);

        return redirect()->route('admin.dashboard')
            ->with('status', $data['is_open'] ? 'Website berhasil dibuka.' : 'Website berhasil ditutup.');
    }
}
