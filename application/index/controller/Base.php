<?php

namespace app\index\controller;

use think\Controller;

class Base extends Controller
{

    public function index()
    {
        //创建header部分
        $header = $this->createHeader();
        $this->assign('header', $header);
    }

    public function createHeader()
    {
        $header = [
            ['title' => 'MD5', 'href' => '#']
        ];
        return $header;
    }

}
