<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
    //标题
    protected $title = '';
    //当前控制器名
    protected $controllerName = null;
    //当前的操作名
    protected $actionName = null;
    //子标题
    protected $subNav = null;
    //闪存消息
    protected $flashMessage = null;

    public function _initialize()
    {
        parent::_initialize();

        //获取当前控制器名
        $this->controllerName = Request::instance()->controller();
        //获取当前的操作名
        $this->actionName = Request::instance()->action();

        //设置网站标题
        $this->view->assign('title', $this->title);

        //生成子导航
        if (is_array($this->subNav)) {
            $items = [];
            foreach ($this->subNav as $key => $name) {
                $url = url("$this->controllerName/$key");
                if ($this->actionName == $key) {
                    $items[] = "<li role='presentation' class='active'><a href='javascript:void(0);'>$name</a></li>";
                } else {
                    $items[] = "<li role='presentation' ><a href='$url'>$name</a></li>";
                }
            }
            $this->assign('sub_nav', $items);
        }

        //LOG消息
        $this->flashMessage = new \FlashMessage();
        $this->view->assign('flashMessage', $this->flashMessage);
    }

}
