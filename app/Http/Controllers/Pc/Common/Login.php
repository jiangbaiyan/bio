<?php

namespace App\Http\Controllers\Pc\Common;

use App\Library\Response;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class Login extends Controller {

    public function login(Request $request) {
        $this->validate($request, [
            'fName' => 'required',
            'fIdCard' => 'required',
            'oName' => 'required',
        ]);
    }
}