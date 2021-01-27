<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($response)
    {
        $response::view(['home/header', 'home/index', 'home/footer'], ['title' => 'NCPHP']);
    }
}
