<?php
/**
 * 请求操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2019-01-24
 * Time: 11:31
 */

namespace App\Library;

use App\Exceptions\OperateFailedException;
use Illuminate\Support\Facades\Log;

class Request{

    /**
     * 发送Http请求
     * @param $type
     * @param $url
     * @param array $postData
     * @param array $options
     * @param int $retry
     * @param int $timeout
     * @return bool|string
     * @throws OperateFailedException
     */
    public static function send($type, $url, $postData = array(), $options = array(), $retry = 3, $timeout = 20){
        try {
            $ch = curl_init($url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if (!empty($options)){
                curl_setopt_array($ch, $options);
            }
            $type = strtoupper($type);
            if ($type == 'POST'){
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);
            }
            $res = curl_exec($ch);
            if (empty($res)){
                for ($i = 0;$i<$retry;$i++){
                    $res = curl_exec($ch);
                    if (!empty($res)){
                        break;
                    }
                }
                if ($i == $retry){
                    Log::error('curl|send_request_error|url:' . $url . '|type:' . $type . '|postData:' .json_encode($postData) . '|retry:' . $retry . '|curl_error:' . json_encode(curl_error($ch)));
                    throw new OperateFailedException();
                }
            }
            curl_close($ch);
        } catch (\Exception $e) {
            Log::error('curl|send_request_error|url:' . $url . '|type:' . $type . '|postData:' . json_encode($postData) . '|retry:' . $retry . '|curl_exception:' . json_encode($e->getMessage()) . '|curl_error:' . json_encode(curl_error($ch)));
            throw new OperateFailedException();
        }
        return $res;
    }

}