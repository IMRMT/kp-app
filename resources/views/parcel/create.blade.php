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

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Buat Parcel Baru</h1>

    <form action="{{ route('parcels.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nama">Nama Parcel</label>
            <input type="text" class="form-control" name="nama" aria-describedby="nameHelp"
                placeholder="Masukkan Nama Parcel">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Parcel</label>
            <textarea class="form-control" name="deskripsi" rows="4" placeholder="Masukkan Deskripsi Parcel"></textarea>
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <h4>Produk Komposisi</h4>
        <div id="produk-list">
            <div class="produk-item">
                <select name="produks_id[]">
                    @foreach ($produks as $produk)
                        <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                    @endforeach
                </select>
                <input type="number" name="quantity[]" placeholder="Jumlah" required>
            </div>
        </div>
        <button type="button" onclick="addProduk()" class="btn btn-info mt-3 me-2">Tambah Produk</button>
        <button type="submit" class="btn btn-primary mt-3 me-2">Simpan</button>
        <a href="{{ route('parcels.index') }}"
            class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
    </form>

    <script>
        function addProduk() {
            const item = document.querySelector('.produk-item');
            const clone = item.cloneNode(true);
            document.getElementById('produk-list').appendChild(clone);
        }
    </script>
@endsection
