<?php

namespace app\index\controller;

class Crypto extends Base
{
    public function index()
    {
        //Hash常用按钮
        $algos = hash_algos();
        $common = ['md5', 'sha1', 'sha224', 'sha256', 'sha384', 'sha512'];
        $hashButton = [];
        foreach ($common as $algo) {
            if (in_array($algo, $algos)) {
                $url = url('crypto/hash');
                $up = strtoupper($algo);
                $hashButton[] = "<button data-query='algo=$algo' data-url='$url' type='button' class='btn btn-primary event-crypto'>$up&nbsp;<span class='glyphicon glyphicon-chevron-right'></span></button>&nbsp;";
            }
        }
        $this->view->assign('hash_button', implode('', $hashButton));
        //hash其他按钮
        $hashOther = [];
        foreach ($algos as $algo) {
            if (!in_array($algo, $common)) $hashOther[$algo] = strtoupper($algo);
        }
        $this->view->assign('hash_other_select', $hashOther);

        return view('index');
    }

    /**
     * base64编码解码
     * @return string
     */
    public function base64()
    {
        if (!$this->request->isPost() || !$this->request->isAjax()) {
            return json_encode(['status' => 1, 'msg' => 'Access Denied']);
        }

        $type = $this->request->post('type');
        $plainText = trim($this->request->post('base64_plain_text'));
        $cipherText = trim($this->request->post('base64_cipher_text'));

        //解析数据生成
        $data = ['base64_plain_text' => $plainText, 'base64_cipher_text' => $cipherText];
        switch ($type) {
            case 'encode':
                $data['base64_cipher_text'] = base64_encode($plainText);
                break;
            case 'decode':
                $data['base64_plain_text'] = base64_decode($cipherText);
                break;
        }
        $res = ['status' => 0, 'msg' => 'success', 'data' => $data];
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function hash()
    {
//        if (!$this->request->isPost() || !$this->request->isAjax()) {
//            return json_encode(['status' => 1, 'msg' => 'Access Denied']);
//        }

        $algo = $this->request->post('algo');
        if ($algo == 'other') {
            $algo = $this->request->post('other_algo');
        }
        $plainText = trim($this->request->post('hash_plain_text'));

        //解析数据生成
        $data = [
            'hash_plain_text' => $plainText,
            'hash_cipher_text' => hash($algo, $plainText),
        ];
        return json_encode(['status' => 0, 'msg' => 'success', 'data' => $data]);
    }
}
