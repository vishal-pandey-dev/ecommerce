<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BigCommerce</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">BigCommerce Products</h1>

        <div class="mb-4">
            <button id="syncBtn" class="btn btn-primary">
                Sync Products to SaaS System
            </button>
            <span id="syncStatus" class="ms-3 text-success fw-bold" style="display: none;"></span>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <p class="card-text">{{ $product['description'] ?? 'No description' }}</p>
                            <p class="card-text"><strong>Price:</strong> ${{ number_format($product['price'], 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
</body>

<script>
    document.getElementById('syncBtn').addEventListener('click', function () {
        let status = document.getElementById('syncStatus');
        status.style.display = 'inline';
        status.innerText = 'Syncing...';

        axios.post('{{ url("/sync-products") }}')
            .then(res => {
                status.innerText = '✅ Sync completed successfully!';
            })
            .catch(err => {
                console.error(err);
                status.innerText = '❌ Sync failed.';
            });
    });
</script>


</html>
