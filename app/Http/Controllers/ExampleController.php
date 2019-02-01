<?php

namespace App\Http\Controllers;

use App\Library\Response;
use Laravel\Lumen\Routing\Controller;

class ExampleController extends Controller
{
    public function show(){
        Response::apiSuccess();
    }
}
