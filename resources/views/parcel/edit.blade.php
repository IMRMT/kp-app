@extends('layout.conquer')
@section('title')
@section('content')

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Ubah Parcel</h1>

    <form method="POST" action="{{ route('parcels.update', $datas->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama">Nama Parcel</label>
            <input type="text" class="form-control" name="nama" aria-describedby="nameHelp"
                placeholder="Masukkan Nama Parcel" value="{{ $datas->nama }}">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Parcel</label>
            <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $datas->deskripsi) }}</textarea>
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>
        <div class="form-group">
            <label for="biaya_packing">Biaya Packing</label>
            <input type="text" class="form-control" name="biaya_packing" aria-describedby="nameHelp"
                placeholder="Masukkan Biaya Packing Parcel" value="{{ $datas->biaya_packing }}">
            <small id="nameHelp" class="form-text text-muted">Mohon isikan dengan input yang diinginkan.</small>
        </div>

        <h4>Produk Komposisi</h4>
        <div id="produk-list">
            @forelse ($komposisi as $k)
                <div class="produk-item mb-2 d-flex align-items-center">
                    <input type="hidden" name="komposisi_id[]" value="{{ $k->id }}">
                    <select name="produks_id[]" class="form-select me-2">
                        @foreach ($produks as $produk)
                            <option value="{{ $produk->id }}" {{ $k->produks_id == $produk->id ? 'selected' : '' }}>
                                {{ $produk->nama }}
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="quantity[]" class="form-control me-2" value="{{ $k->quantity }}" required>
                </div>
            @empty
                <div class="produk-item mb-2 d-flex align-items-center">
                    <input type="hidden" name="komposisi_id[]" value="">
                    <select name="produks_id[]" class="form-select me-2">
                        @foreach ($produks as $produk)
                            <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="quantity[]" class="form-control me-2" value="" required>
                </div>
            @endforelse
        </div>

        <button type="button" onclick="addProduk()" class="btn btn-info mt-3 me-2">Tambah Produk</button>
        <button type="submit" class="btn btn-primary mt-3 me-2">Simpan</button>
        <a href="{{ route('parcels.index') }}"
            class="btn btn-primary bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
    </form>

    <script>
        function addProduk() {
            const item = document.querySelector('.produk-item');
            const clone = item.cloneNode(true);

            // Clear values in cloned inputs
            clone.querySelector('select').selectedIndex = 0;
            clone.querySelector('input[type=number]').value = '';
            const hiddenId = clone.querySelector('input[type=hidden]');
            if (hiddenId) hiddenId.value = '';

            document.getElementById('produk-list').appendChild(clone);
        }
    </script>

@endsection
