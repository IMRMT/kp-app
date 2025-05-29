@extends('layout.conquer')
@section('title')
@section('content')

<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Ubah Produk</h1>

<form method="POST" action="{{route('produks.update', $datas->id)}}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nama">Nama Produk</label>
        <input type="text" class="form-control" name="nama" aria-describedby="nameHelp"
            placeholder="Masukkan Nama Produk" value="{{$datas->nama}}">
        <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
    </div>
    <div class="form-group">
            <label for="sellingprice">Harga Produk</label>
            <input type="number" class="form-control" name="sellingprice" aria-describedby="nameHelp"
                placeholder="Masukkan Harga Jual Produk" value="{{$datas->sellingprice}}">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
    <div class="form-group">
        <label for="deskripsi">Deskripsi Produk</label>
        <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $datas->deskripsi) }}</textarea>
        <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
    </div>
    <div class="form-group">
        <label for="satuans">satuan Produk</label>
        <select class="form-control" name="satuans">
            @foreach ($satuans as $s)
                <option value="{{ $s->id }}"{{ $s->id == $datas->satuans_id ? 'selected' : '' }}>{{ $s->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
            <label for="tipeproduks">Tipe Produk</label>
            <select class="form-control" name="tipeproduks">
                @foreach ($tipeproduks as $tp)
                    <option value="{{ $tp->id }}"{{ $tp->id == $datas->tipe_produks_id ? 'selected' : '' }}>{{ $tp->nama }}</option>
                @endforeach
            </select>
        </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="{{ route('produks.index') }}" class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
</form>
@endsection