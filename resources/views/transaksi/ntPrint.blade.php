@extends('layout.conquer')

@section('title')

@section('content')
    <style>
        @media print {

            .page-sidebar-menu,
            .main-sidebar,
            .navbar,
            .footer,
            .page-sidebar-menu-collapse {
                display: none !important;
            }

            .content-wrapper,
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }

            body {
                overflow: visible !important;
            }
        }
    </style>

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Detail Nota Penerimaan</h1>

    <div class="container mt-4">
        <h2>Nota Penerimaan</h2>
        <p><strong>ID Nota:</strong> {{ $nota->id }}</p>
        <p><strong>Tanggal:</strong> {{ $nota->created_at->format('d M Y') }}</p>
        <p><strong>Pegawai:</strong> {{ $nota->user->nama }}</p>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Batch ID</th>
                    <th>Stok Diterima</th>
                    <th>Lokasi Gudang</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php
                        $namaProduk = optional(optional($nota->produkbatches)->produks)->nama ?? '-';
                        $lokasiGudang = optional($nota->gudangs)->lokasi ?? '-';
                    @endphp
                    <td>{{ $namaProduk }}</td>
                    <td>{{ $nota->produkbatches_id }}</td>
                    <td>{{ $nota->stok }}</td>
                    <td>{{ $lokasiGudang }}</td>
                </tr>
            </tbody>
        </table>

        <button onclick="window.print()" class="btn btn-primary mt-3">Print Nota</button>
    </div>
@endsection
