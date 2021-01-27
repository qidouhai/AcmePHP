<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Error extends BaseController
{
    public function index($response)
    {
        $response::view(['home/header', 'home/404', 'home/footer'], ['title' => '404']);
    }
}
