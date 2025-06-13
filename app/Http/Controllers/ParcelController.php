<?php

namespace App\Http\Controllers;

use App\Models\Notajualparcel;
use App\Models\Produk;
use App\Models\Parcel;
use App\Models\Parcelproduk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ParcelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = \App\Models\Parcel::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%");
            });
        }

        $query->orderBy($sortBy, $sortOrder);

        $datas = $query->paginate(10);

        return view('parcel.index', compact('datas', 'search', 'sortBy', 'sortOrder'));
    }

    public function notaParcel(Request $request)
    {
        $query = Notajualparcel::query()
            ->select(
                'notajuals_has_parcels.*',
                'parcels.id as parcels_id',
                'parcels.nama as nama_parcel',
                'users.nama as nama_pegawai'
            )
            ->join('notajuals', 'notajuals_has_parcels.notajuals_id', '=', 'notajuals.id')
            ->join('parcels', 'notajuals_has_parcels.parcels_id', '=', 'parcels.id')
            ->join('users', 'notajuals.pegawai_id', '=', 'users.id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('parcels.nama', 'LIKE', "%$search%")
                    ->orWhere('users.nama', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_parcels.quantity', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_parcels.subtotal', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_parcels.created_at', 'LIKE', "%$search%")
                    ->orWhere('notajuals_has_parcels.updated_at', 'LIKE', "%$search%");
            });
        }

        $sortBy = $request->get('sort_by', 'notajuals_id');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'nama_parcel':
                $query->orderBy('parcels.nama', $sortOrder);
                break;
            case 'nama_pegawai':
                $query->orderBy('users.nama', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
                break;
        }

        $datas = $query->paginate(10);

        return view('transaksi.daftarPacking', [
            'datas' => $datas,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'search' => $search
        ]);
    }

    public function komposisi(Request $request)
    {
        $id = $request->id;
        $data = Parcel::findOrFail($id);
        $query = Parcelproduk::query()
            ->select(
                'parcelproduks.*',
                'produks.id as produks_id',
                'produks.nama as nama_produk',
                'parcels.id as parcels_id',
                'parcels.nama as nama_parcel',
            )
            ->join('produks', 'parcelproduks.produks_id', '=', 'produks.id')
            ->join('parcels', 'parcelproduks.parcels_id', '=', 'parcels.id')
            ->where('parcelproduks.parcels_id', $id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('parcelproduks.parcels_id', 'LIKE', "%$search%")
                    ->orWhere('parcelproduks.produks_id', 'LIKE', "%$search%")
                    ->orWhere('produks.nama', 'LIKE', "%$search%")
                    ->orWhere('parcels.nama', 'LIKE', "%$search%")
                    ->orWhere('parcelproduks.quantity', 'LIKE', "%$search%")
                    ->orWhere('parcelproduks.created_at', 'LIKE', "%$search%")
                    ->orWhere('parcelproduks.updated_at', 'LIKE', "%$search%");
            });
        }

        $sortBy = $request->get('sort_by', 'parcelproduks.parcels_id');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'id_parcel':
                $query->orderBy('parcelproduks.parcels_id', $sortOrder);
                break;
            case 'id_produk':
                $query->orderBy('parcelproduks.produks_id', $sortOrder);
                break;
            case 'nama_produk':
                $query->orderBy('produks.nama', $sortOrder);
                break;
            case 'nama_parcel':
                $query->orderBy('parcels.nama', $sortOrder); //bisa ga kepakai di view
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
                break;
        }

        $datas = $query->paginate(8);
        // $a = GeneralModel::generateIDBatch(1);


        return view('parcel.komposisi', [
            'datas' => $datas,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'komposisi' => $data,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parcels = Parcel::all();
        $produks = Produk::all();
        return view('parcel.create', ['parcels' => $parcels, 'produks' => $produks]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'produks_id' => 'required|array',
            'quantity' => 'required|array',
        ]);

        // Create Parcel
        $parcel = new Parcel();
        $parcel->nama = $request->get('nama');
        $parcel->deskripsi = $request->get('deskripsi');
        $parcel->biaya_packing = $request->get('biaya_packing');
        $parcel->save();

        // Save compositions
        foreach ($request->produks_id as $index => $produk_id) {
            Parcelproduk::create([
                'parcels_id' => $parcel->id,
                'produks_id' => $produk_id,
                'quantity' => $request->quantity[$index],
            ]);
        }

        return redirect('parcels')->with('status', 'The new data has been inserted');
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
    public function edit($id)
    {
        $data = Parcel::findOrFail($id);
        $produks = Produk::all();

        // Load existing komposisi (parcelproduks)
        $komposisi = $data->parcelproduks()->with('produk')->get();

        return view('parcel.edit', [
            'datas' => $data,
            'produks' => $produks,
            'komposisi' => $komposisi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)
    // {
    //     $parcel = Parcel::findOrFail($id);
    //     $parcel->nama = $request->get('nama');
    //     $parcel->deskripsi = $request->get('deskripsi');
    //     $parcel->save();

    //     $produks_ids = $request->input('produks_id', []);
    //     $quantities = $request->input('quantity', []);
    //     $processed_ids = [];

    //     for ($i = 0; $i < count($produks_ids); $i++) {
    //         $produks_id = $produks_ids[$i];
    //         $quantity = $quantities[$i];

    //         // Avoid duplicate entries in form input
    //         if (in_array($produks_id, $processed_ids)) {
    //             continue;
    //         }
    //         $processed_ids[] = $produks_id;

    //         $existing = Parcelproduk::where('parcels_id', $parcel->id)
    //             ->where('produks_id', $produks_id)
    //             ->first();

    //         if ($existing) {
    //             $existing->quantity = $quantity;
    //             $existing->save();
    //         } else {
    //             Parcelproduk::create([
    //                 'parcels_id' => $parcel->id,
    //                 'produks_id' => $produks_id,
    //                 'quantity' => $quantity,
    //             ]);
    //         }
    //     }

    //     // HARD DELETE entries that are no longer in the form
    //     Parcelproduk::where('parcels_id', $parcel->id)
    //         ->whereNotIn('produks_id', $processed_ids)
    //         ->delete();

    //     return redirect('parcels')->with('status', 'Parcel dan komposisi berhasil diperbarui');
    // }

    public function update(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);
        $parcel->nama = $request->get('nama');
        $parcel->deskripsi = $request->get('deskripsi');
        $parcel->biaya_packing = $request->get('biaya_packing');
        $parcel->save();

        $produks_ids = $request->input('produks_id', []);
        $quantities = $request->input('quantity', []);

        $processed_produks_ids = [];

        for ($i = 0; $i < count($produks_ids); $i++) {
            $produks_id = $produks_ids[$i];
            $quantity = $quantities[$i];

            // Track processed produks_id for deletion filtering later
            $processed_produks_ids[] = $produks_id;

            // Check if the record exists even if soft-deleted
            $existing = DB::table('parcelproduks')
                ->where('parcels_id', $parcel->id)
                ->where('produks_id', $produks_id)
                ->first();

            if ($existing) {
                // Restore soft deleted by setting deleted_at = null and update quantity
                DB::table('parcelproduks')
                    ->where('parcels_id', $parcel->id)
                    ->where('produks_id', $produks_id)
                    ->update([
                        'deleted_at' => null,
                        'quantity' => $quantity,
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new record
                DB::table('parcelproduks')->insert([
                    'parcels_id' => $parcel->id,
                    'produks_id' => $produks_id,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Soft delete komposisi that are no longer in the form input
        DB::table('parcelproduks')
            ->where('parcels_id', $parcel->id)
            ->whereNotIn('produks_id', $processed_produks_ids)
            ->update(['deleted_at' => now(), 'updated_at' => now()]);

        return redirect('parcels')->with('status', 'Parcel dan komposisi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //if no contraint error, then delete data. Redirect to index after it.
            $deletedData = Parcel::find($id);
            $deletedData->delete();
            return redirect('parcels')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('parcels')->with('status', $msg);
        }
    }

    public function destroyKomposisi($parcels_id, $produks_id)
    {
        // First, check if the entry exists
        $komposisi = DB::table('parcelproduks')
            ->where('parcels_id', $parcels_id)
            ->where('produks_id', $produks_id)
            ->first();

        if (!$komposisi) {
            return redirect()->route('parcels.komposisi', ['id' => $parcels_id])
                ->with('status', 'Composition not found.');
        }

        try {
            // Soft delete manually (since Eloquent can't handle composite keys well)
            DB::table('parcelproduks')
                ->where('parcels_id', $parcels_id)
                ->where('produks_id', $produks_id)
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            return redirect()->route('parcels.komposisi', ['id' => $parcels_id])
                ->with('status', 'Composition successfully deleted!');
        } catch (\Throwable $ex) {
            return redirect()->route('parcels.komposisi', ['id' => $parcels_id])
                ->with('status', 'Failed to delete! Make sure there are no related records.');
        }
    }
}
