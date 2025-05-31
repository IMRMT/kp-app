<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //user() error karena 
        if (Auth::user()->tipeuser->tipe !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $sortBy = $request->get('sort_by', 'nama');  // Default to 'nama'
        $sortOrder = $request->get('sort_order', 'asc');  // Default to ascending
        $search = $request->get('search');              // Search query

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('nama', 'LIKE', "%$search%")
                    ->orWhere('no_hp', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('tipe_user', 'LIKE', "%$search%")
                    ->orWhere('username', 'LIKE', "%$search%")
                    ->orWhere('password', 'LIKE', "%$search%")
                    ->orWhere('created_at', 'LIKE', "%$search%")
                    ->orWhere('updated_at', 'LIKE', "%$search%");
            });
        }
        $users = $query->orderBy($sortBy, $sortOrder)->paginate(2);
        // dd($users);
        return view('user.index', [
            'datas' => $users,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'search' => $search
        ]);
    }

    public function detail(Request $request)
    {
        $sortBy = $request->get('sort_by', 'nama');  // Default to 'nama'
        $sortOrder = $request->get('sort_order', 'asc');  // Default to ascending
        $search = $request->get('search');              // Search query

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('nama', 'LIKE', "%$search%")
                    ->orWhere('no_hp', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('tipe_user', 'LIKE', "%$search%")
                    ->orWhere('username', 'LIKE', "%$search%")
                    ->orWhere('password', 'LIKE', "%$search%")
                    ->orWhere('created_at', 'LIKE', "%$search%")
                    ->orWhere('updated_at', 'LIKE', "%$search%");
            });
        }
        $users = $query->orderBy($sortBy, $sortOrder)->get();
        // dd($users);
        return view('user.profile', [
            'datas' => $users,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        // $objType = $type;
        // dd($type);
        $data = User::find($id);
        if (Auth::user()->tipeuser->tipe !== 'admin' && Auth::id() != $id) {
            abort(403, 'Unauthorized action.');
        }
        // dd($data);
        // echo'masuk form edit';
        return view('user.edit', ['datas' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->tipeuser->tipe !== 'admin' && Auth::id() != $id) {
            abort(403, 'Unauthorized action.');
        }
        $data = User::find($id);
        if ($data->tipe_user()->tipe === 'admin') {
            if (empty($request->get('password'))) {
                $data->nama = $request->get('nama');
                $data->no_hp = $request->get('no_hp');
                $data->email = $request->get('email');
                $data->username = $request->get('username');
                $data->tipe_user = $request->get('tipe_user');
            } else {
                $data->nama = $request->get('nama');
                $data->no_hp = $request->get('no_hp');
                $data->email = $request->get('email');
                $data->username = $request->get('username');
                $data->tipe_user = $request->get('tipe_user');
                $data->password = Hash::make($request->get('password'));
            }
        } else {
            $data->password = Hash::make($request->get('password'));
        }
        $data->save();

        // Type::create($request->all());
        if ($data->tipe_user()->tipe === 'admin') {
            return redirect('users')->with('status', 'The new data has been updated');
        } else {
            return view('user.profile')->with('status', 'The new data has been updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            //if no contraint error, then delete data. Redirect to index after it.
            $deletedData = User::find($id);
            $deletedData->delete();
            return redirect('users')->with('status', 'Horray ! Your data is successfully deleted !');
        } catch (\PDOException $ex) {
            // Failed to delete data, then show exception message
            $msg = "Failed to delete data ! Make sure there is no related data before deleting it";
            return redirect('users')->with('status', $msg);
        }
    }

    public function uploadImage(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        return view('user.formUploadImage', compact('user'));
    }

    public function simpanImage(Request $request)
    {
        $file = $request->file("file_photo");
        $folder = 'user_image';
        $filename = time() . "_" . $file->getClientOriginalName();
        $file->move($folder, $filename);
        $user = User::find($request->id);
        $user->image = $filename;
        $user->save();
        return redirect()->route('users.index')->with('status', 'photo terupload');
    }
}
