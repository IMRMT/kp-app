@extends('layout.conquer')
@section('title')
@section('content')

<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Edit Detail Tipe User</h1>

<form method="POST" action="{{route('tipeusers.update', $datas->id)}}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="tipe">Nama Tipe User</label>
        <input type="text" class="form-control" name="tipe" aria-describedby="nameHelp"
            placeholder="Masukkan nama" value="{{$datas->tipe}}">
        <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
    </div>
    <div class="form-group">
            <label for="deskripsi">Deskripsi Tipe User</label>
            <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $datas->deskripsi) }}</textarea>
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="{{ route('tipeusers.index') }}" class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
</form>
@endsection