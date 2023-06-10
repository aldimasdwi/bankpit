<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Saldo;
use App\Models\User;
use App\Models\Data;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|max:255',
            'no_unique' => 'required|string|max:255',
            'saldo' => ['required', 'regex:/^(\d{1,20}(\.\d{1,2})?|\d{21})$/'],
        ], [
            'saldo.regex' => 'Saldo tidak valid atau ada simbol yang dimasukkan. Maksimum 20 digit sebelum tanda desimal.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pin = Auth::user()->pin;

        if ($pin === $request->pin) {
            $user = Auth::id();

            $saldo = Saldo::where('id_user', $user)->first();

            if (!$saldo) {
                return response()->json(['error' => 'Saldo tidak ditemukan'], 404);
            }

            $no_unique = $request->input('no_unique');

            $userr = User::where('no_unique', $no_unique)->first();

            if (!$userr) {
                return response()->json(['error' => 'User tidak ditemukan'], 404);
            }

            $saldoid = Saldo::where('id_user', $userr->id)->first();
            $idsaldo = $saldoid->id;

            $kurang = $saldo->saldo - $request->input('saldo');
            $tambah = $saldoid->saldo + $request->input('saldo');

            if ($kurang >= 0) {
                $pengirim = Saldo::find($user);
                $pengirim->saldo = $kurang;
                $pengirim->save();

                $penerima = Saldo::find($idsaldo);
                $penerima->saldo = $tambah;
                $penerima->save();

                $data = Data::create([
                    'no_ref' => '#' . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99),
                    'keterangan' => 'Transfer Sesama Bank',
                    'saldo' => $kurang,
                    'username_penerima' => $saldoid->username,
                    'username' => $saldo->username,
                    'id_user' => $user,
                    'waktu' => now()
                ]);

                return response()->json(['pengirim' => $pengirim->saldo, 'penerima' => $penerima->saldo ,'Data'=>$data], 200);
            } else {
                return response()->json(['error' => 'Masukkan nominal dengan uang yang ada di saldo utama'], 401);
            }
           
        } else {
            return response()->json(['error' => 'Pin tidak sesuai'], 401);
        }

        
    }

}
