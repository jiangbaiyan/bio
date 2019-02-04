<?php

namespace App\Http\Controllers\Pc\Common;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Library\Response;
use App\Model\MBio;
use App\Model\MBioLog;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $data = [
            'fName' => $request->get('fName'),
            'fIdCard' => $request->get('fIdCard'),
            'oName' => $request->get('oName'),
            'organization' => $request->get('organization')
        ];
        $token = $this->setToken($data);
        $this->insert2Bio($data);
        $this->insert2BioLog($data);
        Response::apiSuccess($token);
    }

    /**
     * 设置token
     * @param $data
     * @return string
     */
    private function setToken($data) {
        $time = time();
        $token = [
            'iss' => 'https://bio.hzcloudservice.com',
            'aud' => 'https://bio.hzcloudservice.com',
            'iat' => $time,
            'exp' => $time + 86400,
            'data' => $data
        ];
        return JWT::encode($token, env('JWT_KEY'));
    }

    /**
     * 插入基本表
     * @param $data
     * @throws OperateFailedException
     */
    private function insert2Bio($data) {
        $dbData = [
            'f_name' => $data['fName'],
            'id_card' => $data['fIdCard'],
            'o_name' => $data['oName'],
            'organization' => $data['organization']
        ];
        try {
            MBio::firstOrCreate([
                'id_card' => $dbData['id_card']
            ], $dbData);
        } catch (\Exception $e){
            Log::error('login|insert_or_update_into_bio_failed|msg:' , json_encode($e->getMessage()));
            throw new OperateFailedException();
        }
    }

    /**
     * 插入日志表
     * @param $data
     * @throws OperateFailedException
     */
    private function insert2BioLog($data) {
        $dbData = [
            'f_name' => $data['fName'],
            'id_card' => $data['fIdCard'],
            'o_name' => $data['oName'],
            'organization' => $data['organization'],
            'operation' => '操作员登录',
            'state'  => 1
        ];
        try {
            MBioLog::firstOrCreate([
                'id_card' => $dbData['id_card']
            ], $dbData);
        } catch (\Exception $e){
            Log::error('login|insert_or_update_into_bio_log_failed|msg:' , json_encode($e->getMessage()));
            throw new OperateFailedException();
        }
    }
}