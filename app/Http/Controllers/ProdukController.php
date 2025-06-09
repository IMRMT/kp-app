<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Gudang;
use App\Models\Satuan;
use App\Models\Produk;
use App\Models\Produkbatches;
use App\Models\Terimabatches;
use App\Models\TipeProduk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $produks = $this->getFilteredProduk($request);
        // dd($produks);
        return view('produk.index', [
            'datas' => $produks,
            'sortBy' => $request->get('sort_by', 'nama'),
            'sortOrder' => $request->get('sort_order', 'asc'),
            'search' => $request->get('search')
        ]);
    }

    public function batch(Request $request)
    {
        $id = $request->id;
        $data = Produk::findOrFail($id);

        $sortBy = $request->get('sort_by', 'nama');
        $sortOrder = $request->get('sort_order', 'asc');
        $search = $request->get('search');

        $query = Produkbatches::with(['produks', 'distributor', 'gudang', 'notaBeliProduks', 'terimaBatches'])
            ->where('produks_id', $id);
        // ->where('status', 'tersedia')
        // ->whereDate('tgl_kadaluarsa', '>', now());

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('stok', 'LIKE', "%$search%")
                    ->orWhere('unitprice', 'LIKE', "%$search%")
                    ->orWhere('diskon', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%")
                    ->orWhere('tgl_kadaluarsa', 'LIKE', "%$search%")
                    ->orWhere('tgl_datang', 'LIKE', "%$search%")
                    ->orWhere('created_at', 'LIKE', "%$search%")
                    ->orWhere('updated_at', 'LIKE', "%$search%")
                    ->orWhereHas('distributor', fn($q) => $q->where('nama', 'LIKE', "%$search%"))
                    ->orWhereHas('gudang', fn($q) => $q->where('lokasi', 'LIKE', "%$search%"));
            });
        }

        if (in_array($sortBy, ['stok', 'unitprice', 'diskon', 'tgl_kadaluarsa', 'tgl_datang', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $produks = $query->paginate(8);

        $expiredBatches = $produks->filter(function ($batch) {
            return $batch->tgl_kadaluarsa <= now() && $batch->tgl_kadaluarsa !== null && $batch->status === 'tersedia';
        });

        $expiredBatchList = null;
        if ($expiredBatches->isNotEmpty()) {
            $expiredBatchList = $expiredBatches->map(function ($b) {
                return "Batch ID: {$b->id} telah kadaluarsa, harap segera ganti status batch!";
            })->implode('\n');
        }

        return view('produk.batch', [
            'datas' => $produks,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'search' => $search,
            'produk' => $data,
            'expired_batches' => $expiredBatchList
        ]);
    }

    private function getFilteredProduk(Request $request)
    {
        $sortBy = $request->get('sort_by', 'nama');
        $sortOrder = $request->get('sort_order', 'asc');
        $search = $request->get('search');

        // Load produk with summed stok from batches
        $query = Produk::withSum(
            ['produkbatches as total_stok' => function ($q) {
                $q->where('status', 'tersedia')->where(function ($sub) {
                    $sub->whereDate('tgl_kadaluarsa', '>', now())
                        ->orWhereNull('tgl_kadaluarsa');
                });
            }],
            'stok'
        )
            ->with(['produkbatches' => function ($q) {
                $q->where('status', 'tersedia')->where(function ($sub) {
                    $sub->whereDate('tgl_kadaluarsa', '>', now())
                        ->orWhereNull('tgl_kadaluarsa');
                });
            }]);


        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                    ->orWhere('golongan', 'LIKE', "%$search%")
                    ->orWhere('deskripsi', 'LIKE', "%$search%")
                    ->orWhere('sellingprice', 'LIKE', "%$search%")
                    ->orWhereHas('produkbatches', function ($qb) use ($search) {
                        $qb->where('status', 'tersedia')
                            ->whereDate('tgl_kadaluarsa', '>', now())
                            ->where(function ($sub) use ($search) {
                                $sub->where('stok', 'LIKE', "%$search%")
                                    ->orWhere('unitprice', 'LIKE', "%$search%")
                                    ->orWhere('tgl_kadaluarsa', 'LIKE', "%$search%")
                                    ->orWhere('tgl_datang', 'LIKE', "%$search%")
                                    ->orWhere('created_at', 'LIKE', "%$search%")
                                    ->orWhere('updated_at', 'LIKE', "%$search%")
                                    ->orWhereHas('satuan', fn($q) => $q->where('nama', 'LIKE', "%$search%"))
                                    ->orWhereHas('distributor', fn($q) => $q->where('nama', 'LIKE', "%$search%"))
                                    ->orWhereHas('gudang', fn($q) => $q->where('lokasi', 'LIKE', "%$search%"));
                            });
                    });
            });
        }

        // Allow sorting only on selected fields
        if (in_array($sortBy, ['nama', 'sellingprice', 'golongan', 'deskripsi', 'total_stok'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate(8);
    }



    public function welcomeProduk(Request $request)
    {
        $produks = $this->getFilteredProduk($request);

        return view('welcome', [
            'datas' => $produks,
            'sortBy' => $request->get('sort_by', 'nama'),
            'sortOrder' => $request->get('sort_order', 'asc'),
            'search' => $request->get('search')
        ]);
    }

    public function homeProduk(Request $request)
    {
        $produks = $this->getFilteredProduk($request);

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Sales data
        $salesData = DB::table('notajuals_has_produks')
            ->join('produkbatches', 'produkbatches.id', '=', 'notajuals_has_produks.produkbatches_id')
            ->join('notajuals', 'notajuals.id', '=', 'notajuals_has_produks.notajuals_id')
            ->whereBetween('notajuals.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw("WEEK(notajuals.created_at, 1) - WEEK('$startOfMonth', 1) + 1 as week_number"),
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_rupiah')
            )
            ->groupBy('week_number')
            ->orderBy('week_number')
            ->get();

        $chartLabelsSales = $salesData->pluck('week_number')->map(fn($w) => 'Minggu ' . $w);
        $chartDataSales = $salesData->pluck('total_qty');
        $totalSalesRupiah = $salesData->sum('total_rupiah');

        // Purchase data (fixed typo on notabelis)
        $purchasesData = DB::table('notabelis_has_produks')
            ->join('produkbatches', 'produkbatches.id', '=', 'notabelis_has_produks.produkbatches_id')
            ->join('notabelis', 'notabelis.id', '=', 'notabelis_has_produks.notabelis_id')
            ->whereBetween('notabelis.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw("WEEK(notabelis.created_at, 1) - WEEK('$startOfMonth', 1) + 1 as week_number"),
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_rupiah')
            )
            ->groupBy('week_number')
            ->orderBy('week_number')
            ->get();

        $chartLabelsPurchases = $purchasesData->pluck('week_number')->map(fn($w) => 'Minggu ' . $w);
        $chartDataPurchases = $purchasesData->pluck('total_qty');
        $totalPurchasesRupiah = $purchasesData->sum('total_rupiah');

        return view('home', [
            'datas' => $produks,
            'sortBy' => $request->get('sort_by', 'nama'),
            'sortOrder' => $request->get('sort_order', 'asc'),
            'search' => $request->get('search'),
            'chartLabelsSales' => $chartLabelsSales,
            'chartDataSales' => $chartDataSales,
            'totalSalesRupiah' => $totalSalesRupiah,
            'chartLabelsPurchases' => $chartLabelsPurchases,
            'chartDataPurchases' => $chartDataPurchases,
            'totalPurchasesRupiah' => $totalPurchasesRupiah,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::all();
        $satuans = Satuan::all();
        $tipeproduks = TipeProduk::all();
        return view('produk.create', ['satuans' => $satuans, 'tipeproduks' => $tipeproduks, 'produks' => $produks]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'sellingprice' => 'required',
            'deskripsi' => 'required',
            'satuans' => 'required',
            'tipeproduks' => 'required',
        ]); //ini memberitahu bahwa kolom name itu perlu, agar tidak null
        $produk = new Produk();
        $produk->nama = $request->nama;
        $produk->sellingprice = $request->sellingprice;
        $produk->deskripsi = $request->deskripsi;
        $produk->satuans_id = $request->satuans;
        $produk->tipe_produks_id = $request->tipeproduks;
        $produk->save();


        // Type::create($request->all());
        return redirect('produks')->with('status', 'The new data has been inserted');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produk = Produk::findOrFail($id);

        // Get the latest available batch for this product
        $latestBatch = Produkbatches::where('produks_id', $id)
            ->where('status', 'tersedia')
            ->where(function ($q) {
                $q->whereDate('tgl_kadaluarsa', '>', now())
                    ->orWhereNull('tgl_kadaluarsa');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        $stok = Produkbatches::where('produks_id', $id)
            ->where('status', 'tersedia')
            ->where(function ($q) {
                $q->whereDate('tgl_kadaluarsa', '>', now())
                    ->orWhereNull('tgl_kadaluarsa');
            })
            ->sum('stok');

        $satuan = $latestBatch && $latestBatch->satuan ? $latestBatch->satuan->nama : 'Tidak tersedia';

        return view('pdetail', compact('produk', 'stok', 'satuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // $objType = $type;
        // dd($type);
        $data = Produk::find($id);
        $distributors = Distributor::all();
        $satuans = Satuan::all();
        $gudangs = Gudang::all();
        $tipeproduks = TipeProduk::all();
        // dd($data);
        // echo'masuk form edit';
        return view('produk.edit', ['datas' => $data, 'distributors' => $distributors, 'satuans' => $satuans, 'tipeproduks' => $tipeproduks, 'gudangs' => $gudangs]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Produk::find($id);
        $data->nama = $request->get('nama');
        $data->sellingprice = $request->get('sellingprice');
        $data->deskripsi = $request->get('deskripsi');
        $data->satuans_id = $request->get('satuans');
        $data->tipe_produks_id = $request->get('tipeproduks');
        $data->save();

        // Type::create($request->all());
        return redirect('produks')->with('status', 'The new data has been updated');
    }

    public function terimaBatch($id)
    {
        $data = Produkbatches::with(['notaBeliProduks', 'terimabatches'])->findOrFail($id);
        $users = User::all();
        $gudangs = Gudang::all();

        $qtyOrdered = $data->notaBeliProduks->sum('quantity');
        $qtyReceived = $data->terimaBatches->sum('stok');
        $qtyRemaining = $qtyOrdered - $qtyReceived;

        return view('produk.terimaBatch', [
            'datas' => $data,
            'gudangs' => $gudangs,
            'user' => $users,
            'qtyOrdered' => $qtyOrdered,
            'qtyReceived' => $qtyReceived,
            'qtyRemaining' => $qtyRemaining,
        ]);
    }

    public function updateTerimaBatch(Request $request, $id)
    {
        $batch = Produkbatches::with('notaBeliProduks')->findOrFail($id);

        $stokBaru = (int) $request->get('stok');
        $newGudangId = $request->get('gudangs');

        $qtyOrdered = $batch->notaBeliProduks->sum('quantity');
        $qtyReceived = $batch->terimaBatches->sum('stok');
        $qtyRemaining = $qtyOrdered - $qtyReceived;

        if ($stokBaru > $qtyRemaining) {
            return redirect()
                ->back()
                ->withInput()
                ->with('status', "Jumlah yang diterima ($stokBaru) melebihi sisa pesanan ($qtyRemaining).");
        }

        if ($batch->gudangs_id != $newGudangId) {
            // Create a new batch with the same data but a different gudang
            Produkbatches::create([
                'produks_id' => $batch->produks_id,
                'stok' => $stokBaru,
                'unitprice' => $batch->unitprice,
                'distributors_id' => $batch->distributors_id,
                'tgl_kadaluarsa' => $batch->tgl_kadaluarsa ?? null,
                'tgl_datang' => $request->get('tgl_datang'),
                'status' => 'tersedia',
                'gudangs_id' => $newGudangId,
            ]);
        } else {
            // Just update the current batch
            // $batch->increment('stok', $stokBaru);
            $batch->update([
                'stok' => $batch->stok + $stokBaru,
                'tgl_datang' => $request->get('tgl_datang'),
                'status' => 'tersedia',
            ]);
        }

        // dd($request->get('pegawai_id'));
        Terimabatches::create([
            'pegawai_id' => $request->get('pegawai_id'),
            'produkbatches_id' => $batch->id,
            'stok' => $stokBaru,
            'gudangs_id' => $newGudangId,
        ]);

        return redirect()->route('produks.batch', [
            'id' => $request->get('produks_id')
        ]);
    }


    public function editBatch($id)
    {
        // $objType = $type;
        // dd($type);
        // $data = Produkbatches::where('id', $id)
        //     ->first(); //sama laravel kalau cuma find bakal dianggap nama id itu"id" padahal harusnya "produks_id"
        $data = Produkbatches::find($id);
        $distributors = Distributor::all();
        $gudangs = Gudang::all();
        // dd($data);
        // echo'masuk form edit';
        return view('produk.editBatch', ['datas' => $data, 'distributors' => $distributors, 'gudangs' => $gudangs]);
    }

    public function updateBatch(Request $request, $id)
    {
        $data = Produkbatches::where('id', $id)
            ->update([
                'stok' => $request->get('stok'),
                'status' => $request->get('status'),
                'unitprice' => $request->get('unitprice'),
                'diskon' => $request->get('diskon'),
                'tgl_kadaluarsa' => $request->get('tgl_kadaluarsa') ?: null,
                'tgl_datang' => $request->get('tgl_datang'),
                'distributors_id' => $request->get('distributors'),
                'gudangs_id' => $request->get('gudangs'),
            ]);

        // dd($data);

        // Type::create($request->all());
        return redirect()->route('produks.batch', [
            'id' => $request->get('produks_id')
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //if no contraint error, then delete data. Redirect to index after it.
            $deletedData = Produk::find($id);
            $deletedData->delete();
            return redirect('produks')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('produks')->with('status', $msg);
        }
    }

    public function destroyBatch($id)
    {
        $batch = Produkbatches::where('id', $id)->first();
        // dd($id);
        try {
            $batch->delete(); // Delete the batch, not the produk!
            return redirect()->route('produks.batch', [
                'id' => $batch->produks_id
            ])->with('status', 'Batch deleted successfully!');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect()->route('produks.batch', [
                'id' => $batch->produks_id
            ])->with('status', $msg);
        }
    }

    public function destroyTerima($id)
    {
        // dd($id);
        $deletedData = TerimaBatches::find($id);
        try {
            //if no contraint error, then delete data. Redirect to index after it.
            $deletedData->delete();
            return redirect('daftarTerima')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('daftarTerima')->with('status', $msg);
        }
    }

    public function uploadImage(Request $request)
    {
        $id = $request->id;
        $produk = Produk::find($id);
        return view('produk.formUploadImage', compact('produk'));
    }

    public function simpanImage(Request $request)
    {
        $file = $request->file("file_photo");
        $folder = 'produk_image';
        $filename = time() . "_" . $file->getClientOriginalName();
        $file->move($folder, $filename);
        $produk = Produk::find($request->id);
        $produk->image = $filename;
        $produk->save();
        return redirect()->route('produks.index')->with('status', 'photo terupload');
    }

    public function daftarTerima(Request $request)
    {
        $query = Terimabatches::query()
            ->select(
                'terimabatches.*',
                'terimabatches.id as terima_id',
                'terimabatches.pegawai_id as pegawai_id',
                'produkbatches.id as batch_id',
                'produks.nama as nama_produk',
                'gudangs.lokasi as nama_gudang',
                'distributors.nama as nama_distributor',
                'users.nama as nama_pegawai'
            )
            ->join('users', 'terimabatches.pegawai_id', '=', 'users.id')
            ->join('produkbatches', 'terimabatches.produkbatches_id', '=', 'produkbatches.id')
            ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
            ->join('gudangs', 'produkbatches.gudangs_id', '=', 'gudangs.id')
            ->join('distributors', 'produkbatches.distributors_id', '=', 'distributors.id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('terimabatches.id', 'LIKE', "%$search%")
                    ->orWhere('produkbatches.id', 'LIKE', "%$search%")
                    ->orWhere('terimabatches.pegawai_id', 'LIKE', "%$search%")
                    ->orWhere('produks.nama', 'LIKE', "%$search%")
                    ->orWhere('distributors.nama', 'LIKE', "%$search%")
                    ->orWhere('gudangs.lokasi', 'LIKE', "%$search%")
                    ->orWhere('users.nama', 'LIKE', "%$search%")
                    ->orWhere('terimabatches.stok', 'LIKE', "%$search%")
                    ->orWhere('terimabatches.created_at', 'LIKE', "%$search%")
                    ->orWhere('terimabatches.updated_at', 'LIKE', "%$search%");
            });
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'id_batch':
                $query->orderBy('produkbatches.id', $sortOrder);
                break;
            case 'id_terima':
                $query->orderBy('terimabatches.id', $sortOrder);
                break;
            case 'id_pegawai':
                $query->orderBy('produkbatches.pegawai_id', $sortOrder);
                break;
            case 'nama_pegawai':
                $query->orderBy('users.nama', $sortOrder);
                break;
            case 'nama_produk':
                $query->orderBy('produks.nama', $sortOrder);
                break;
            case 'nama_gudang':
                $query->orderBy('gudangs.lokasi', $sortOrder);
                break;
            case 'nama_dist':
                $query->orderBy('distributors.nama', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
                break;
        }

        $datas = $query->paginate(10);

        return view('transaksi.daftarPenerimaan', [
            'datas' => $datas,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'search' => $search
        ]);
    }

    public function print($id)
    {
        $nota = Terimabatches::with(['user', 'produkbatches.produks', 'gudangs'])->findOrFail($id);

        // dd($nota);
        return view('transaksi.ntPrint', compact('nota'));
    }
}
