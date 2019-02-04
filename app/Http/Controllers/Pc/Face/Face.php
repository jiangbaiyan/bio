<?php

namespace App\Http\Controllers\Pc\Face;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Model\MBio;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;

class Face extends Controller {

    public function reserve(Request $request) {
        $this->validate($request, [
            'face' => 'required|image'
        ]);
        $file = $request->file('face');
        if (!$file->isValid()) {
            Log::error('face_reserve|upload_face_file_failed|msg:' . json_encode($file));
            throw new OperateFailedException();
        }
        $dir = '/var/www/Bio/storage/face/' . date('Y') . '/' . date('md');
        $user = User::getCurUser($request);
        $fileName = md5(json_encode(User::getCurUser($request)));
        try {
            $file->move($dir, $fileName);
        } catch (\Exception $e){
            Log::error('face_reserve|save_face_file_failed|msg:' . json_encode($e->getMessage()));
            throw new OperateFailedException();
        }
        $bioData = MBio::where('id_card', $user['fIdCard'])->first();
        if (!$bioData) {
            Log::error('face_reserve|bio_data_empty');
            throw new ResourceNotFoundException();
        }
        try {
            $bioData->update([

            ]);
        } catch (\Exception $e) {

        }
    }

    public function compare() {

    }
}