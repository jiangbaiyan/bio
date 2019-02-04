<?php

namespace App\Model;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class User {

    /**
     * 获取当前用户
     * @param Request $request
     * @return array
     */
    public static function getCurUser(Request $request) {
        $token = $request->header('Authorization');
        $user = (array)JWT::decode($token, env('JWT_KEY'), ['HS256']);
        return $user;
    }

}