<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class BigCommerceController extends Controller

{
    public function index(Request $request)
    {
        // Example of using BigCommerce API
        $apiUrl = config('bigcommerce.api_url');
        $clientId = config('bigcommerce.client_id');
        $accessToken = config('bigcommerce.access_token');

        $response = Http::withHeaders([
            'X-Auth-Token' => $accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withoutVerifying()->get($apiUrl . 'catalog/products');


        $products = $response->json('data');

        return view('bigcommerce.index', compact('products'));
    }

    public function syncProducts(Request $request)
    {
        $apiUrl = config('bigcommerce.api_url');
        $accessToken = config('bigcommerce.access_token');
        $targetUrl = 'https://box.saasintegrator.ai/api/products';
        $targetToken = 'MRbCcqplvHIyA9ZM9DYCYsIPVbStXA';

        // Fetch products from BigCommerce
        $response = Http::withHeaders([
            'X-Auth-Token' => $accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withoutVerifying()->get($apiUrl . 'catalog/products');

        $products = $response->json('data');
        foreach ($products as $product) {
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

            // Send to SaaS integrator and capture response
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $targetToken,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
                ->withoutVerifying()
                ->post($targetUrl, $data);

        }

        return response()->json(['message' => 'Sync completed']);
    }
}
