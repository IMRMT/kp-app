<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Gudang;
use App\Models\Notabeli;
use App\Models\Notabeliproduk;
use App\Models\Produk;
use App\Models\Produkbatches;
use App\Models\Satuan;
use App\Models\TipeProduk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotabeliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notabeliproduk::query()
            ->select(
                'notabelis_has_produks.*',
                'produks.id as produks_id',
                'produks.nama as nama_produk',
                'distributors.id as distributors_id',
                'distributors.nama as nama_distributor',
                'users.nama as nama_pegawai'
            )
            ->join('notabelis', 'notabelis_has_produks.notabelis_id', '=', 'notabelis.id')
            ->join('users', 'notabelis.pegawai_id', '=', 'users.id')
            ->join('produkbatches', 'notabelis_has_produks.produkbatches_id', '=', 'produkbatches.id')
            ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
            ->join('distributors', 'produkbatches.distributors_id', '=', 'distributors.id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('produkbatches.id', 'LIKE', "%$search%")
                    ->orWhere('produks.nama', 'LIKE', "%$search%")
                    ->orWhere('distributors.nama', 'LIKE', "%$search%")
                    ->orWhere('users.nama', 'LIKE', "%$search%")
                    ->orWhere('notabelis_has_produks.quantity', 'LIKE', "%$search%")
                    ->orWhere('notabelis_has_produks.subtotal', 'LIKE', "%$search%")
                    ->orWhere('notabelis_has_produks.created_at', 'LIKE', "%$search%")
                    ->orWhere('notabelis_has_produks.updated_at', 'LIKE', "%$search%");
            });
        }

        $sortBy = $request->get('sort_by', 'notabelis_id');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'id_batch':
                $query->orderBy('produkbatches.id', $sortOrder);
                break;
            case 'nama_pegawai':
                $query->orderBy('users.nama', $sortOrder);
                break;
            case 'nama_produk':
                $query->orderBy('produks.nama', $sortOrder);
                break;
            case 'nama_dist':
                $query->orderBy('distributors.nama', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
                break;
        }

        $datas = $query->paginate(10);

        return view('transaksi.daftarPembelian', [
            'datas' => $datas,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'search' => $search
        ]);
    }

    public function report(Request $request)
    {
        $filter = $request->get('filter', 'day');

        // Base query: eager load related produk info
        $query = Notabeliproduk::with('notabeli', 'produkbatches.produks');

        // Grouping and filtering by created_at according to filter
        switch ($filter) {
            case 'week':
                $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
            case 'day':
            default:
                $query->whereDate('created_at', now()->toDateString());
        }

        $purchases = $query->get();

        // Calculate total sales (sum of subtotal)
        $total = $purchases->sum('subtotal');

        return view('transaksi.reportPembelian', compact('purchases', 'total', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // session()->forget('cart'); // Uncomment to reset cart on reload (optional)

        $search = $request->input('search');
        $cart = session('cart', []);

        // Load related dropdown data
        $distributors = Distributor::all();
        $satuans = Satuan::all();
        $tipeproduks = TipeProduk::all();
        $gudangs = Gudang::all();
        $notabelis = Notabeli::all();
        $users = User::all();

        // Main produk query (summed stock, one row per product)
        $produksQuery = DB::table('produks')
            ->select(
                'produks.id',
                'produks.nama',
                'produks.image',
                DB::raw('SUM(produkbatches.stok) as total_stok')
            )
            // ->leftJoin('produkbatches', 'produks.id', '=', 'produkbatches.produks_id')
            // ->where(function ($query) {
            //     $query->whereNull('produkbatches.id') // includes products with no batch
            //         ->orWhere(function ($q) {
            //             $q->whereDate('produkbatches.tgl_kadaluarsa', '>', Carbon::now())
            //                 ->where('produkbatches.status', '=', 'tersedia');
            //         });
            // })
            // ->groupBy('produks.id', 'produks.nama', 'produks.image');
            ->leftJoin('produkbatches', function ($join) {
                $join->on('produks.id', '=', 'produkbatches.produks_id')
                    ->where(function ($q) {
                        $q->whereDate('produkbatches.tgl_kadaluarsa', '>', Carbon::now())
                            ->orWhereNull('produkbatches.tgl_kadaluarsa');
                    })
                    ->where('produkbatches.status', '=', 'tersedia');
            })
            ->groupBy('produks.id', 'produks.nama', 'produks.image');

        // Apply search filter if provided
        if ($search) {
            $produksQuery->where('produks.nama', 'like', '%' . $search . '%');
        }

        // Get final produk result
        $produks = $produksQuery->paginate(2);

        // dd($produks);
        // Return to view
        return view('transaksi.beliProduk', [
            'distributors' => $distributors,
            'satuans' => $satuans,
            'tipeproduks' => $tipeproduks,
            'gudangs' => $gudangs,
            'data' => $notabelis,
            'prod' => $produks,
            'user' => $users,
            'search' => $search,
            'cart' => $cart,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->withErrors('Keranjang kosong.');
        }

        $nota = Notabeli::create([
            'pegawai_id' => $request->pegawai_id,
        ]);

        foreach ($cart as $cartKey => $item) {
            $produkId = $item['id'];

            $batch = Produkbatches::where('produks_id', $produkId)
                ->where('distributors_id', $item['distributors_id'])
                ->where('unitprice', $item['unitprice'])
                ->whereDate('tgl_kadaluarsa', $item['tgl_kadaluarsa'] ?? null)
                ->first();

            if ($batch) {
                // Increment stock if batch exists
                // $batch->increment('stok', $item['quantity']); //stok tidak diupdate dulu, menunggu surat terima
                $batch->update(['status' => 'proses_order']);
            } else {
                // Create new batch if it doesn't exist
                $batch = Produkbatches::create([
                    'produks_id' => $produkId,
                    // 'stok' => $item['quantity'],
                    'unitprice' => $item['unitprice'],
                    'distributors_id' => $item['distributors_id'],
                    'tgl_kadaluarsa' => $item['tgl_kadaluarsa'] ?? null,
                    'status' => 'proses_order',
                    'gudangs_id' => $item['gudangs_id'],
                ]);
            }

            // Save to pivot with new simplified foreign key
            Notabeliproduk::create([
                'notabelis_id' => $nota->id,
                'produkbatches_id' => $batch->id,
                'quantity' => $item['quantity'],
                'subtotal' => $item['quantity'] * $item['unitprice'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('transaksi')->with('status', 'Pembelian Tercatat');
    }



    public function beliProdukBaru(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'stok' => 'required',
            'unitprice' => 'required',
            'sellingprice' => 'required',
            'status' => 'required',
            'deskripsi' => 'required',
            'distributors' => 'required',
            'satuans' => 'required',
            'tipeproduks' => 'required',
            'gudangs' => 'required',
            'tgl_kadaluarsa' => 'nullable',
            'pegawai_id' => 'required',
        ]);

        // $produk = Produk::create([
        //     'nama' => $request->nama,
        //     'deskripsi' => $request->deskripsi,
        //     'image' => null, // image menunggu produk diterima
        //     'satuans_id' => $request->satuans,
        //     'gudangs_id' => $request->gudangs,
        // ]);
        $produk = new Produk();
        $produk->nama = $request->nama;
        $produk->sellingprice = $request->sellingprice;
        $produk->deskripsi = $request->deskripsi;
        $produk->satuans_id = $request->satuans;
        $produk->tipe_produks_id = $request->tipeproduks;
        $produk->save();


        $batch = Produkbatches::create([
            'produks_id' => $produk->id,
            // 'stok' => $request->stok,
            'unitprice' => $request->unitprice,
            'distributors_id' => $request->distributors,
            'tgl_kadaluarsa' => $request->tgl_kadaluarsa ?? null,
            'status' => $request->status,
            'gudangs_id' => $request->gudangs,
        ]);


        $nota = Notabeli::create([
            'pegawai_id' => $request->pegawai_id,
        ]);

        Notabeliproduk::create([
            'notabelis_id' => $nota->id,
            'produkbatches_id' => $batch->id,
            'quantity' => $request->stok,
            'subtotal' => $request->stok * $batch->unitprice,
        ]);

        return redirect()->route('transaksi')->with('status', 'Produk baru berhasil ditambahkan dan dicatat dalam nota pembelian.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deletedData = Notabeli::findOrFail($id);

            // Delete all related nota beli produks
            $deletedData->notaBeliProduks()->delete();

            // Delete the notabeli itself
            $deletedData->delete();
            return redirect('notabelis')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('notabelis')->with('status', $msg);
        }
    }

    public function addToCart(Request $request)
    {
        $cart = session()->get('cart', []);

        $id = $request->input('produk_id');
        if ($request->input('tgl_kadaluarsa') == date('d-m-Y')) {
            $tgl_kadaluarsa = null;
        }
        else
        $tgl_kadaluarsa = $request->input('tgl_kadaluarsa') ?: null;
        $unitprice = $request->input('unitprice');
        $quantity = (int) $request->input('quantity');

        $uniqueKey = implode('-', [
            $request->produk_id,
            $request->tgl_kadaluarsa,
            $request->distributors_id,
            $request->unitprice,
        ]);

        $produk = Produk::find($id);

        if (!$produk || $quantity < 1) {
            return redirect()->back()->with('error', 'Produk tidak valid atau jumlah tidak boleh nol.');
        }

        // Just use the produk ID as the key
        $cart[$uniqueKey] = [
            'id' => $id,
            'nama' => $produk->nama,
            'distributors_id' => $request->input('distributors_id'),
            'satuans_id' => $request->input('satuans_id'),
            'gudangs_id' => $request->input('gudangs_id'),
            'tgl_kadaluarsa' => $tgl_kadaluarsa,
            'unitprice' => $unitprice,
            'quantity' => $quantity,
        ];

        // dd($cart);
        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function deleteFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('status', 'Produk telah dibuang dari Cart');
    }

    public function print($id)
    {
        $nota = Notabeli::with(['user', 'notabeliproduks.produkbatches.produks'])->findOrFail($id);

        // dd($nota);
        return view('transaksi.nbPrint', compact('nota'));
    }
}
