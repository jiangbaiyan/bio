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
use App\Library\Socket;
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
        $data = $this->getFingerFeature($file);
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

    /**
     * 获取指纹特征数据
     * @return string
     * @throws OperateFailedException
     */
    private function getFingerFeature($file) {
        $fileSize = $file->getSize();
        $fileHandle = fopen($file->getRealPath(), 'rb');
        $content = fread($fileHandle, $fileSize);
        $processType = pack('C1', self::PROCESS_GET_FEATURE);
        $fileSize = pack('L', $fileSize);
        $data = $processType . $fileSize . $content;
        Socket::write($this->host, $this->port, $data);
        $data = Socket::read($this->host, $this->port, 5);
        return $data;
    }

}