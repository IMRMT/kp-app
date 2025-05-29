@extends('layout.conquer')
@section('title')
@section('content')

<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Upload Logo Apotek</h1>

<div class="page-content">
    <h3 class="page-title">Upload Logo untuk Apotek {{ $profil->nama }}</h3>
    <div class="container">
        <form method="POST" enctype="multipart/form-data" action="{{ url('profiltoko/simpanImage') }}">
            @csrf
            <div class="form-group">
                <label for="exampleInputType">Pilih Logo</label>
                <input type="file" class="form-control" name="file_photo" />
                <input type="hidden" name='id' value="{{ $profil->id }}" />
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('profiltokos.index') }}" class="btn btn-secondary ml-2">Batal</a>
        </form>
    </div>
</div>
@endsection