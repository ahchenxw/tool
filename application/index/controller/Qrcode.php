<?php

namespace app\index\controller;

class Qrcode extends Base
{

    protected $subNav = [
        'encode' => '生成二维码',
        'decode' => '二维码解码',
    ];

    /**
     * 二维码加密
     * @return \think\response\View
     */
    public function encode()
    {
        $this->view->assign('title', '在线二维码生成');
        $this->view->assign('keywords', '开源中国 在线工具,ostools,jsbin,加密/解密，Markdown,less,java api,php api,node.js api,QR Code');
        $this->view->assign('description', '开源中国在线工具,ostools为开发设计人员提供在线工具，提供jsbin在线 CSS、JS 调试，在线 Java API文档,在线 PHP API文档,在线 Node.js API文档,Less CSS编译器，MarkDown编译器等其他在线工具');

        $tabContent = $this->view->fetch("qrcode/encode");
        $this->view->assign('tab_content', $tabContent);
        return view('qrcode/index');
    }

    /**
     * 二维码加密
     * @return \think\response\View
     */
    public function decode()
    {
        $this->view->assign('title', '在线二维码解码');

        $tabContent = $this->view->fetch("format/xml");
        $this->view->assign('tab_content', $tabContent);
        return view('qrcode/index');
    }

}
