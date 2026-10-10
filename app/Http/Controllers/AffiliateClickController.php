<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AffiliateClickController extends Controller
{
    /**
     * Record an affiliate click and redirect the visitor to the affiliate URL.
     */
    public function redirect(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->isActive(), 404);
        abort_if(blank($product->affiliate_url), 404);

        $product->affiliateClicks()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'clicked_at' => now(),
        ]);

        $product->increment('clicks');

        return redirect()->away($product->affiliate_url);
    }
}
