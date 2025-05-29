<?php

namespace App\Http\Controllers;

use App\Models\TipeProduk;
use Illuminate\Http\Request;

class TipeProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        $datas = TipeProduk::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%$search%");
        })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(6)
            ->appends([
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]);

        return view('tipeproduk.index', compact('datas', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $satuans = TipeProduk::all();
        return view('tipeproduk.create', ['satuans' => $satuans]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]); //ini memberitahu bahwa kolom name itu perlu, agar tidak null
        $data = new TipeProduk();
        $data->nama = $request->get('nama');
        $data->save();

        // Type::create($request->all());
        return redirect('tipeproduks')->with('status', 'The new data has been inserted');
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
        // $objType = $type;
        // dd($type);
        $data = TipeProduk::find($id);
        // dd($data);
        // echo'masuk form edit';
        return view('tipeproduk.edit', ['datas' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = TipeProduk::find($id);
        $data->nama = $request->get('nama');
        $data->save();

        // Type::create($request->all());
        return redirect('tipeproduks')->with('status', 'The new data has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //if no contraint error, then delete data. Redirect to index after it.
            $deletedData = TipeProduk::find($id);
            $deletedData->delete();
            return redirect('tipeproduks')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('tipeproduks')->with('status', $msg);
        }
    }
}
