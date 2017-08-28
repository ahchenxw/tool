<?php
namespace app\index\controller;

use security\AES;
use think\Controller;

class Index extends Base
{
    public function index()
    {
        $this->redirect('format/json');
    }

}
