<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Saldo;
use App\Models\Data;
use App\Models\History;
use App\Models\User;


class SaldoController extends Controller
{
    public function ceksaldo()
    {
        $id = Auth::user()->id;
        $user = Saldo::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['saldo' => $user->saldo], 200);
    }

    public function isisaldo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|max:255',
            'saldo' => ['required', 'regex:/^(\d{1,20}(\.\d{1,2})?|\d{21})$/'],
        ], [
            'saldo.regex' => 'Saldo tidak valid atau ada simbol yang di masuk an. Maksimum 20 digit sebelum tanda desimal.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $id = Auth::user()->id;

        $saldo = Saldo::where('id', $id)->first();
        $user = Auth::user();

        if (!$saldo) {
            return response()->json(['error' => 'Saldo not found'], 404);
        }

        if ($user->pin === $request->pin) {
            $tambah = $saldo->saldo + $request->saldo;
            $saldo->saldo = $tambah;
            $saldo->save();

            $data = Data::create([
                'no_ref' => '#' . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99),
                'keterangan' => 'Isi Saldo',
                'saldo' => $tambah,
                'username' => $saldo->username,
                'id_user' => $id,
                'waktu' => now()
            ]);

            return response()->json(['saldo' => $saldo->saldo, 'pesan' => 'Pin berhasil', 'History' => $data], 200);
        } else {
            return response()->json(['error' => 'Pin tidak sesuai'], 401);
        }
    }
}
