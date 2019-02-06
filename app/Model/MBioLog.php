<?php
/**
 * Created by PhpStorm.
 * User: baiyanzzZ
 * Date: 2019/1/14
 * Time: 13:09
 */


namespace App\Model;

use App\Exceptions\OperateFailedException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MBioLog extends Model {

    public $guarded = [];

    public $table = 'bio_log';

    /**
     * 状态描述
     */
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 0;

    /**
     * 操作记录
     */
    const OP_LOGIN = '操作员登录';
    const OP_FACE_RESERVE = '人脸预约';
    const OP_FINGER_RESERVE = '指纹预约';
    const OP_VOICE_RESERVE = '声纹预约';
    const OP_FACE_COMPARE = '人脸比对';
    const OP_FINGER_COMPARE = '指纹比对';
    const OP_VOICE_COMPARE = '声纹比对';

    /**
     * 操作日志记录
     * @param $request
     * @param $operation
     * @param $state
     * @return bool
     * @throws OperateFailedException
     */
    public static function writeLog(Request $request, $operation, $state) {
        if (empty($request) || empty($operation) || empty($state)) {
            return false;
        }
        $user = User::getCurUser($request);
        $dbData = [
            'f_name' => $user['fName'],
            'id_card' => $user['fIdCard'],
            'o_name' => $user['oName'],
            'organization' => $user['organization'],
            'operation' => $operation,
            'state'  => $state
        ];
        try {
            MBioLog::create($dbData);
        } catch (\Exception $e){
            Log::error('mBioLog|insert_into_bio_log_failed|msg:' , json_encode($e->getMessage()));
            throw new OperateFailedException();
        }
        return true;
    }

}