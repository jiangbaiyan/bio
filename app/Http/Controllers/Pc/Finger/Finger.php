<?php
/**
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/02/08
 * Time: 10:14
 */

namespace App\Http\Controllers\Pc\Finger;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Library\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;

class Finger extends Controller {

    const PROCESS_NONE = 0;
    const PROCESS_GET_FEATURE = 1;//获取指纹特征
    const PROCESS_MAKE_COMPARISON = 2;//指纹特征比对

    private $host;

    private $port;

    /**
     * 指纹预约
     * @param Request $request
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function reserve(Request $request) {
        $this->validate($request, [
            'finger' => 'required|image'
        ]);
        $file = $request->file('finger');
        $this->loadConfig();
//        $socket = socket_create('127.0.0.1', AF_INET, SOL_TCP);
//        if ($socket < 0) {
//            Log::error('finger|create_socket_failed');
//            throw new OperateFailedException();
//        }
//        $result = socket_connect($socket, $this->host, $this->port);
//        if ($socket < 0) {
//            Log::error('finger|connect_socket_failed|msg:' . socket_strerror($result));
//            throw new OperateFailedException();
//        }
        $fileSize = $file->getSize();
        $processType = pack('C1', self::PROCESS_GET_FEATURE);
        $fileSize = pack('L4', $fileSize);
        $fileHandle = fopen($file->getRealPath(), 'rb');
        $content = fread($fileHandle, $fileSize);
        $data = $processType . $fileSize . $content;
        var_dump($data);exit;
        Response::apiSuccess();
    }

    /**
     * 指纹比对
     */
    public function compare() {

    }

    /**
     * 加载配置
     * @return bool
     * @throws OperateFailedException
     */
    private function loadConfig() {
        $this->host = env('FINGER_HOST');
        $this->port = env('FINGER_PORT');
        if (empty($this->host) || empty($this->port)) {
            Log::error('finger|empty_config|host:' . $this->host . '|port:' . $this->port);
            throw new OperateFailedException();
        }
        return true;
    }
}