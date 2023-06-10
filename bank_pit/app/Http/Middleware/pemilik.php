<?php

namespace App\Http\Middleware;

use App\Models\Barang;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class pemilik
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();
        $barang = Barang::find($request->id);

        if($barang->id_user!= $user->id){
            return response()->json(['message'=>'bukan barang mu']);
        }


        return $next($request);
    }
}
