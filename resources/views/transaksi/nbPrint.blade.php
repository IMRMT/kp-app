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

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Detail Nota Pembelian</h1>

    @php
        // Group items by distributor ID
        $groupedByDistributor = $nota->notabeliproduks->groupBy(function ($item) {
            return $item->produkbatches->distributors_id;
        });
    @endphp

    <div class="container mt-4">
        <h2>Nota Pembelian</h2>
        <p><strong>ID Nota:</strong> {{ $nota->id }}</p>
        <p><strong>Tanggal:</strong> {{ $nota->created_at->format('d M Y') }}</p>
        <p><strong>Pegawai:</strong> {{ $nota->user->nama }}</p>

        @foreach ($groupedByDistributor as $distributorId => $items)
            @php
                $distributorName = $items->first()->produkbatches->distributor->nama ?? 'Distributor Tidak Diketahui';
                $grandTotal = 0;
            @endphp

            <hr class="my-5 print-split">
            <h4>Distributor: {{ $distributorName }}</h4>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        @php
                            $namaProduk = $item->produkbatches->produks->nama ?? '-';
                            $subtotal = $item->subtotal;
                            $grandTotal += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $namaProduk }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($subtotal / $item->quantity, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th>Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        @endforeach

        <button onclick="window.print()" class="btn btn-primary mt-3">Print Nota</button>
    </div>

    <style>
        @media print {
            .btn {
                display: none;
            }

            .print-split {
                page-break-before: always;
            }
        }
    </style>
@endsection
