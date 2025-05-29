@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Produk</h1>

    <a href="{{ route('produks.create') }}" class="btn btn-primary mb-3">Create New Produk</a>

    <div class="container">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('produks.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari Produk..."
                    value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <h2>Produk</h2>
        <p>Produk yang tercatat di apotek</p>

        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach ([
            'id' => 'Produk ID',
            'nama' => 'Nama Produk',
            'stok' => 'Stok',
            'satuan' => 'Satuan',
            'sellingprice' => 'Harga Jual',
            'deskripsi' => 'Deskripsi',
            'tipeproduk' => 'Tipe Produk',
            'created_at' => 'Created Time',
            'updated_at' => 'Updated Time',
        ] as $column => $label)
                        <th>
                            <a
                                href="{{ route('produks.index', ['sort_by' => $column, 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}">
                                {{ $label }}
                                @if ($sortBy == $column)
                                    {{ $sortOrder == 'asc' ? '▲' : '▼' }}
                                @endif
                            </a>
                        </th>
                    @endforeach
                    <th>Image</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->nama ?? '-' }}</td>
                        <td>{{ $d->total_stok ?? '-' }}</td>
                        <td>{{ $d->satuan->nama ?? '-' }}</td>
                        <td>{{ $d->sellingprice ?? '-' }}</td>
                        <td>{{ $d->deskripsi ?? '-' }}</td>
                        <td>{{ $d->tipeproduk->nama ?? '-' }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ $d->updated_at }}</td>
                        <td>
                            <img height="100px" src="{{ asset('/produk_image/' . $d->image) }}" alt="Product Image" /><br>
                            <a href="{{ url('produk/uploadImage/' . $d->id) }}" class="btn btn-xs btn-default">Upload</a>
                        </td>
                        <td>
                            <a href="{{ route('produks.batch', $d->id) }}">Lihat Batch</a>
                            <a class="btn btn-warning" href="{{ route('produks.edit', $d->id) }}">Edit</a>
                            <form method="POST" action="{{ route('produks.destroy', $d->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete {{ $d->id }} - {{ $d->nama }} ?');">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $datas->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
