<?php

namespace app\index\controller;

class Format extends Base
{
    protected $subNav = [
        'json' => 'JSON格式化',
        'xml' => 'XML格式化',
        'css' => 'CSS格式化',
        'sql' => 'SQL格式化',
    ];

    /**
     * 对称加密
     * @return \think\response\View
     */
    public function json()
    {
        $this->view->assign('title','在线JSON格式化工具');
        $this->view->assign('keywords','开源中国 在线工具,ostools,jsbin,加密/解密，Markdown,less,java api,php api,node.js api,QR Code');
        $this->view->assign('description','开源中国在线工具,ostools为开发设计人员提供在线工具，提供jsbin在线 CSS、JS 调试，在线 Java API文档,在线 PHP API文档,在线 Node.js API文档,Less CSS编译器，MarkDown编译器等其他在线工具');

        $tabContent = $this->view->fetch("format/json");
        $this->view->assign('tab_content', $tabContent);
        return view('format/index');
    }

    public function xml()
    {
        $this->view->assign('title','在线XML格式化工具');

        $tabContent = $this->view->fetch("format/xml");
        $this->view->assign('tab_content', $tabContent);
        return view('format/index');
    }

    public function css()
    {
        $this->view->assign('title','在线CSS格式化工具');

        $tabContent = $this->view->fetch("format/css");
        $this->view->assign('tab_content', $tabContent);
        return view('format/index');
    }

    public function sql()
    {
        $this->view->assign('title','在线SQL格式化工具');

        $tabContent = $this->view->fetch("format/sql");
        $this->view->assign('tab_content', $tabContent);
        return view('format/index');
    }
}
