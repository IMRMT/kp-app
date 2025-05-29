<?php

namespace App\Http\Controllers;

use App\Models\Profiltoko;
use App\Models\User;
use Illuminate\Http\Request;

class ProfiltokoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profils = Profiltoko::first();
        
        // dd($profils);
        return view('profil.index', ['profil' => $profils]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $profils = Profiltoko::first();
        $users = User::all();
        return view('profil.create', ['profils' => $profils, 'user' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'email' => 'required',
            'deskripsi' => 'required',
            'jam_operasional' => 'required',
            'pemilik_id' => 'required',
        ]); //ini memberitahu bahwa kolom name itu perlu, agar tidak null
        $data = new Profiltoko();
        $data->nama = $request->get('nama');
        $data->alamat = $request->get('alamat');
        $data->no_hp = $request->get('no_hp');
        $data->email = $request->get('email');
        $data->deskripsi = $request->get('deskripsi');
        $data->jam_operasional = $request->get('jam_operasional');
        $data->pemilik_id = $request->pemilik_id;
        $data->save();

        // Type::create($request->all());
        return redirect('profiltokos')->with('status', 'The new data has been inserted');
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
        $profils = Profiltoko::find($id);
        $users = User::all();
        return view('profil.edit', ['profils' => $profils, 'user' => $users]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Profiltoko::find($id);
        $data->nama = $request->get('nama');
        $data->alamat = $request->get('alamat');
        $data->no_hp = $request->get('no_hp');
        $data->email = $request->get('email');
        $data->deskripsi = $request->get('deskripsi');
        $data->jam_operasional = $request->get('jam_operasional');
        $data->pemilik_id = $request->pemilik_id;
        $data->save();

        return redirect('profiltokos')->with('status', 'Profil telah diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            //if no contraint error, then delete data. Redirect to index after it.
            $deletedData = Profiltoko::find($id);
            $deletedData->delete();
            return redirect('Profiltokos')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('Profiltokos')->with('status', $msg);
        }
    }

    public function uploadImage(Request $request)
    {
        $id = $request->id;
        $profil = Profiltoko::find($id);
        return view('profil.formUploadImage', compact('profil'));
    }

    public function simpanImage(Request $request)
    {
        $file = $request->file("file_photo");
        $folder = 'company_logo';
        $filename = time() . "_" . $file->getClientOriginalName();
        $file->move($folder, $filename);
        $produk = Profiltoko::find($request->id);
        $produk->logo = $filename;
        $produk->save();
        return redirect()->route('profiltokos.index')->with('status', 'photo terupload');
    }
}
