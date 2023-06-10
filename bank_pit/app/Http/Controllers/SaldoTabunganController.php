<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Saldo;
use App\Models\Data;
use App\Models\SaldoTabungan;
use Illuminate\Support\Facades\Validator;

class SaldoTabunganController extends Controller
{
    public function isitabungan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|max:255',
            'saldo' => ['required', 'regex:/^(\d{1,20}(\.\d{1,2})?|\d{21})$/'],
        ], [
            'saldo.regex' => 'Saldo tidak valid atau ada simbol yang dimasukkan. Maksimum 20 digit sebelum tanda desimal.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $id = Auth::id();

        $saldo = Saldo::where('id_user', $id)->first();
        $saldoTabungan = SaldoTabungan::where('id_user', $id)->first();

        if (!$saldo || !$saldoTabungan) {
            return response()->json(['error' => 'Saldo atau SaldoTabungan tidak ditemukan'], 404);
        }

        $pin = Auth::user();
        $kurang = $saldo->saldo - $request->input('saldo');
        $tabungantambah = $saldoTabungan->saldo_tabungan + $request->input('saldo');

        if ($pin->pin === $request->pin) {
            if ($kurang >= 0) {
                $saldo->saldo = $kurang;
                $saldo->save();

                $saldoTabungan->saldo_tabungan = $tabungantambah;
                $saldoTabungan->save();


                $data = Data::create([
                    'no_ref' => '#' . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99),
                    'keterangan' => 'Isi Tabungan',
                    'saldo' => $kurang,
                    'saldo_tabungan' => $tabungantambah,
                    'username' => $saldo->username,
                    'id_user' => $id,
                    'waktu' => now()
                ]);

                return response()->json(['saldo' => $saldo->saldo, 'saldo_tabungan' => $saldoTabungan->saldo_tabungan, 'pesan' => 'Tabungan berhasil ditambah', 'pin' => 'Pin berhasil','Data'=> $data], 200);
            } else {
                return response()->json(['error' => 'Masukkan nominal dengan uang yang ada di saldo utama'], 401);
            }
        } else {
            return response()->json(['error' => 'Pin tidak sesuai'], 401);
        }
    }

    public function cektabungan()
    {
        $id = Auth::user()->id;
        $user = SaldoTabungan::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['saldo' => $user->saldo_tabungan], 200);
    }

    public function isisaldoutama(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|max:255',
            'saldo' => ['required', 'regex:/^(\d{1,20}(\.\d{1,2})?|\d{21})$/'],
        ], [
            'saldo.regex' => 'Saldo tidak valid atau ada simbol yang dimasukkan. Maksimum 20 digit sebelum tanda desimal.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $id = Auth::id();

        $saldo = Saldo::where('id_user', $id)->first();
        $saldoTabungan = SaldoTabungan::where('id_user', $id)->first();

        if (!$saldo || !$saldoTabungan) {
            return response()->json(['error' => 'Saldo atau SaldoTabungan tidak ditemukan'], 404);
        }

        $pin = Auth::user();
        $kurang = $saldo->saldo + $request->input('saldo');
        $tabungantambah = $saldoTabungan->saldo_tabungan - $request->input('saldo');

        if ($pin->pin === $request->pin) {
            if ($kurang >= 0) {
                $saldo->saldo = $kurang;
                $saldo->save();

                $saldoTabungan->saldo_tabungan = $tabungantambah;
                $saldoTabungan->save();

                $data = Data::create([
                    'no_ref' => '#' . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99),
                    'keterangan' => 'Isi Saldo Utama',
                    'saldo' => $kurang,
                    'saldo_tabungan' => $tabungantambah,
                    'username' => $saldo->username,
                    'id_user' => $id,
                    'waktu' => now()
                ]);

                return response()->json(['saldo' => $saldo->saldo, 'saldo_tabungan' => $saldoTabungan->saldo_tabungan, 'pesan' => 'Tabungan berhasil dipindahkan', 'pin' => 'Pin berhasil','Data'=> $data], 200);
            } else {
                return response()->json(['error' => 'Masukkan nominal dengan uang yang ada di saldo utama'], 401);
            }
        } else {
            return response()->json(['error' => 'Pin tidak sesuai'], 401);
        }
    }

}
