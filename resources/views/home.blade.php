@extends('layout.conquer')
@section('title')
@section('content')
    <form method="GET" action="{{ route('homeProduk') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari Produk..." value="{{ $search }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">Search</button>
            </div>
        </div>
    </form>

    <div class="main-content">
        <form method="GET" action="{{ url('/') }}" class="flex flex-wrap gap-4 mb-6">
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
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Home</h1>
        <h2 class="mb-4">📦 Produk</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-1 gap-y-4 justify-items-center">
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
                                <p>Stok: {{ $d->total_stok ?? 0}}</p>
                                <p>Harga: Rp{{ number_format($d->sellingprice, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $datas->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
