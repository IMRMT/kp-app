@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if (!empty($expired_batches))
        <script>
            alert("Batch kadaluarsa ditemukan:\n\n{{ $expired_batches }}");
        </script>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Batch</h1>

    <form method="GET" action="{{ route('produks.batch', ['id' => $produk->id]) }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ $search }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </div>
        </div>
    </form>

    <div class="container">
        <h2>Batch Produk: {{ $produk->nama }}</h2>
        <p>Daftar semua batch dari produk ini</p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    @foreach ([
            'id' => 'Id Batch',
            'produks_id' => 'Id Produk',
            'stok' => 'Stok',
            'unitprice' => 'Harga',
            'diskon' => 'Diskon',
            'status' => 'Status',
            'distributor' => 'Distributor',
            'gudang' => 'Gudang',
            'tgl_datang' => 'Tanggal Datang',
            'tgl_kadaluarsa' => 'Tanggal Kadaluarsa',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
        ] as $column => $label)
                        <th>
                            <a
                                href="{{ route('produks.batch', [
                                    'id' => $produk->id,
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
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->produks_id }}</td>
                        <td>{{ $d->stok }}</td>
                        <td>{{ $d->unitprice }}</td>
                        <td>{{ $d->diskon ?? 0 }}</td>
                        <td>{{ ucfirst($d->status) }}</td>
                        <td>{{ $d->distributor->nama }}</td>
                        <td>{{ $d->gudang->lokasi }}</td>
                        <td>{{ $d->tgl_datang }}</td>
                        <td>{{ $d->tgl_kadaluarsa }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ $d->updated_at }}</td>
                        <td>
                            @php
                                $nota = $d->notaBeliProduks->first();
                                $totalReceived = $d->terimaBatches->sum('stok') ?? 0;
                            @endphp

                            @if ($nota && $totalReceived < $nota->quantity && ($d->tgl_kadaluarsa > now() || is_null($d->tgl_kadaluarsa)))
                                {{-- ini pengecekan apakah produk diterima sudah sama dengan produk dibeli --}}
                                <a class="btn" href="{{ route('produks.terimaBatch', [$d->id]) }}">Terima</a>
                            @endif
                            <a class="btn btn-warning" href="{{ route('produks.editBatch', [$d->id]) }}">Edit</a>
                            <form method="POST" action="{{ route('produks.destroyBatch', [$d->id]) }}"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete {{ $d->id }} ?');">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $datas->links('pagination::bootstrap-5') }}
        </div>

        <a href="{{ route('produks.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
