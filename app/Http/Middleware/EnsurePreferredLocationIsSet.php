<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePreferredLocationIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $user->load('preferredLocation');

        if (!$user->preferredLocation) {
            $request->session()->flash('type', 'error');
            $request->session()->flash('message', 'Es necesario llenar los datos de dirección primero.');

            if ($request->routeIs('checkout*')) {
                return to_route('carts.show', $user->cart->id);
            } else {
                return back();
            }
        }

        return $next($request);
    }
}
