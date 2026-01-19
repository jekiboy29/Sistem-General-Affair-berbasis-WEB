<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StokOpnameAuth
{
    public function handle(Request $request, Closure $next)
    {
        // 🔒 Cek apakah user stok opname sudah login
        if (!session('stokopname_logged_in')) {
            return redirect('/login')->with('error', 'Silakan login sebagai stokopname.');
        }

        return $next($request);
    }
}
