@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Parcel</h1>

    <a href="{{ route('parcels.create') }}" class="btn btn-primary mb-3">Create New Parcel</a>

    <div class="container">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('parcels.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari Parcel..."
                    value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <h2>Reporting</h2>
        <p>Report of currently available parcel</p>

        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach([
                        'id' => 'ID',
                        'nama' => 'Nama Parcel',
                        'biaya_packing' => 'Biaya Packing',
                        'deskripsi' => 'Deskripsi'
                    ] as $column => $label)
                        <th>
                            <a href="{{ route('parcels.index', ['sort_by' => $column, 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
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
                        <td>{{ $d->nama }}</td>
                        <td>{{ $d->biaya_packing }}</td>
                        <td>{{ $d->deskripsi }}</td>
                        <td>
                            <a href="{{ route('parcels.komposisi', $d->id) }}">Komposisi</a>
                            <a class="btn btn-warning" href="{{ route('parcels.edit', $d->id) }}">Edit</a>
                            <form method="POST" action="{{ route('parcels.destroy', $d->id) }}" style="display:inline;">
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
            {{ $datas->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
