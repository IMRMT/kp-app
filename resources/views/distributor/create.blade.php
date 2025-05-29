@extends('layout.conquer')
@section('title')
@section('content')

<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Buat Distributor Baru</h1>

<form method="POST" action="{{ route('distributors.store') }}">
    @csrf
    <div class="form-group">
        <label for="nama">Nama Distributor</label>
        <input type="text" class="form-control" name="nama" aria-describedby="nameHelp" placeholder="Masukkan nama">
        <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
    </div>
    <div class="form-group">
        <label for="alamat">Alamat Distributor</label>
        <input type="text" class="form-control" name="alamat" aria-describedby="nameHelp" placeholder="Masukkan alamat">
        <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
    </div>
    <div class="form-group">
        <label for="no_hp">No HP Distributor</label>
        <input type="text" class="form-control" name="no_hp" aria-describedby="nameHelp" placeholder="Masukkan no HP">
        <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="{{ route('distributors.index') }}" class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
</form>
@endsection