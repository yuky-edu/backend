<?php

namespace App\Http\Middleware;

use Closure;
use App\Player;

class PlayerMiddleware
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
        $getUserByToken = Player::select("id", "yclass_session", "token")->where([
          ["token", "=", $token]
        ])->first();
        if ($getUserByToken->token == $token) {
          $request->attributes->add(['myidsession' => $getUserByToken->yclass_session]);
          $request->attributes->add(['myid' => $getUserByToken->id]);
          return $next($request);
        }
      } catch (\Exception $e) {
        return $unauthorized;
      }
    }
}
