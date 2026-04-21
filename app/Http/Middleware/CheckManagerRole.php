<?php
// app/Http/Middleware/CheckManagerRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté et a le rôle 'superuser'
        // Adaptez la condition à votre logique de rôles
        if (Auth::check() && Auth::user()->role === 'superuser') {
            return $next($request);
        }

        // Redirige ou renvoie une erreur 403 si ce n'est pas un superuser
        abort(403, 'Accès non autorisé.');
    }
}