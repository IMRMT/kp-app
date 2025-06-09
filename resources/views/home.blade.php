@extends('layout.conquer')
@section('title')
@section('content')

<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Home</h1>

<div class="flex flex-col lg:flex-row gap-6">
    {{-- LEFT SIDE: Cards + Charts --}}
    <div class="w-full lg:w-2/5 space-y-6">
        {{-- Total Sales Card --}}
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-2 rounded shadow">
            <p class="text-sm">Total Penjualan Bulan Ini</p>
            <p class="text-lg font-bold">Rp{{ number_format($totalSalesRupiah, 0, ',', '.') }}</p>
        </div>

        {{-- Total Purchases Card --}}
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 px-4 py-2 rounded shadow">
            <p class="text-sm">Total Pembelian Bulan Ini</p>
            <p class="text-lg font-bold">Rp{{ number_format($totalPurchasesRupiah, 0, ',', '.') }}</p>
        </div>

        {{-- Sales Chart --}}
        <div>
            <h3 class="font-semibold text-gray-700 mb-2">Penjualan per Minggu Bulan Ini</h3>
            <canvas id="salesChart"></canvas>
        </div>

        {{-- Purchases Chart --}}
        <div>
            <h3 class="font-semibold text-gray-700 mb-2">Pembelian per Minggu Bulan Ini</h3>
            <canvas id="purchasesChart"></canvas>
        </div>
    </div>

    {{-- RIGHT SIDE: Produk List + Filter --}}
    <div class="w-full lg:w-3/5">
        {{-- Search Bar --}}
        <form method="GET" action="{{ route('homeProduk') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari Produk..." value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        {{-- Sort Options --}}
        <form method="GET" action="{{ url('/home') }}" class="flex flex-wrap gap-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <select name="sort_by" class="form-select px-4 py-2 rounded border border-gray-300">
                    <option value="nama" {{ request('sort_by') === 'nama' ? 'selected' : '' }}>Nama</option>
                    <option value="total_stok" {{ request('sort_by') === 'total_stok' ? 'selected' : '' }}>Stok</option>
                    <option value="sellingprice" {{ request('sort_by') === 'sellingprice' ? 'selected' : '' }}>Harga</option>
                </select>

                <select name="sort_order" class="form-select px-4 py-2 rounded border border-gray-300">
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Sort</button>
            </div>
        </form>

        {{-- Product Cards --}}
        <h2 class="mb-4">📦 Produk</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2 gap-x-2 gap-y-4 justify-items-center">
            @foreach ($datas as $d)
                <a href="{{ route('produk.show', ['id' => $d->id]) }}" class="block hover:cursor-pointer">
                    <div class="w-72 bg-white dark:bg-zinc-800 rounded-lg shadow hover:shadow-lg transition flex flex-col">
                        <img src="{{ asset('/produk_image/' . $d->image) }}"
                             class="w-full h-full object-cover rounded-t-lg self-center">
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div class="font-semibold mb-2">
                                Nama Produk: <br> {{ $d->nama }}
                            </div>
                            <div class="text-sm text-right text-gray-600 dark:text-gray-400 mt-auto">
                                <p>Stok: {{ $d->total_stok ?? 0 }}</p>
                                <p>Harga: Rp{{ number_format($d->sellingprice, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $datas->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: @json($chartLabelsSales),
            datasets: [{
                label: 'Produk Terjual per Minggu',
                data: @json($chartDataSales),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    const purchasesCtx = document.getElementById('purchasesChart').getContext('2d');
    new Chart(purchasesCtx, {
        type: 'bar',
        data: {
            labels: @json($chartLabelsPurchases),
            datasets: [{
                label: 'Produk Dibeli per Minggu',
                data: @json($chartDataPurchases),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endsection
