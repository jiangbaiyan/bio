<?php

namespace App\Http\Controllers\Pc\Face;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class Face extends Controller {

    public function reserve(Request $request) {
        $this->validate($request, [
            'face' => 'required|image'
        ]);
        $file = $request->file('face');
    }

    public function compare() {

    }
}