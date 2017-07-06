<?php
namespace app\index\controller;

use think\Controller;

class Index extends Base
{
    public function index()
    {
        $this->assign('aa','bb');
        return view('common/main');
    }
}
