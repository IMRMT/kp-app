<?php

namespace App\Http\Controllers;

// use App\Models\GeneralModel;
use App\Models\Notajual;
use App\Models\Notajualproduk;
use App\Models\Produk;
use App\Models\Produkbatches;
use App\Models\Parcel;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotajualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $query = NotajualProduk::query()
        //     ->select('notajuals_has_produks.*')
        //     ->join('notajuals', 'notajuals_has_produks.notajuals_id', '=', 'notajuals.id')
        //     ->join('users', 'notajuals.pegawai_id', '=', 'users.id')
        //     ->join('produkbatches', function ($join) {
        //         $join->on('notajuals_has_produks.produk_batches_produks_id', '=', 'produkbatches.produks_id')
        //             ->on('notajuals_has_produks.produk_batches_distributors_id', '=', 'produkbatches.distributors_id');
        //     })
        //     ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
        //     ->join('distributors', 'produkbatches.distributors_id', '=', 'distributors.id');
        // 

        $query = NotajualProduk::query()
            ->select(
                'notajuals_has_produks.*',
                'notajuals.jenis_pembayaran as metodebayar',
                'produks.id as produks_id',
                'produks.nama as nama_produk',
                'distributors.id as distributors_id',
                'distributors.nama as nama_distributor',
                'users.nama as nama_pegawai'
            )
            ->join('notajuals', 'notajuals_has_produks.notajuals_id', '=', 'notajuals.id')
            ->join('users', 'notajuals.pegawai_id', '=', 'users.id')
            ->join('produkbatches', 'notajuals_has_produks.produkbatches_id', '=', 'produkbatches.id')
            ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
            ->join('distributors', 'produkbatches.distributors_id', '=', 'distributors.id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('produkbatches.id', 'LIKE', "%$search%")
                    ->orWhere('produks.nama', 'LIKE', "%$search%")
                    ->orWhere('distributors.nama', 'LIKE', "%$search%")
                    ->orWhere('users.nama', 'LIKE', "%$search%")
                    ->orWhere('notajuals.jenis_pembayaran', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_produks.quantity', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_produks.subtotal', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_produks.created_at', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_produks.updated_at', 'LIKE', "%$search%");
            });
        }

        $sortBy = $request->get('sort_by', 'notajuals_id');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'id_batch':
                $query->orderBy('produkbatches.id', $sortOrder);
                break;
            case 'nama_pegawai':
                $query->orderBy('users.nama', $sortOrder);
                break;
            case 'metodebayar':
                $query->orderBy('notajuals.jenis_pembayaran', $sortOrder);
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
        // $a = GeneralModel::generateIDBatch(1);


        return view('transaksi.daftarPenjualan', [
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
        $query = Notajualproduk::with('notajual', 'produkbatches.produks');

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

        $sales = $query->get();

        // Calculate total sales (sum of subtotal)
        $total = $sales->sum('subtotal');

        return view('transaksi.reportPenjualan', compact('sales', 'total', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create(Request $request)
    // {
    //     $search = $request->input('search');
    //     $cart = session('cart', []);

    //     $notajuals = Notajual::all();

    //     // Get all parcels with their ingredients and calculate total price
    //     $parcels = DB::table('parcels')
    //         ->select('parcels.id', 'parcels.nama', 'parcels.biaya_packing')
    //         ->get();

    //     foreach ($parcels as $parcel) {
    //         $details = DB::table('parcelproduks')
    //             ->join('produks', 'parcelproduks.produks_id', '=', 'produks.id')
    //             ->where('parcelproduks.parcels_id', $parcel->id)
    //             ->select('produks.sellingprice', 'parcelproduks.quantity')
    //             ->get();

    //         $totalKomponen = 0;
    //         foreach ($details as $d) {
    //             $totalKomponen += $d->sellingprice * $d->quantity;
    //         }

    //         $parcel->harga = $parcel->biaya_packing + $totalKomponen;
    //         $parcel->image = null;
    //         $parcel->satuan_nama = '-';
    //         $parcel->stok = 1; // default
    //         $parcel->sellingprice = $parcel->harga;
    //         $parcel->tgl_kadaluarsa = null;
    //         $parcel->distributors_id = null;
    //         $parcel->is_parcel = true;
    //     }

    //     // Regular product logic
    //     $produksQuery = DB::table('produkbatches')
    //         ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
    //         ->join('satuans', 'produkbatches.satuans_id', '=', 'satuans.id')
    //         ->where('produkbatches.status', '=', 'tersedia')
    //         ->where('produkbatches.stok', '>', 0)
    //         ->whereDate('produkbatches.tgl_kadaluarsa', '>', Carbon::now())
    //         ->whereNotNull('produkbatches.tgl_datang')
    //         ->select(
    //             'produks.id',
    //             'produks.nama',
    //             'produks.image',
    //             'satuans.nama as satuan_nama',
    //             DB::raw('SUM(produkbatches.stok) as stok'),
    //             DB::raw('MIN(produks.sellingprice) as sellingprice'),
    //             DB::raw('MIN(produkbatches.tgl_kadaluarsa) as tgl_kadaluarsa'),
    //             DB::raw('MIN(produkbatches.distributors_id) as distributors_id')
    //         )
    //         ->groupBy('produks.id', 'produks.nama', 'produks.image', 'satuan_nama');

    //     if ($search) {
    //         $produksQuery->where('produks.nama', 'like', '%' . $search . '%');
    //     }

    //     $produks = $produksQuery->get();

    //     // Merge regular products and parcel
    //     $produks = $produks->merge($parcels);

    //     $users = User::all();

    //     return view('transaksi.jualProduk', [
    //         'data' => $notajuals,
    //         'prod' => $produks,
    //         'user' => $users,
    //         'search' => $search,
    //         'cart' => $cart
    //     ]);
    // }

    public function create(Request $request)
    {
        // session()->forget('cart');
        $search = $request->input('search');
        $cart = session('cart', []);

        // Get all parcel products
        $parcels = DB::table('parcels')
            ->select('parcels.id', 'parcels.nama', 'parcels.biaya_packing')
            ->get();

        foreach ($parcels as $parcel) {
            // Get parcel details: ingredients and their quantities
            $details = DB::table('parcelproduks')
                ->join('produkbatches', 'parcelproduks.produks_id', '=', 'produkbatches.produks_id')
                ->where('parcelproduks.parcels_id', $parcel->id)
                ->where('produkbatches.status', 'tersedia')
                ->where('produkbatches.stok', '>', 0)
                ->where(function ($query) {
                    $query->whereDate('produkbatches.tgl_kadaluarsa', '>', Carbon::now())
                        ->orWhereNull('produkbatches.tgl_kadaluarsa');
                })
                ->select('parcelproduks.produks_id', 'parcelproduks.quantity', DB::raw('AVG(produkbatches.diskon) as diskon'), DB::raw('SUM(produkbatches.stok) as total_stok'))
                ->groupBy('parcelproduks.produks_id', 'parcelproduks.quantity')
                ->get();

            // Calculate parcel price
            $totalKomponen = 0;
            foreach ($details as $d) {
                // To get selling price, fetch from produks table separately
                $sellingPrice = DB::table('produks')->where('id', $d->produks_id)->value('sellingprice') ?? 0;
                $totalKomponen += (1 - $d->diskon) * ($sellingPrice * $d->quantity); //diskon dalam 0 koma
            }

            $totalDiskon = 0;
            $count = 0;
            foreach ($details as $d) {
                $totalDiskon += $d->diskon;
                $count++;
            }

            $parcel->harga = $parcel->biaya_packing + $totalKomponen;
            $parcel->image = null;
            $parcel->satuan_nama = '-';
            $parcel->sellingprice = $parcel->harga;
            $parcel->diskon = $count > 0 ? $totalDiskon / $count : 0;
            $parcel->tgl_kadaluarsa = null;
            $parcel->distributors_id = null;
            $parcel->is_parcel = true;

            // Calculate minimum stock available based on ingredients
            $minAvailable = PHP_INT_MAX;

            foreach ($details as $ingredient) {
                $required = $ingredient->quantity;
                $totalAvailable = $ingredient->total_stok ?? 0;

                if ($required > 0) {
                    $maxMakeable = intdiv($totalAvailable, $required);
                    $minAvailable = min($minAvailable, $maxMakeable);
                }
            }

            $parcel->stok = $minAvailable > 0 ? $minAvailable : 0;
        }

        // Regular products query (unchanged)
        $produksQuery = DB::table('produkbatches')
            ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
            ->join('satuans', 'produks.satuans_id', '=', 'satuans.id')
            ->where('produkbatches.status', '=', 'tersedia')
            ->where('produkbatches.stok', '>', 0)
            ->where(function ($query) {
                $query->whereDate('produkbatches.tgl_kadaluarsa', '>', Carbon::now())
                    ->orWhereNull('produkbatches.tgl_kadaluarsa');
            })
            ->whereNotNull('produkbatches.tgl_datang')
            ->select(
                'produks.id',
                'produks.nama',
                'produks.image',
                'satuans.nama as satuan_nama',
                'produkbatches.diskon as diskon',
                DB::raw('SUM(produkbatches.stok) as stok'),
                DB::raw('MIN(produks.sellingprice) as sellingprice'),
                DB::raw('MIN(produkbatches.tgl_kadaluarsa) as tgl_kadaluarsa'),
                DB::raw('MIN(produkbatches.distributors_id) as distributors_id')
            )
            ->groupBy('produks.id', 'produks.nama', 'produks.image', 'satuan_nama', 'diskon');

        if ($search) {
            $produksQuery->where('produks.nama', 'like', '%' . $search . '%');
        }

        $produks = $produksQuery->get();

        // Merge regular and parcel products
        $merged = $produks->merge($parcels);

        $page = request()->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $paginated = new LengthAwarePaginator(
            $merged->slice($offset, $perPage)->values(),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('transaksi.jualProduk', [
            'prod' => $paginated,
            'search' => $search,
            'cart' => $cart
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'pegawai_id' => 'required',
    //     ]);

    //     $cart = session('cart', []);

    //     if (empty($cart)) {
    //         return redirect()->back()->withErrors('Keranjang kosong.');
    //     }

    //     // Create main sale record
    //     $nota = Notajual::create([
    //         'pegawai_id' => $request->pegawai_id,
    //     ]);

    //     // Save each item from cart
    //     foreach ($cart as $id => $item) {
    //         $produkId = $item['id'];
    //         $quantityToSell = $item['quantity'];

    //         $batches = DB::table('produkbatches')
    //             ->join('produks', 'produkbatches.produks_id', '=', 'produks.id')
    //             ->select(
    //                 'produks.id as prod_id',
    //                 'produks.sellingprice as sellingprice',
    //                 'produkbatches.id as id',
    //                 'produkbatches.stok as stok',
    //                 'produkbatches.distributors_id as distributors_id',
    //                 'produkbatches.tgl_kadaluarsa as tgl_kadaluarsa',
    //             )
    //             ->where('produks_id', $produkId)
    //             ->where('status', 'tersedia')
    //             ->whereDate('tgl_kadaluarsa', '>', now())
    //             ->orderBy('tgl_kadaluarsa', 'asc')
    //             ->get();

    //         foreach ($batches as $batch) {
    //             if ($quantityToSell <= 0) break;

    //             $qtyFromThisBatch = min($quantityToSell, $batch->stok);
    //             if ($qtyFromThisBatch <= 0) continue;

    //             Notajualproduk::create([
    //                 'notajuals_id' => $nota->id,
    //                 'produkbatches_id' => $batch->id,
    //                 'quantity' => $qtyFromThisBatch,
    //                 'unitprice' => $batch->sellingprice,
    //                 'subtotal' => $qtyFromThisBatch * $batch->sellingprice,
    //             ]);

    //             // Reduce stock
    //             DB::table('produkbatches')
    //                 ->where('produks_id', $batch->prod_id)
    //                 ->where('distributors_id', $batch->distributors_id)
    //                 ->whereDate('tgl_kadaluarsa', $batch->tgl_kadaluarsa)
    //                 ->decrement('stok', $qtyFromThisBatch);

    //             $quantityToSell -= $qtyFromThisBatch;
    //         }

    //         if ($quantityToSell > 0) {
    //             // Not enough stock, optionally roll back or notify
    //             return redirect()->back()->withErrors("Stok untuk produk {$item['nama']} tidak mencukupi.");
    //         }

    //         // Clear cart
    //     }
    //     session()->forget('cart');

    //     return redirect()->route('transaksi')->with('status', 'Penjualan Tercatat');
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $pegawaiId = $request->input('pegawai_id');
            $metodeBayar = $request->input('metodebayar');

            $notajual = Notajual::create([
                'pegawai_id' => $pegawaiId,
                'jenis_pembayaran' => $metodeBayar,
            ]);

            $ids = $request->input('id');
            $quantities = $request->input('quantity');
            $isParcelFlags = $request->input('is_parcel', []);

            // dd($isParcelFlags, $ids, $quantities);

            foreach ($ids as $i => $produkId) {
                $jumlah = $quantities[$i];
                $isParcel = $isParcelFlags[$i] == '1';

                if ($isParcel) {
                    // ==== RACIKAN ====
                    $parcel = Parcel::with('produks')->findOrFail($produkId);
                    // dd($parcel->toArray());
                    $totalHarga = 0;

                    foreach ($parcel->produks as $produk) {
                        $totalQty = $produk->pivot->quantity * $jumlah;
                        $sisa = $totalQty;

                        $batches = Produkbatches::where('produks_id', $produk->id)
                            ->where('stok', '>', 0)
                            ->where('status', 'tersedia')
                            ->where(function ($query) {
                                $query->whereDate('tgl_kadaluarsa', '>', now())
                                    ->orWhereNull('tgl_kadaluarsa');
                            })
                            ->orderBy('tgl_kadaluarsa')
                            ->get();

                        foreach ($batches as $batch) {
                            if ($sisa <= 0) break;

                            $terjual = min($sisa, $batch->stok);
                            $batch->decrement('stok', $terjual);

                            DB::table('notajuals_has_produks')->insert([
                                'notajuals_id' => $notajual->id,
                                'produkbatches_id' => $batch->id,
                                'quantity' => $terjual,
                                'subtotal' => (1 - $batch->diskon) * ($terjual * $produk->sellingprice),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            $totalHarga += (1 - $batch->diskon) * ($terjual * $produk->sellingprice);
                            $sisa -= $terjual;
                        }
                    }

                    $totalHarga += $parcel->biaya_packing;

                    DB::table('notajuals_has_parcels')->insert([
                        'notajuals_id' => $notajual->id,
                        'parcels_id' => $parcel->id,
                        'quantity' => $jumlah,
                        'subtotal' => $totalHarga,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // ==== REGULAR PRODUK ====
                    $sisa = $jumlah;

                    $batches = Produkbatches::where('produks_id', $produkId)
                        ->where('stok', '>', 0)
                        ->where('status', 'tersedia')
                        ->where(function ($query) {
                            $query->whereDate('tgl_kadaluarsa', '>', now())
                                ->orWhereNull('tgl_kadaluarsa');
                        })
                        ->orderBy('tgl_kadaluarsa')
                        ->get();

                    $produk = \App\Models\Produk::findOrFail($produkId);

                    foreach ($batches as $batch) {
                        if ($sisa <= 0) break;

                        $terjual = min($sisa, $batch->stok);
                        $batch->decrement('stok', $terjual);

                        DB::table('notajuals_has_produks')->insert([
                            'notajuals_id' => $notajual->id,
                            'produkbatches_id' => $batch->id,
                            'quantity' => $terjual,
                            'subtotal' => (1 - $batch->diskon) * ($terjual * $produk->sellingprice),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $sisa -= $terjual;
                    }
                }
            }

            // ==== RACIKAN CART ====
            $parcelCart = session('parcel_cart', []);
            foreach ($parcelCart as $item) {
                $parcelId = $item['parcel_id'];
                $jumlah = $item['jumlah'];
                $parcel = Parcel::with('produks')->findOrFail($parcelId);
                $totalHarga = 0;

                foreach ($parcel->produks as $produk) {
                    $totalQty = $produk->pivot->quantity * $jumlah;
                    $sisa = $totalQty;

                    $batches = Produkbatches::where('produks_id', $produk->id)
                        ->where('stok', '>', 0)
                        ->where('status', 'tersedia')
                        ->whereDate('tgl_kadaluarsa', '>', now())
                        ->orderBy('tgl_kadaluarsa')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($sisa <= 0) break;

                        $terjual = min($sisa, $batch->stok);
                        $batch->decrement('stok', $terjual);

                        DB::table('notajuals_has_produks')->insert([
                            'notajuals_id' => $notajual->id,
                            'produkbatches_id' => $batch->id,
                            'quantity' => $terjual,
                            'subtotal' => (1 - $batch->diskon) * ($terjual * $produk->sellingprice),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $totalHarga += (1 - $batch->diskon) * ($terjual * $produk->sellingprice);
                        $sisa -= $terjual;
                    }
                }

                $totalHarga += $parcel->biaya_packing;

                DB::table('notajuals_has_parcels')->insert([
                    'notajuals_id' => $notajual->id,
                    'parcels_id' => $parcelId,
                    'quantity' => $jumlah,
                    'subtotal' => $totalHarga,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            session()->forget(['cart', 'parcel_cart']);
            DB::commit();

            return redirect()->route('notajuals.index')->with('success', 'Nota jual berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
            $deletedData = Notajual::findOrFail($id);

            // Delete all related nota jual produks
            $deletedData->notaJualProduks()->delete();

            // Delete the notajual itself
            $deletedData->delete();
            return redirect('notajuals')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('notajuals')->with('status', $msg);
        }
    }

    // public function addToCart(Request $request)
    // {
    //     $cart = session()->get('cart', []);

    //     $id = $request->input('produkbatches_id');
    //     $tgl_kadaluarsa = $request->input('tgl_kadaluarsa');
    //     $quantity = (int) $request->input('quantity');

    //     $produk = Produk::find($id);

    //     if (!$produk || $quantity < 1) {
    //         return redirect()->back()->with('error', 'Produk tidak valid atau jumlah tidak boleh nol.');
    //     }

    //     $cartKey = $id . '_' . $tgl_kadaluarsa;
    //     // Just use the produk ID as the key
    //     $cart[$cartKey] = [
    //         'id' => $id,
    //         'nama' => $produk->nama,
    //         'distributors_id' => $request->input('distributors_id'),
    //         'tgl_kadaluarsa' => $tgl_kadaluarsa,
    //         'sellingprice' => $request->input('sellingprice'),
    //         'satuan' => $request->input('satuan'),
    //         'quantity' => $quantity,
    //     ];

    //     session(['cart' => $cart]);

    //     return redirect()->route('notajuals.create')->with('success', 'Produk ditambahkan ke keranjang.');
    // }
    public function addToCart(Request $request)
    {
        $cart = session()->get('cart', []);

        $id = $request->input('id');
        $isParcel = $request->input('is_parcel') == true || $request->input('is_parcel') == 'true';

        // Use unique key for parcel or normal product
        $key = $isParcel ? 'parcel_' . $id : $id;

        $cart[$key] = [
            'id' => $id,
            'nama' => $request->input('nama'),
            'satuan' => $request->input('satuan'),
            'diskon' => $request->input('diskon'),
            'sellingprice' => $request->input('sellingprice'),
            'stok' => $request->input('stok'),
            'tgl_kadaluarsa' => $request->input('tgl_kadaluarsa') ?? 0,
            'distributors_id' => $request->input('distributors_id'),
            'quantity' => $request->input('quantity'),
            'is_parcel' => $isParcel,
        ];

        session()->put('cart', $cart);

        return redirect()->route('notajuals.create')->with('success', 'Item added to cart.');
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
        $nota = Notajual::with(['user', 'notajualproduks.produkbatches.produks', 'notajualparcels.parcel'])->findOrFail($id);

        // dd($nota);
        return view('transaksi.njPrint', compact('nota'));
    }
}
