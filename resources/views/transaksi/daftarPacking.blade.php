@extends('layout.conquer')
@section('title', 'Daftar Nota Penjualan Parcel')
@section('content')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Nota Penjualan Parcel</h1>

    <a href="{{ route('notajuals.create') }}" class="btn btn-primary mb-3">Create New Nota Penjualan</a>

    <div class="container">
        <form method="GET" action="{{ route('parcels.notaParcel') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari parcel..."
                    value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach ([
            'notajuals_id' => 'Nota ID',
            'parcels_id' => 'ID Parcel',
            'nama_parcel' => 'Nama Parcel',
            'nama_pegawai' => 'Nama Pegawai',
            'quantity' => 'Quantity',
            'subtotal' => 'Subtotal',
            'created_at' => 'Tanggal Transaksi',
            'updated_at' => 'Terakhir Diubah',
        ] as $column => $label)
                        <th>
                            <a
                                href="{{ route('parcels.notaParcel', ['sort_by' => $column, 'sort_order' => $sortOrder == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}">
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
                        <td>{{ $d->notajuals_id }}</td>
                        <td>{{ $d->parcels_id }}</td>
                        <td>{{ $d->nama_parcel }}</td>
                        <td>{{ $d->nama_pegawai }}</td>
                        <td>{{ $d->quantity }}</td>
                        <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ $d->updated_at }}</td>
                        <td>
                            <a href="{{ route('notajuals.print', $d->notajuals_id) }}" class="btn btn-secondary btn-sm"
                                target="_blank">Cetak Nota</a>
                            <form method="POST" action="{{ route('notajuals.destroy', $d->notajuals_id) }}"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete Nota {{ $d->notajuals_id }}?');">
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
