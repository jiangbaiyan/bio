<?php
/**
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/02/09
 * Time: 20:16
 */

namespace App\Library;

use App\Exceptions\OperateFailedException;
use Illuminate\Support\Facades\Log;

class File {

    /**
     * 特征图像存储
     * @param $file
     * @param
     * @param $fileName
     * @return bool
     * @throws OperateFailedException
     */
    public static function saveFile($file, $dir, $fileName) {
        if (empty($file) || empty($dir) || empty($fileName)) {
            throw new OperateFailedException();
        }
        if (!$file->isValid()) {
            Log::error('file|upload_file_failed');
            throw new OperateFailedException();
        }
        try {
            $file->move($dir, $fileName);
        } catch (\Exception $e){
            Log::error('file|save_file_failed|msg:' . json_encode($e->getMessage()));
            throw new OperateFailedException();
        }
        return true;
    }

    /**
     * 字符串形式读取文件
     * @param $path
     * @return bool
     * @throws OperateFailedException
     */
    public static function readFileAsString($path) {
        if (empty($path)) {
            throw new OperateFailedException();
        }
        try {
            $content = file_get_contents($path);
        } catch (\Exception $e) {
            Log::error('file|read_file_failed|msg:' . json_encode($e->getMessage()) . '|path:' . $path);
            throw new OperateFailedException();
        }
        return $content;
    }


    /**
     * 二进制形式读取文件
     * @param $path
     * @param $length
     * @return bool|string
     * @throws OperateFailedException
     */
    public static function readFileAsBinary($path, $length) {
        if (empty($path)) {
            throw new OperateFailedException();
        }
        try {
            $fileHandle = fopen($path, 'rb');
            $content = fread($fileHandle, $length);
            fclose($fileHandle);
        } catch (\Exception $e) {
            Log::error('file|read_file_failed|msg:' . json_encode($e->getMessage()) . '|path:' . $path);
            throw new OperateFailedException();
        }
        return $content;
    }

}