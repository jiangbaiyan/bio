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
     * 获取当前用户生物特征数据
     * @param $user
     * @return bool
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     */
    public static function getData($user) {
        if (empty($user)) {
            throw new OperateFailedException();
        }
        $bioData = MBio::where('id_card', $user['fIdCard'])->first();
        if (!$bioData) {
            Log::error('mBio|bio_data_empty');
            throw new ResourceNotFoundException();
        }
        return $bioData;
    }

    /**
     * 录入家属生物特征数据
     * @param $user
     * @param $data
     * @return bool
     * @throws OperateFailedException
     *
     * @throws ResourceNotFoundException
     */
    public static function writeData($user, $data) {
        if (empty($data) || !is_array($data)) {
            throw new OperateFailedException();
        }
        $bioData = self::getData($user);
        try {
            $bioData->update($data);
        } catch (\Exception $e) {
            Log::error('mBio|write_data_update_failed|msg:' . json_encode($e->getMessage()) . '|data:' . json_encode($data));
            throw new OperateFailedException();
        }
        return true;
    }


}