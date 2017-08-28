<?php

namespace app\index\controller;

use security\RSA;

class Encrypt extends Base
{
    protected $subNav = [
        'symmetric' => '对称加密解密',
        'asymmetric' => 'RSA加密解密',
        'base64' => 'Base64',
        'hash' => 'Hash',
        'hmachash' => 'Hmac-Hash',
        'picbase64' => '图片转Base64',
    ];

    private $supportSymmetric = ['DES' => 'DES', '3DES' => '3DES', 'AES' => 'AES', 'RC2' => 'RC2', 'RC4' => 'RC4', 'SEED' => 'SEED', 'IDEA' => 'IDEA', 'CAST5' => 'CAST5', 'BF' => 'BLOWFISH', 'CAMELLIA' => 'CAMELLIA'];

    public function index()
    {
        return view('index');
    }

    /**
     * 对称加密
     * @return \think\response\View
     */
    public function symmetric()
    {
        $this->view->assign('title','在线对称加密和解密');
        if ($this->request->post() && $this->request->isAjax()) {
            $buttonType = $this->request->post('button-type');
            $cipher = $this->request->post('cipher');
            $secretKey = $this->request->post('secret_key');
            $secretIv = $this->request->post('secret_iv');
            $plainText = $this->request->post('plain_text');
            $cipherText = $this->request->post('cipher_text');
            //检查iv长度
            $ivLen1 = openssl_cipher_iv_length($cipher);
            $ivLen2 = mb_strlen($secretIv);
            if ($ivLen1 > 0 && $ivLen1 !== $ivLen2) {
                exit(json_encode(['status' => 1, 'msg' => '偏移量长度不正确！']));
            }

            $data = [];
            switch ($buttonType) {
                case 'encrypt':
                    $encrypt = openssl_encrypt($plainText, $cipher, $secretKey, 0, $secretIv);
                    if ($encrypt === false) {
                        exit(json_encode(['status' => 1, 'msg' => '加密明文失败！']));
                    }
                    $data['cipher_text'] = $encrypt;
                    break;
                case 'decrypt':
                    $decrypt = openssl_decrypt($cipherText, $cipher, $secretKey, 0, $secretIv);
                    if ($decrypt === false) {
                        exit(json_encode(['status' => 1, 'msg' => '解密密文失败！']));
                    }
                    $data['plain_text'] = $decrypt;
                    break;
            }
            exit(json_encode(['status' => 0, 'msg' => 'success', 'data' => $data]));
        }

        $cipherType = $this->request->get('cipher_type', key($this->supportSymmetric));
        $methodList = $this->getCipherMethods($cipherType);

        $this->view->assign('cipher_type_list', $this->supportSymmetric);
        $this->view->assign('cipher_list', $methodList);
        $this->view->assign('secret_iv_length', openssl_cipher_iv_length($cipherType));

        $tabContent = $this->view->fetch("encrypt/symmetric");
        $this->view->assign('tab_content', $tabContent);

        return view('encrypt/index');
    }

    public function asymmetric()
    {
        $this->view->assign('title','在线RSA加密和解密');
        if ($this->request->post() && $this->request->isAjax()) {
            $buttonType = $this->request->post('button-type');
            $originText = $this->request->post('origin_text');
            $publicKey = $this->request->post('public_key');
            $privateKey = $this->request->post('private_key');
            $padding = $this->request->post('padding', OPENSSL_PKCS1_PADDING);
            $rsa = new RSA($publicKey, $privateKey, $padding);

            $data = [];
            switch ($buttonType) {
                case 'public_encrypt':
                    $data['result_text'] = $rsa->publicKeyEncrypt($originText);
                    break;
                case 'public_decrypt':
                    $data['result_text'] = $rsa->publicKeyDecrypt($originText);
                    break;
                case 'private_encrypt':
                    $data['result_text'] = $rsa->privateKeyEncrypt($originText);
                    break;
                case 'private_decrypt':
                    $data['result_text'] = $rsa->privateKeyDecrypt($originText);
                    break;
            }
            exit(json_encode(['status' => 0, 'msg' => 'success', 'data' => $data]));
        }

        $tabContent = $this->view->fetch("encrypt/asymmetric");
        $this->view->assign('tab_content', $tabContent);

        return view('encrypt/index');
    }

    /**
     * BASE64编码解码
     * @return string
     */
    public function base64()
    {
        $this->view->assign('title','在线BASE64加密和解密');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $buttonType = $this->request->post('button-type');
            $plainText = $this->request->post('plain_text');
            $cipherText = $this->request->post('cipher_text');

            //解析数据生成
            $data = [];
            switch ($buttonType) {
                case 'encode':
                    $data['cipher_text'] = base64_encode($plainText);
                    break;
                case 'decode':
                    $data['plain_text'] = base64_decode($cipherText);
                    break;
            }
            $res = ['status' => 0, 'msg' => 'success', 'data' => $data];
            exit(json_encode($res, JSON_UNESCAPED_UNICODE));
        }

        $tabContent = $this->view->fetch("encrypt/base64");
        $this->view->assign('tab_content', $tabContent);

