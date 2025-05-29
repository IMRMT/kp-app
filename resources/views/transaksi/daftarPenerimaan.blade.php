@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Nota Penerimaan</h1>

    <div class="container">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('produks.daftarTerima') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari notajual..."
                    value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <h2>Nota Penerimaan</h2>
        <p>Daftar transaksi penerimaan batch yang tercatat di apotek.</p>

        <!-- Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach ([
            'id_terima' => 'Nota ID',
            'pegawai_id' => 'ID Pegawai',
            'nama_pegawai' => 'Nama Pegawai',
            'id_batch' => 'ID Batch',
            'nama_produk' => 'Nama Produk',
            'nama_dist' => 'Nama Distributor',
            'nama_gudang' => 'Lokasi Gudang',
            'stok' => 'Stok Masuk',
            'created_at' => 'Tanggal Transaksi',
            'updated_at' => 'Terakhir Diubah',
        ] as $column => $label)
                        <th>
                            <a
                                href="{{ route('produks.daftarTerima', ['sort_by' => $column, 'sort_order' => $sortOrder == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}">
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
                        <td>{{ $d->id ?? '-' }}</td>
                        <td>{{ $d->pegawai_id ?? '-' }}</td>
                        <td>{{ $d->user->nama ?? '-' }}</td>
                        <td>{{ $d->produkbatches->id ?? '-' }}</td>
                        <td>{{ $d->nama_produk ?? '-' }}</td>
                        <td>{{ $d->nama_distributor ?? '-' }}</td>
                        <td>{{ $d->nama_gudang ?? '-' }}</td>
                        <td>{{ $d->stok }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ $d->updated_at }}</td>
                        <td>
                            {{-- <a class="btn btn-warning" href="{{ route('notajuals.edit', $d->id) }}">Edit</a> --}}
                            <a href="{{ route('produks.print', $d->id) }}" class="btn btn-secondary btn-sm"
                                target="_blank">
                                Cetak Nota
                            </a>
                            <form method="POST" action="{{ route('produks.destroyTerima', $d->id) }}"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete Nota {{ $d->id }}?');">
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
