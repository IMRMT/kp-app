@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Komposisi Parcel</h1>

    <form method="GET" action="{{ route('parcels.komposisi', ['id' => $komposisi->id]) }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ $search }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </div>
        </div>
    </form>

    <div class="container">
        <h2>Komposisi Parcel: {{ $komposisi->nama }}</h2>
        <p>Daftar semua komposisi dari parcel ini</p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    @foreach ([
            'parcels_id' => 'Id Parcel',
            'produks_id' => 'Id Produk',
            'nama_produk' => 'Nama Produk',
            'quantity' => 'Quantity',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
        ] as $column => $label)
                        <th>
                            <a
                                href="{{ route('parcels.komposisi', [
                                    'id' => $komposisi->id,
                                    'sort_by' => $column,
                                    'sort_order' => $sortBy == $column && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    'search' => $search,
                                ]) }}">
                                {{ $label }}
                                @if ($sortBy == $column)
                                    {{ $sortOrder == 'asc' ? '▲' : '▼' }}
                                @endif
                            </a>
                        </th>
                    @endforeach
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $d)
                    <tr>
                        <td>{{ $d->parcels_id }}</td>
                        <td>{{ $d->produks_id }}</td>
                        <td>{{ $d->nama_produk }}</td>
                        <td>{{ $d->quantity }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ $d->updated_at }}</td>
                        <td>
                            <form method="POST"
                                action="{{ route('parcels.destroyKomposisi', [$d->parcels_id, $d->produks_id]) }}"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete {{ $d->nama_produk }} from this parcel?');">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $datas->links('pagination::bootstrap-5') }}
        </div>

        <a href="{{ route('parcels.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
