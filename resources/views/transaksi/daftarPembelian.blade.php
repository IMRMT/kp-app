@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Nota Pembelian</h1>

    <a href="{{ route('notabelis.create') }}" class="btn btn-primary mb-3">Create New Nota Pembelian</a>

    <div class="container">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('notabelis.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari notabeli..."
                    value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <form method="GET" action="{{ route('notabelis.report') }}">
            <label for="groupBy">Lihat Laporan Penjualan:</label>
            <select name="filter" id="groupBy" class="form-select w-auto d-inline mx-2">
                <option value="">-- Pilih Grup --</option>
                <option value="day">Hari Ini</option>
                <option value="week">Minggu Ini</option>
                <option value="month">Bulan Ini</option>
                <option value="year">Tahun Ini</option>
            </select>
            <button type="submit" class="btn btn-primary">Lihat</button>
        </form>

        <h2>Nota Beli</h2>
        <p>Daftar transaksi pembelian yang tercatat di apotek.</p>

        <!-- Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach ([
            'notabelis_id' => 'Nota ID',
            'pegawai_id' => 'ID Pegawai',
            'nama_pegawai' => 'Nama Pegawai',
            'id_batch' => 'ID Batch',
            'produks_id' => 'ID Produk',
            'nama_produk' => 'Nama Produk',
            'distributors_id' => 'ID Distributor',
            'nama_dist' => 'Nama Distributor',
            'satuan' => 'Satuan',
            'quantity' => 'Quantity',
            'subtotal' => 'Subtotal',
            'created_at' => 'Tanggal Transaksi',
            'updated_at' => 'Terakhir Diubah',
        ] as $column => $label)
                        <th>
                            <a
                                href="{{ route('notabelis.index', ['sort_by' => $column, 'sort_order' => $sortOrder == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}">
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
                        <td>{{ $d->notabeli->id ?? '-' }}</td>
                        <td>{{ $d->notabeli->pegawai_id ?? '-' }}</td>
                        <td>{{ $d->notabeli->user->nama ?? '-' }}</td>
                        <td>{{ $d->produkbatches->id ?? '-' }}</td>
                        <td>{{ $d->produks_id ?? '-' }}</td>{{-- ?? '-' -> supaya tetap bisa tampil walau null --}}
                        <td>{{ $d->nama_produk ?? '-' }}</td>
                        <td>{{ $d->distributors_id }}</td>
                        <td>{{ $d->nama_distributor ?? '-' }}</td>
                        <td>{{ $d->nama_satuan ?? '-' }}</td>
                        <td>{{ $d->quantity }}</td>
                        <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ $d->updated_at }}</td>
                        <td>
                            {{-- <a class="btn btn-warning" href="{{ route('notabelis.edit', $d->id) }}">Edit</a> --}}
                            <a href="{{ route('notabelis.print', $d->notabeli->id) }}" class="btn btn-secondary btn-sm"
                                target="_blank">
                                Cetak Nota
                            </a>
                            <form method="POST" action="{{ route('notabelis.destroy', $d->notabelis_id) }}"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete Nota {{ $d->notabelis_id }}?');">
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
