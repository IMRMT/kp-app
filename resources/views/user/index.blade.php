@extends('layout.conquer')
@section('title')
@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Daftar User</h1>

    <div class="container">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari User..."
                    value="{{ $search }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </div>
        </form>

        <h2>User</h2>
        <p>User yang tercatat di apotek</p>

        <!-- Produk Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        @foreach ([
            'id' => 'ID User',
            'nama' => 'Nama User',
            'no_hp' => 'No HP',
            'email' => 'Email',
            'username' => 'Username',
            // 'password' => 'Password',
            'tipe_user' => 'Tipe User',
            'created_at' => 'Created Time',
            'updated_at' => 'Updated Time',
        ] as $column => $label)
                            <th>
                                <a
                                    href="{{ route('users.index', ['sort_by' => $column, 'sort_order' => $sortOrder == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}">
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
                            <td>{{ $d->nama }}</td>
                            <td>{{ $d->no_hp }}</td>
                            <td>{{ $d->email }}</td>
                            <td>{{ $d->username }}</td>
                            {{-- <td style="word-break: break-all;">{{ Str::limit($d->password, 30) }}</td> --}}
                            <td>{{ ucfirst($d->tipeuser->tipe) }}</td>
                            <td>{{ $d->created_at }}</td>
                            <td>{{ $d->updated_at }}</td>
                            <td>
                                <img height='100px' src="{{ asset('/user_image/' . $d->image) }}" /><br>
                                <a href="{{ url('user/uploadImage/' . $d->id) }}" class="btn btn-xs btn-default">Upload</a>
                            </td>
                            <td>
                                <a class="btn btn-warning" href="{{ route('users.edit', $d->id) }}">Edit</a>
                                <form method="POST" action="{{ route('users.destroy', $d->id) }}" style="display:inline;">
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
    </div>
@endsection
