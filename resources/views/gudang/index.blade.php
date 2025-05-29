@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar Gudang</h1>

    <a href="{{ route('gudangs.create') }}" class="btn btn-primary mb-3">Create New Gudang</a>

    <div class="container">
        <!-- Search Form -->
        <form method="GET" action="{{ route('gudangs.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari lokasi gudang..." value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <h2>Gudang</h2>
        <p>Gudang yang tercatat</p>

        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach ([
                        'id' => 'Gudang ID',
                        'lokasi' => 'Lokasi Gudang'
                    ] as $column => $label)
                        <th>
                            <a href="{{ route('gudangs.index', [
                                'sort_by' => $column,
                                'sort_order' => $sortBy == $column && $sortOrder == 'asc' ? 'desc' : 'asc',
                                'search' => $search
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
                        <td>{{ $d->lokasi }}</td>
                        <td>
                            <a class="btn btn-warning" href="{{ route('gudangs.edit', $d->id) }}">Edit</a>
                            <form method="POST" action="{{ route('gudangs.destroy', $d->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger"
                                    onclick="return confirm('Are you sure to delete {{ $d->id }} - {{ $d->lokasi }} ?');">
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