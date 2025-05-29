@if ($errors->any()) untuk memunculkan error
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Search -->
{{-- @extends('layout.conquer')

@section('title')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Halaman Penjualan Produk</h1>
<form method="GET" action="{{ route('notajuals.create') }}" class="mb-4">
    <div class="input-group mb-2">
        <input type="text" name="search" class="form-control" placeholder="Cari Produk..." value="{{ $search }}">
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </div>
</form>

<!-- Pegawai Info -->
<div class="mb-4">
    <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">
    <p>Pegawai: {{ auth()->user()->nama }}</p>
</div>

<!-- Produk List -->
<div id="produk-list">
    @foreach ($prod as $p)
        <div class="border p-4 rounded mb-4">
            <h4>{{ $p->nama }} (Kadaluarsa: {{ $p->tgl_kadaluarsa }})</h4>
            <p>Harga: Rp{{ number_format($p->sellingprice, 0, ',', '.') }}</p>
            <p>Stok Tersedia: {{ $p->stok }}</p>

            <!-- Add-to-Cart Form -->
            <form method="POST" action="{{ route('notajuals.cart') }}">
                @csrf
                <input type="hidden" name="produkbatches_id" value="{{ $p->id }}">
                <input type="hidden" name="distributors_id" value="{{ $p->distributors_id }}">
                <input type="hidden" name="tgl_kadaluarsa" value="{{ $p->tgl_kadaluarsa }}">
                <input type="hidden" name="nama" value="{{ $p->nama }}">
                <input type="hidden" name="satuan" value="{{ $p->satuan_nama }}">
                <input type="hidden" name="sellingprice" value="{{ $p->sellingprice }}">
                <input type="hidden" name="stok" value="{{ $p->stok }}">
                <input type="number" name="quantity"
                    value="{{ session('cart')[$p->id . '_' . $p->tgl_kadaluarsa]['quantity'] ?? 0 }}" min="0"
                    max="{{ $p->stok }}" class="form-control mb-2">
                <button class="btn btn-sm btn-success">Tambah ke Keranjang</button>
            </form>
        </div>
    @endforeach
</div>


<!-- Cart Display -->
@if (session('cart'))
    <div class="card mb-4 p-3 border border-primary">
        <h5 class="mb-3">Keranjang Saat Ini</h5>
        <ul>
            @foreach (session('cart', []) as $key => $item)
                <li>
                    {{ $item['nama'] }} - {{ $item['quantity'] }} {{ $item['satuan'] }}
                    (Rp{{ number_format($item['sellingprice'], 0, ',', '.') }})
                </li>
                <form method="POST" action="{{ route('notajualscart.delete', ['id' => $key]) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('notajuals.store') }}">
    @csrf
    <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">

    @php
        $cart = session('cart', []);
    @endphp

    @if (!empty($cart))
        @foreach ($cart as $id => $item)
            <input type="hidden" name="tgl_kadaluarsa[]" value="{{ $item['tgl_kadaluarsa'] }}">
            <input type="hidden" name="id[]" value="{{ $item['id'] }}">
            <input type="hidden" name="quantity[]" value="{{ $item['quantity'] }}">
            <input type="hidden" name="sellingprice[]" value="{{ $item['sellingprice'] }}">
            <input type="hidden" name="distributors_id[]" value="{{ $item['distributors_id'] }}">
        @endforeach

        <button class="btn btn-primary mt-3">Simpan Penjualan</button>
    @endif
</form>
@endsection --}}
@extends('layout.conquer')

