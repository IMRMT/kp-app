@extends('layout.conquer')

@section('title')

@section('content')
    @if ($errors->any()) untuk memunculkan error
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Halaman Pembelian Produk</h1>

    <!-- Search -->
    <form method="GET" action="{{ route('notabelis.create') }}" class="mb-4">
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
    <!-- Add New Product or Choose Existing -->
    <div class="card mb-4 p-4 border border-secondary">
        <h5 class="mb-3">Tambah Produk Baru atau Pilih Produk yang Sudah Ada</h5>

        <!-- Toggle between new or existing product -->
        <div class="form-group mb-3">
            <label for="modeSelector">Pilih Mode</label>
            <select id="modeSelector" class="form-control" onchange="toggleProductMode()">
                <option value="existing">Pilih Produk yang Sudah Ada</option>
                <option value="new">Tambah Produk Baru</option>
            </select>
        </div>

        <!-- Form for new product -->
        <form method="POST" action="{{ route('notabelis.beliProdukBaru') }}" id="newProductForm" style="display: none;">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">
            <div class="form-group">
                <label for="nama">Nama Produk</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="satuans">Satuan Produk</label>
                <select class="form-control" name="satuans">
                    @foreach ($satuans as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tipeproduks">Tipe Produk</label>
                <select class="form-control" name="tipeproduks">
                    @foreach ($tipeproduks as $tp)
                        <option value="{{ $tp->id }}">{{ $tp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="distributors">Distributor</label>
                <select name="distributors" class="form-control">
                    @foreach ($distributors as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="gudangs">Gudang Produk</label>
                <select class="form-control" name="gudangs">
                    @foreach ($gudangs as $g)
                        <option value="{{ $g->id }}">{{ $g->lokasi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status Produk</label>
                <select class="form-control" name="status" aria-describedby="nameHelp">
                    <option value="proses_order">Proses Order</option>
                    <option value="discontinued">Discontinued</option>
                    <option value="tersedia">Tersedia</option>
                </select>
                <small id="nameHelp" class="form-text text-muted">Mohon pilih input yang diinginkan.</small>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi Produk</label>
                <textarea class="form-control" name="deskripsi" rows="4" placeholder="Masukkan Deskripsi Produk"></textarea>
                <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
            </div>
            <div class="form-group">
                <label for="stok">Quantity</label>
                <input type="number" name="stok" class="form-control" required min="1">
            </div>
            <div class="form-group">
                <label for="unitprice">Harga Beli per Unit</label>
                <input type="number" name="unitprice" class="form-control" required min="0">
            </div>
            <div class="form-group">
                <label for="sellingprice">Harga Jual per Unit</label>
                <input type="number" name="sellingprice" class="form-control" required min="0">
            </div>
            <div class="form-group">
                <label for="tgl_kadaluarsa">Tanggal Kadaluarsa</label>
                <input type="date" name="tgl_kadaluarsa" class="form-control" value="{{ old('tgl_kadaluarsa', '') }}">
            </div>
            <button class="btn btn-success mt-3" type="submit">Simpan Produk Baru</button>
        </form>
    </div>

    <script>
        function toggleProductMode() {
            const mode = document.getElementById('modeSelector').value;
            document.getElementById('newProductForm').style.display = (mode === 'new') ? 'block' : 'none';
        }
    </script>

    <!-- Produk List -->
    <div id="produk-list">
        @foreach ($prod as $p)
            @php
                $cart = session('cart', []);
                $quantity = 0;
                foreach ($cart as $item) {
                    if ($item['id'] == $p->id) {
                        $quantity = $item['quantity'];
                    }
                }
            @endphp

            <div class="border p-4 rounded mb-4">
                <h4>{{ $p->nama }}</h4>
                <p>Stok Tersedia: {{ $p->total_stok ?? 0 }}</p>

                <form method="POST" action="{{ route('notabelis.cart') }}">
                    @csrf
                    <input type="hidden" name="produk_id" value="{{ $p->id }}">
                    <input type="hidden" name="nama" value="{{ $p->nama }}">

                    <div class="form-group">
                        <label for="unitprice">Harga Produk</label>
                        <input type="number" class="form-control" name="unitprice" required>
                    </div>

                    <div class="form-group">
                        <label for="tgl_kadaluarsa">Tanggal Kadaluarsa</label>
                        <input type="date" class="form-control" name="tgl_kadaluarsa"
                            value="{{ old('tgl_kadaluarsa', '') }}">
                    </div>

                    <div class="form-group">
                        <label for="distributors_id">Distributor Produk</label>
                        <select class="form-control" name="distributors_id" required>
                            @foreach ($distributors as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="satuans_id">Satuan Produk</label>
                        <select class="form-control" name="satuans_id" required>
                            @foreach ($satuans as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gudangs_id">Penyimpanan Produk</label>
                        <select class="form-control" name="gudangs_id" required>
                            @foreach ($gudangs as $g)
                                <option value="{{ $g->id }}">{{ $g->lokasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="number" name="quantity" value="{{ $quantity }}" min="0"
                        class="form-control mb-2" required> qty

                    <button class="btn btn-sm btn-success">Tambah ke Keranjang</button>
                </form>
            </div>
        @endforeach
        <div class="mt-4">
            {{ $prod->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Cart Display -->
    @if (session('cart'))
        <div class="card mb-4 p-3 border border-primary">
            <h5 class="mb-3">Keranjang Saat Ini</h5>
            <ul>
                @foreach (session('cart', []) as $key => $item)
                    @php
                        $satuan = \App\Models\Satuan::find($item['satuans_id']);
                    @endphp
                    <li>
                        {{ $item['nama'] }} - {{ $item['quantity'] }} {{ $satuan->nama ?? 'N/A' }}
                        (Rp{{ number_format($item['unitprice'], 0, ',', '.') }})
                        <br><small>Kadaluarsa:
                            @php
                                $tgl = $item['tgl_kadaluarsa'];
                            @endphp

                            @if (!$tgl || \Carbon\Carbon::parse($tgl)->isToday())
                                Tidak Ada
                            @else
                                {{ \Carbon\Carbon::parse($tgl)->format('d-m-Y') }}
                            @endif
                        </small>
                    </li>
                    <form method="POST" action="{{ route('notabeliscart.delete', ['id' => $key]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('notabelis.store') }}">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">

        @php
            $cart = session('cart', []);
        @endphp

        @if (!empty($cart))
            @foreach ($cart as $id => $item)
                <input type="hidden" name="tgl_kadaluarsa[]" value="{{ $item['tgl_kadaluarsa'] }}">
                <input type="hidden" name="produk_id[]" value="{{ $item['id'] }}">
                <input type="hidden" name="quantity[]" value="{{ $item['quantity'] }}">
                <input type="hidden" name="unitprice[]" value="{{ $item['unitprice'] }}">
                <input type="hidden" name="distributors_id[]" value="{{ $item['distributors_id'] }}">
                <input type="hidden" name="satuans_id[]" value="{{ $item['satuans_id'] }}">
                <input type="hidden" name="gudangs_id[]" value="{{ $item['gudangs_id'] }}">
            @endforeach

            <button class="btn btn-primary mt-3">Simpan Pembelian</button>
        @endif
    </form>
@endsection
