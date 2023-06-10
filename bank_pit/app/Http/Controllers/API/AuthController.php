<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Verifytoken;
use App\Models\Saldo;
use App\Mail\WelcomeMail;
use App\Models\SaldoTabungan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{




    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_depan' => 'required|string|max:255',
            'nama_belakang' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'tempat' => 'required|string|max:255',
            'tanggal' => 'required|string|max:255',
            'bulan' => 'required|string|max:255',
            'tahun' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'pin' => 'required|string|max:255|confirmed',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'tempat' => $request->tempat,
            'tanggal' => $request->tanggal,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'alamat_lengkap' => $request->alamat_lengkap,
            'pin' => $request->pin,
            'no_unique' => '0000' . rand(10, 99) . rand(10, 99) . rand(10, 99),
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $usersaldo = $user->id;

        Saldo::create([
            'username' => $request->username,
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'id_user' => $usersaldo
        ]);

         SaldoTabungan::create([
            'username' => $request->username,
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'id_user' => $usersaldo
        ]);

        $saldos = Saldo::where('id_user', $usersaldo)->get();
        $saldotabungan = SaldoTabungan::where('id_user', $usersaldo)->get(); 

        $validToken = rand(10, 99) . rand(10, 99);
        $get_token = new VerifyToken();
        $get_token->token = $validToken;
        $get_token->email = $user->email;
        $get_token->save();

        $get_user_email = $user->email;
        $get_user_name = $user->usename;
        Mail::to($user->email)->send(new WelcomeMail($get_user_email, $validToken, $get_user_name));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', 'pesan' => 'otp sudah terkirim ke email', 'saldo' => $saldos , 'saldotabungan' => $saldotabungan]);
    }

    public function kirimulang(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()
                ->json(['message' => 'User not found'], 404);
        }

        $validToken = rand(10, 99) . rand(10, 99);
        $get_token = new VerifyToken();
        $get_token->token = $validToken;
        $get_token->email = $user->email;
        $get_token->save();

        $get_user_email = $user->email;
        $get_user_name = $user->name;
        Mail::to($user->email)->send(new WelcomeMail($get_user_email, $validToken, $get_user_name));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['pesan' => 'OTP sudah terkirim ulang ke email']);
    }


    public function otptoken(Request $request)
    {
        $get_token = $request->token;
        $get_token = Verifytoken::where('token', $get_token)->first();


            $user = User::where('email', $get_token->email)->first();

            if ($user->is_activated > 0) {
           

            $get_token->is_activated = 1;
            $get_token->save();

            $user->is_activated = 1;
            $user->save();

            Verifytoken::where('token', $get_token->token)->delete();

        
            return response()->json([
                'message' => 'otp benar silahkan ganti password',
                'data' => $user->id,
            ]);}
            else{
            return response()->json(['message' => 'OTP salah'], 401);
        }
    }


    public function otp(Request $request)
    {
        $get_token = $request->token;
        $get_token = Verifytoken::where('token', $get_token)->first();

        if ($get_token) {
            $user = User::where('email', $get_token->email)->first();

            if ( $user->is_activated == 'off') {
                $get_token->is_activated = 1;
                $get_token->save();

                $user->is_activated = 1;
                $user->save();

                Verifytoken::where('token', $get_token->token)->delete();

                $token = $user->createToken('auth_token')->plainTextToken;

                $saldos = Saldo::where('id_user', $user->id)->get();

                return response()->json([
                    'message' => 'Anda berhasil masuk menjadi nasabah',
                    'access_token' => $token,
                    'data' => $saldos,
                    'token_type' => 'Bearer',
                ]);
            }

            Verifytoken::where('token', $get_token->token)->delete();
            return response()->json(['message' => 'Email sudah diaktifkan sebelumnya'], 401);
        } else {
            return response()->json(['message' => 'OTP salah'], 401);
        }
    }


    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('username', $request->input('username'))->firstOrFail();

            if ($user->is_activated == 'off') {
                return response()->json(['message' => 'Akun Anda belum diaktifkan'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $message = '';
            if ($user->role === 'user') {
                $message = 'Anda berhasil masuk menjadi nasabah';
            } elseif ($user->role === 'admin') {
                $message = 'Anda berhasil masuk menjadi admin';
            }

            $saldos = Saldo::where('id_user', $user->id)->get();

            return response()->json([
                'message' => $message,
                'data' => $saldos,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

        return response()->json(['message' => 'Username atau password salah'], 401);
    }




    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }

    public function password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::find(auth()->user()->id);
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah'], 200);
    }

    public function passworddepan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('id', $request->id)->first(); // Use first() instead of get()

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah'], 200);
    }

}
