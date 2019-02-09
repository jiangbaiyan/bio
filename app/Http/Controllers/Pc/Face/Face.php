<?php
/**
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/1/15
 * Time: 20:12
 */

namespace App\Http\Controllers\Pc\Face;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Library\File;
use App\Library\Response;
use App\Model\MBio;
use App\Model\MBioLog;
use App\Model\User;
use Illuminate\Http\Request;
use App\Library\Request as SendRequest;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;

class Face extends Controller {

    private $host;

    private $port;

    private $userName;

    private $password;


    /**
     * 人脸预约
     * @param Request $request
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     * @throws ParamValidateFailedException
     */
    public function reserve(Request $request) {
        $this->validate($request, [
            'face' => 'required|image'
        ]);
        $file = $request->file('face');
        $dir = storage_path('picture/face/' . date('Y') . '/' . date('md'));
        $user = User::getCurUser($request);
        $md5Obj = json_encode([$user['fIdCard'], $user['fName']]);//一个用户只能有一个预约照片,后面的覆盖前面的
        $fileName = md5($md5Obj) . '.' . $file->getClientOriginalExtension();
        $fullPath = $dir . '/' . $fileName;
        File::saveFile($file, $dir, $fileName);
        MBio::writeData($request, [
            'face_data' => $fullPath,
            'face_reserve_state' => MBio::STATUS_SUCCESS
        ]);
        MBioLog::writeLog($user, MBioLog::OP_FACE_RESERVE, MBioLog::STATUS_SUCCESS);
        Response::apiSuccess();
    }

    /**
     * 人脸比对,对接宇视科技接口
     * @param Request $request
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function compare(Request $request) {
        $this->validate($request, [
            'face' => 'required|image'
        ]);
        $newFile = $request->file('face')->getRealPath();
        $user = User::getCurUser($request);
        $bioData = MBio::getData($user);
        $oldFile = $bioData->face_data;
        $this->loadConfig();
        $accessToken = $this->loginYs();
        $res = $this->doFaceCompare($oldFile, $newFile, $accessToken);
        if ($res) {
            MBio::writeData($user, ['face_compare_state' => MBio::STATUS_SUCCESS]);
            MBioLog::writeLog($user, MBioLog::OP_FACE_COMPARE, MBioLog::STATUS_SUCCESS);
        } else {
            MBio::writeData($user, ['face_compare_state' => MBio::STATUS_FAILED]);
            MBioLog::writeLog($user, MBioLog::OP_FACE_COMPARE, MBioLog::STATUS_FAILED);
        }
        Response::apiSuccess(['result' => $res]);
    }

    /**
     * 加载宇视服务器配置
     * @return bool
     * @throws OperateFailedException
     */
    private function loadConfig() {
        $this->host = env('FACE_HOST');
        $this->port = env('FACE_PORT');
        $this->userName = env('FACE_USERNAME');
        $this->password = env('FACE_PASSWORD');
        if (empty($this->host) || empty($this->port) || empty($this->userName) || empty($this->password)) {
            Log::error('face|empty_config|host:' . $this->host . '|port:' . $this->port . '|userName:' . $this->userName . '|password:' . $this->password);
            throw new OperateFailedException();
        }
        return true;
    }
    /**
     * 宇视接口统一登录
     * @return mixed
     * @throws OperateFailedException
     */
    private function loginYs() {
        $url = sprintf("http://%s:%s/VIID/login", $this->host, $this->port);
        $res = SendRequest::send('POST', $url);
        $res = json_decode($res, true);
        if (empty($res['AccessCode'])) {
            Log::error('face|login_Ys_first_failed|res:' . json_encode($res));
            throw new OperateFailedException();
        }
        $accessCode = $res['AccessCode'];
        $secondLoginParam = [
            'LoginSignature' => md5(base64_encode($this->userName). $accessCode . md5($this->password)),
            'UserName'       => $this->userName,
            'AccessCode'     => $accessCode
        ];
        $jsonParam = json_encode($secondLoginParam);
        $res = SendRequest::send('POST', $url, $jsonParam, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonParam)
            ]
        ]);
        $res = json_decode($res, true);
        if (empty($res['AccessToken'])) {
            Log::error('face|login_Ys_second_failed|res:' . json_encode($res));
            throw new OperateFailedException();
        }
        return $res['AccessToken'];
    }

    /**
     * 请求宇视人脸比对接口
     * @param $faceA
     * @param $faceB
     * @param $token
     * @return bool|mixed|string
     * @throws OperateFailedException
     */
    private function doFaceCompare($faceA, $faceB, $token) {
        $url = sprintf("http://%s:%s/VIID/ia/face/get/similarity", $this->host, $this->port);
        $param = [
            'Info' => json_encode([
                'IP' => '192.168.100.25',
                'ThriftDesFaceInfo' => [],
                'ThriftSrcFaceInfo' => []
            ]),
            'FaceA' => new \CURLFile($faceA),
            'FaceB' => new \CURLFile($faceB)
        ];
        $res = SendRequest::send('POST', $url, $param, [
            CURLOPT_HTTPHEADER => [
                "Authorization: $token",
            ]
        ]);
        $res = json_decode($res, true);
        if ($res['ErrCode']) {
            Log::error('face|face_Ys_compare_failed|res:' . json_encode($res));
            throw new OperateFailedException();
        }
        if ($res['Result'] < 0.9) {
            return false;
        }
        return true;
    }
}