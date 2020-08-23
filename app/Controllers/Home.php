<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index($responst)
    {
        $responst::view(['home/header', 'home/index', 'home/footer'], ['title' => 'NCPHP']);
    }
}
