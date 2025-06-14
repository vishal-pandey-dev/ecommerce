<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SyncProductsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Cache::has('bigcommerce_product_sync')) {
            Cache::put('bigcommerce_product_sync', true, now()->addMinutes(10));
            $this->syncProducts(); // Call product sync logic
        }

        return $next($request);
    }

    protected function syncProducts()
    {
        $apiUrl      = config('bigcommerce.api_url');
        $accessToken = config('bigcommerce.access_token');
        $targetUrl   = 'https://box.saasintegrator.ai/api/products';
        $targetToken = 'MRbCcqplvHIyA9ZM9DYCYsIPVbStXA';

        // Fetch products from BigCommerce
        $response = Http::withHeaders([
            'X-Auth-Token'  => $accessToken,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])
        ->withoutVerifying()
        ->get($apiUrl . 'catalog/products');

        if (!$response->ok()) {
            logger()->error('BigCommerce fetch failed', ['status' => $response->status(), 'body' => $response->body()]);
            return;
        }

        $products = $response->json('data') ?? [];

        foreach ($products as $product) {
            try {
                $sku = 'SKU-' . $product['id'] . '-' . substr(md5($product['id'] . microtime()), 0, 6);

                $data = [
                    'sku'         => $sku,
                    'name'        => $product['name'],
                    'slug'        => Str::slug($product['name']),
                    'description' => $product['description'] ?? '',
                    'is_active'   => true,
                    'price'       => $product['price'] ?? 0,
                    'category_id' => 1,
                ];

                $postResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $targetToken,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->post($targetUrl, $data);

                logger()->info("Synced Product ID: {$product['id']}", [
                    'status' => $postResponse->status(),
                    'response' => $postResponse->body(),
                ]);
            } catch (\Exception $e) {
                logger()->error("Sync failed for Product ID: {$product['id']}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