        return view('encrypt/index');
    }

    /**
     * HASH生成
     * @return string
     */
    public function hash()
    {
        $this->view->assign('title','在线Hash生成');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $algo = $this->request->post('button-type');
            if ($algo == 'other') $algo = $this->request->post('other_algo');
            $plainText = $this->request->post('plain_text');

            //解析数据生成
            $data = ['cipher_text' => hash($algo, $plainText),];
            exit(json_encode(['status' => 0, 'msg' => 'success', 'data' => $data]));
        }

        //Hash常用按钮
        $algos = hash_algos();
        $common = ['md5', 'sha1', 'sha224', 'sha256', 'sha384', 'sha512', 'crc32'];
        $hashButton = [];
        foreach ($common as $algo) {
            if (in_array($algo, $algos)) {
                $upper = strtoupper($algo);
                $hashButton[] = "<button type='button' class='btn btn-primary' value='$algo' event-type='AJAX_SUBMIT'>$upper&nbsp;<span class='glyphicon glyphicon-chevron-right'></span></button>&nbsp;";
            }
        }
        $this->view->assign('hash_button', implode('', $hashButton));

        //其他按钮
        $hashOther = [];
        foreach ($algos as $algo) {
            $hashOther[$algo] = strtoupper($algo);
        }
        $this->view->assign('hash_select', $hashOther);

        $tabContent = $this->view->fetch("encrypt/hash");
        $this->view->assign('tab_content', $tabContent);
        return view('encrypt/index');
    }

    /**
     * HMAC-HASH
     * @return string
     */
    public function hmacHash()
    {
        $this->view->assign('title','在线HMAC-HASH生成');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $algo = $this->request->post('hashAlgo');
            $secret = $this->request->post('hashSecret');
            $plainText = $this->request->post('plainText');

            //解析数据生成
            $data = ['cipherText' => hash_hmac($algo, $plainText, $secret)];
            return json_encode(['status' => 0, 'msg' => 'success', 'data' => $data]);
        }

        //Hash按钮
        $algos = hash_algos();
        $hashSelect = [];
        foreach ($algos as $algo) {
            $hashSelect[$algo] = strtoupper($algo);
        }
        $this->view->assign('hash_select', $hashSelect);

        $tabContent = $this->view->fetch("encrypt/hmacHash");
        $this->view->assign('tab_content', $tabContent);

        return view('encrypt/index');
    }

    /**
     * 图片转base64
     * @return string
     */
    public function picBase64()
    {
        $this->view->assign('title','在线图片转BASE64');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $picture = $this->request->file('picture');
            if (!$picture->checkImg()) {
                exit(json_encode(['status' => 1, 'msg' => '文件不合法！']));
            }
            $info = $picture->getInfo();
            $content = file_get_contents($picture->getPathname());
            $encode = base64_encode($content);
            $img = "<img src='data:$info[type];base64,$encode'/>";
            exit(json_encode(['status' => 0, 'msg' => 'success', 'data' => $img]));
        }

        $tabContent = $this->view->fetch("encrypt/picBase64");
        $this->view->assign('tab_content', $tabContent);

        return view('encrypt/index');
    }

    /**
     * SELECT切换事件
     */
    public function selectChange()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {
            $selectName = $this->request->post('select-name');
            $cipherType = $this->request->post('cipher_type');
            $cipher = $this->request->post('cipher');

            $data = [];
            //算法
            if (in_array($selectName, ['cipher_type'])) {
                $methods = $this->getCipherMethods($cipherType);
                $cipher = key($methods);
                $data['cipher'] = \Tag::select($methods, ['name' => 'cipher', 'class' => 'form-control', 'event-type' => 'SELECT', 'event-url' => url('encrypt/selectChange')], $cipher);
            }

            //偏移量
            if (in_array($selectName, ['cipher_type', 'cipher'])) {
                $length = openssl_cipher_iv_length($cipher);
                if ($length > 0) {
                    $input = \Tag::inputText(['name' => 'secret_iv', 'class' => 'form-control', 'placeholder' => "{$length}个字符长度"]);
                } else {
                    $input = \Tag::inputText(['name' => 'secret_iv', 'class' => 'form-control', 'placeholder' => "不用填写", 'disabled' => 'disabled']);
                }
                $data['secret_iv'] = $input;
            }
            exit(json_encode(['status' => 0, 'msg' => '成功', 'data' => $data]));
        }
        exit(json_encode(['status' => 1, 'msg' => '错误']));
    }

    /**
     * 获取对称加密支持的方法
     * @param string $cipherMethod
     * @return array
     */
    private function getCipherMethods($cipherMethod)
    {
        static $cache = [];
        if (empty($cache)) {
            $methods = openssl_get_cipher_methods();
            foreach ($methods as $method) {
                $exp = explode('-', $method);
                $key = $exp[0];
                if ($key == 'DES' && $exp[1] == 'EDE3') {
                    $key = '3DES';
                }
                if (isset($this->supportSymmetric[$key])) {
                    $cache[$key][$method] = $method;
                }
            }
        }
        return $cache[$cipherMethod];
    }
}
