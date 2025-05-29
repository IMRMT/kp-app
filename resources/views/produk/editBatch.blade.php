@extends('layout.conquer')
@section('title')
@section('content')

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Ubah Detail Batch</h1>

    <form method="POST" action="{{ route('produks.updateBatch', $datas->id) }}">
        @csrf
        @method('PUT')
        <input type="hidden" class="form-control" name="produks_id" aria-describedby="nameHelp"
            value="{{ $datas->produks_id }}">
        <div class="form-group">
            <label for="stok">Stok Obat</label>
            <input type="number" class="form-control" name="stok" aria-describedby="nameHelp"
                placeholder="Masukkan Stok Obat" value="{{ $datas->stok }}">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="unitprice">Harga Obat</label>
            <input type="number" class="form-control" name="unitprice" aria-describedby="nameHelp"
                placeholder="Masukkan Harga Obat" value="{{ $datas->unitprice }}">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="diskon">Diskon Obat</label>
            <input type="number" step="0.01" class="form-control" name="diskon" aria-describedby="nameHelp"
                placeholder="Masukkan Diskon Obat" value="{{ $datas->diskon }}">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="status">Status Obat</label>
            <select class="form-control" name="status" aria-describedby="nameHelp">
                <option value="proses_order" {{ $datas->status == 'tersedia' ? 'selected' : '' }}>Proses Order</option>
                <option value="discontinued" {{ $datas->status == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                <option value="tersedia" {{ $datas->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
            </select>
            <small id="nameHelp" class="form-text text-muted">Mohon pilih input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="distributors">Distributor Obat</label>
            <select class="form-control" name="distributors">
                @foreach ($distributors as $d)
                    <option value="{{ $d->id }}"{{ $d->id == $datas->distributors_id ? 'selected' : '' }}>
                        {{ $d->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="gudangs">Gudang Obat</label>
            <select class="form-control" name="gudangs">
                @foreach ($gudangs as $g)
                    <option value="{{ $g->id }}"{{ $g->id == $datas->gudangs_id ? 'selected' : '' }}>
                        {{ $g->lokasi }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tgl_datang">Tanggal Datang</label>
            <input type="date" class="form-control" name="tgl_datang" aria-describedby="dateHelp"
                value="{{ $datas->tgl_datang }}">
            <small id="dateHelp" class="form-text text-muted">Pilih tanggal datang produk.</small>
        </div>
        <div class="form-group">
            <label for="tgl_kadaluarsa">Tanggal Kadaluarsa</label>
            <input type="date" class="form-control" name="tgl_kadaluarsa" aria-describedby="dateHelp"
                value="{{ $datas->tgl_kadaluarsa }}">
            <small id="dateHelp" class="form-text text-muted">Pilih tanggal kadaluarsa produk.</small>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{ route('produks.batch', ['id' => $datas->produks_id]) }}"
            class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
    </form>
@endsection
