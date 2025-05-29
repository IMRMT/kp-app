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

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Buat Produk Baru</h1>

    <form method="POST" action="{{ route('produks.store') }}">
        @csrf
        <div class="form-group">
            <label for="nama">Nama Produk</label>
            <input type="text" class="form-control" name="nama" aria-describedby="nameHelp"
                placeholder="Masukkan Nama Produk">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="sellingprice">Harga Produk</label>
            <input type="number" class="form-control" name="sellingprice" aria-describedby="nameHelp"
                placeholder="Masukkan Harga Jual Produk">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Produk</label>
            <textarea class="form-control" name="deskripsi" rows="4" placeholder="Masukkan Deskripsi Produk"></textarea>
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
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
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{ route('produks.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </form>
@endsection
