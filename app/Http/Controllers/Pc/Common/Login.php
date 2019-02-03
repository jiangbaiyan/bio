<?php

namespace App\Http\Controllers\Pc\Common;

use App\Exceptions\ParamValidateFailedException;
use App\Library\Response;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class Login extends Controller {

    public function login(Request $request) {
        $this->validate($request, [
            'fName' => 'required',
            'fIdCard' => 'required',
            'oName' => 'required',
            'organization' => 'required'
        ]);
        $idCard = $request->get('fIdCard');
        if (!preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $idCard)){
            throw new ParamValidateFailedException();
        }
        $time = time();
        $token = [
            'iss' => 'https://bio.hzcloudservice.com',
            'aud' => 'https://bio.hzcloudservice.com',
            'iat' => $time,
            'exp' => $time + 86400,
            'data' => [
                'fName' => $request->get('fName'),
                'fIdCard' => $request->get('fIdCard'),
                'oName' => $request->get('oName'),
                'organization' => $request->get('organization')
            ]
        ];
        $token = JWT::encode($token, env('JWT_KEY'));
        Response::apiSuccess($token);
    }
}