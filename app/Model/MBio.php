<?php
/**
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/1/14
 * Time: 13:08
 */

namespace App\Model;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MBio extends Model {

    public $guarded = [];

    public $table = 'bio';

    /**
     * 状态描述
     */
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 0;

    /**
     * 录入家属生物特征数据
     * @param $request
     * @param $data
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     */
    public static function writeData($request, $data) {
        $user = User::getCurUser($request);
        $bioData = MBio::where('id_card', $user['fIdCard'])->first();
        if (!$bioData) {
            Log::error('mBio|bio_data_empty');
            throw new ResourceNotFoundException();
        }
        try {
            $bioData->update($data);
        } catch (\Exception $e) {
            throw new OperateFailedException();
        }
    }

    /**
     * 特征图像存储
     * @param $file
     * @param $dir
     * @param $fileName
     * @throws OperateFailedException
     */
    public static function saveFile($file, $dir, $fileName) {
        if (!$file->isValid()) {
            Log::error('mBio|upload_file_failed|');
            throw new OperateFailedException();
        }
        try {
            $file->move($dir, $fileName);
        } catch (\Exception $e){
            Log::error('mBio|save_file_failed|msg:' . json_encode($e->getMessage()));
            throw new OperateFailedException();
        }
    }

}