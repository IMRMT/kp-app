@extends('layout.conquer')
@section('title')
@section('content')

<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Upload Image User</h1>
<div class="page-content">
    <h3 class="page-title">Upload Image untuk User {{ $user->nama }}</h3>
    <div class="container">
        <form method="POST" enctype="multipart/form-data" action="{{ url('user/simpanImage') }}">
            @csrf
            <div class="form-group">
                <label for="exampleInputType">Pilih Image</label>
                <input type="file" class="form-control" name="file_photo" />
                <input type="hidden" name='id' value="{{ $user->id }}" />
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('users.index') }}" class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
        </form>
    </div>
</div>
@endsection