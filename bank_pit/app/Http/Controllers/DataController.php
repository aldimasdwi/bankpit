<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Data;


class DataController extends Controller
{
    public function dataadmin(Request $request)
    {
        $rol = 'admin';

        $users = User::where('role', $rol)->get();

        return response()->json(['data' => $users], 200);
    }

    public function datauser(Request $request)
    {
        $rol = 'user';

        $users = User::where('role', $rol)->get();

        return response()->json(['data' => $users], 200);
    }

    public function historyadmin($id)
    {
        $data = Data::where('id_user', $id)->get();
        return response()->json(['data' => $data], 200);
    }

    public function historyuser()
    {
        $id = Auth::id();
        $data = Data::where('id_user', $id)->get();
        return response()->json(['data' => $data], 200);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $results = Data::where(function ($query) use ($searchTerm) {
            $query->where('username', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('no_ref', 'LIKE', '%' . $searchTerm . '%');
        })->get();

        return response()->json(['searcha'=>$results], 200);
    }

 
}
