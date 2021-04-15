<?php

namespace App\Http\Middleware;

use Closure;
use \App\User;

class HostMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $unauthorized = response([
          "status" => false,
          "errCode" => "unauthorized",
          "errMsg" => "Unauthenticated (token wrong/expired)"
        ], 403);
        try {
          $token = $request->bearerToken();
          if (!$token) return $unauthorized;
          $getUserByToken = User::select("id", "remember_token")->where([
            ["remember_token", "=", $token]
          ])->first();
          if ($getUserByToken->remember_token == $token) {
            $request->attributes->add(['mytoken' => $getUserByToken->remember_token]);
            $request->attributes->add(['myid' => $getUserByToken->id]);
            return $next($request);
          }
        } catch (\Exception $e) {
          return $unauthorized;
        }

    }
}
