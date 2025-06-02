@extends('layout.conquer')

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

            button {
                display: none !important;
            }
        }
    </style>

    <div class="container">

        <h1>Laporan Penjualan</h1>
        <!-- Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nota ID</th>
                    <th>ID Produk</th>
                    <th>Nama Produk</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->notajual->id ?? '-' }}</td>
                        <td>{{ $sale->produks_id ?? '-' }}</td>
                        <td>{{ $sale->produkbatches->produks->nama ?? '-' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total Penjualan</th>
                    <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
        <button onclick="window.print()" class="btn btn-primary mt-3">Print Laporan</button>
    </div>
@endsection
