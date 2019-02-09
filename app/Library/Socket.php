<?php
/**
 * Socket客户端操作类
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/02/09
 * Time: 11:54
 */

namespace App\Library;

use App\Exceptions\OperateFailedException;
use Illuminate\Support\Facades\Log;

class Socket {

    /**
     * socket句柄
     */
    private static $socket = null;

    /**
     * 向服务端发送数据
     * @param $host
     * @param $port
     * @param $data
     * @return bool
     * @throws OperateFailedException
     */
    public static function write($host, $port, $data) {
        self::init($host, $port);
        if ($result = socket_write(self::$socket, $data, strlen($data)) == false) {
            Log::error('finger|socket_write_data_failed|msg:' . socket_strerror(socket_last_error(self::$socket)));
            throw new OperateFailedException();
        }
        return $result;
    }

    /**
     * 接收服务端返回数据
     * @param $host
     * @param $port
     * @param $length
     * @return bool
     * @throws OperateFailedException
     */
    public static function read($host, $port, $length) {
        self::init($host, $port);
        if (($data = socket_read(self::$socket, $length)) === false) {
            Log::error('finger|socket_read_data_failed|msg:' . socket_strerror(socket_last_error(self::$socket)) . '|data:' . json_encode($data));
            throw new OperateFailedException();
        }
        return $data;
    }

    /**
     * 创建socket数据结构
     * @return bool
     * @throws OperateFailedException
     */
    private static function create() {
        if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            Log::error('finger|create_socket_failed|msg:' . socket_strerror(socket_last_error()));
            throw new OperateFailedException();
        }
        self::$socket = $socket;
        return true;
    }

    /**
     * socket连接
     * @param $host
     * @param $port
     * @return bool
     * @throws OperateFailedException
     */
    private static function connect($host, $port) {
        if (($result = socket_connect(self::$socket, $host, $port)) === false) {
            Log::error('finger|connect_socket_failed|msg:' . socket_strerror(socket_last_error()));
            throw new OperateFailedException();
        }
        return true;
    }

    /**
     * 初始化
     * @param $host
     * @param $port
     * @return bool
     * @throws OperateFailedException
     */
    private static function init($host, $port) {
        if (!isset(self::$socket)){
            self::create();
            self::connect($host, $port);
        }
        return true;
    }

}