@section('title', 'Halaman Penjualan Produk')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Halaman Penjualan Produk</h1>

    <!-- Search -->
    <form method="GET" action="{{ route('notajuals.create') }}" class="mb-4">
        <input type="text" name="search" value="{{ $search }}" class="form-control mb-2" placeholder="Cari Produk...">
        <button class="btn btn-primary">Cari</button>
    </form>

    <!-- Pegawai Info -->
    <div class="mb-4">
        <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">
        <p>Pegawai: {{ auth()->user()->nama }}</p>
    </div>

    <!-- Produk List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($prod as $p)
            <div class="border p-4 rounded shadow">
                <h4 class="font-semibold">
                    {{ $p->nama }}
                    @if (isset($p->is_parcel))
                        <span class="badge bg-info">Parcel</span>
                    @else
                        <small>(Kadaluarsa: {{ $p->tgl_kadaluarsa }})</small>
                    @endif
                </h4>
                <p>Harga: Rp{{ number_format($p->sellingprice, 0, ',', '.') }}</p>
                <p>Stok: {{ $p->stok }}</p>

                <!-- Add to Cart -->
                <form method="POST" action="{{ route('notajuals.cart') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $p->id }}">
                    <input type="hidden" name="nama" value="{{ $p->nama }}">
                    <input type="hidden" name="satuan" value="{{ $p->satuan_nama }}">
                    <input type="hidden" name="diskon" value="{{ $p->diskon ?? 0}}">
                    <input type="hidden" name="sellingprice" value="{{ $p->sellingprice }}">
                    <input type="hidden" name="stok" value="{{ $p->stok }}">
                    <input type="hidden" name="tgl_kadaluarsa" value="{{ $p->tgl_kadaluarsa }}">
                    <input type="hidden" name="distributors_id" value="{{ $p->distributors_id }}">
                    <input type="hidden" name="is_parcel" value="{{ $p->is_parcel ?? false }}">
                    <input type="number" name="quantity" class="form-control mb-2" min="1"
                        max="{{ $p->stok }}" placeholder="Jumlah">
                    <button class="btn btn-success btn-sm">Tambah ke Keranjang</button>
                </form>
            </div>
        @endforeach
        <div class="mt-4">
            {{ $prod->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Cart Display -->
    @if (count($cart) > 0)
        <div class="mt-6 p-4 border border-primary rounded">
            <h4 class="font-bold mb-3">Keranjang</h4>
            <ul class="list-disc pl-5">
                @foreach ($cart as $key => $item)
                    <li>
                        {{ $item['nama'] }} - {{ $item['quantity'] }} {{ $item['satuan'] ?? '' }}
                        (Rp{{ number_format($item['sellingprice'], 0, ',', '.') }})
                        @if (empty($item['is_parcel']))
                            (Diskon {{ $item['diskon'] * 100 ?? 0}}%)
                        @endif
                        @if (!empty($item['is_parcel']))
                            <span class="badge bg-info">Parcel</span>
                            @if (!empty($item['diskon']))
                                (Diskon {{ $item['diskon'] * 100 ?? 0}}%)
                            @endif
                        @endif
                        <form method="POST" action="{{ route('notajualscart.delete', ['id' => $key]) }}"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm ml-2">Hapus</button>
                        </form>
                    </li>
                @endforeach
            </ul>

            <form method="POST" action="{{ route('notajuals.store') }}" class="mt-4">
                @csrf
                <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">
                @foreach ($cart as $item)
                    <input type="hidden" name="id[]" value="{{ $item['id'] }}">
                    <input type="hidden" name="quantity[]" value="{{ $item['quantity'] }}">
                    <input type="hidden" name="sellingprice[]" value="{{ $item['sellingprice'] }}">
                    <input type="hidden" name="diskon[]" value="{{ $item['diskon'] }}">
                    <input type="hidden" name="tgl_kadaluarsa[]" value="{{ $item['tgl_kadaluarsa'] ?? '' }}">
                    <input type="hidden" name="distributors_id[]" value="{{ $item['distributors_id'] ?? '' }}">
                    <input type="hidden" name="is_parcel[]" value="{{ $item['is_parcel'] ?? false }}">
                @endforeach
                <button class="btn btn-primary">Simpan Penjualan</button>
            </form>
        </div>
    @endif
@endsection
