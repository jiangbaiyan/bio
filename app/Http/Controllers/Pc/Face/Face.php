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
use App\Library\Response;
use App\Model\MBio;
use App\Model\MBioLog;
use App\Model\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class Face extends Controller {

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
        $fileName = md5(json_encode(User::getCurUser($request))) . '.' . $file->getClientOriginalExtension();
        $fullPath = $dir . '/' . $fileName;
        MBio::saveFile($file, $dir, $fileName);
        MBio::writeData($request, [
            'face_data' => $fullPath,
            'face_reserve_state' => MBio::STATUS_SUCCESS
        ]);
        MBioLog::writeLog($request, MBioLog::OP_FACE_RESERVE, MBioLog::STATUS_SUCCESS);
        Response::apiSuccess();
    }

    /**
     * 人脸比对
     */
    public function compare() {

    }
}