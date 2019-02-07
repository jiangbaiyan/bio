<?php
/**
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/1/10
 * Time: 15:41
 */


namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        if (empty($token)) {
            Log::error('auth|hedaer_token_empty');
            throw new UnauthorizedException();
        }
        try {
            JWT::decode($token, env('JWT_KEY'), ['HS256']);
        } catch (\Exception $e) {
            Log::error('auth|decode_token_failed|msg:' . json_encode($e->getMessage()));
            throw new UnauthorizedException();
        }
        return $next($request);
    }
}
