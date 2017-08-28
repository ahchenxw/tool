<?php

namespace app\index\controller;

class Ip extends Base
{
    //子标题
    protected $subNav = [
        'search' => 'IP查询',
        'ipwhois' => 'IP WHOIS查询',
    ];

    /**
     * IP查询
     * @return \think\response\View
     */
    public function search()
    {
        $address = trim($this->request->post('address', ''));
        $address = str_replace('http://', '', $address);
        $address = str_replace('https://', '', $address);

        if ($this->request->post() && $address) {
            if (preg_match('/(\d{1,3}\.){3}\d{1,3}/', $address)) {
                $ipList = [$address];
            } else {
                $ipList = gethostbynamel($address);
            }
            //获取IP地址的位置
            $tbody = [];
            if ($ipList) {
                foreach ($ipList as $item) {
                    $position = '';
                    $res = $this->getIpPosition($item);
                    if ($res['code'] == 0) {
                        $data = $res['data'];
                        $position = $data['country'] . $data['region'] . $data['city'] . ' ' . $data['isp'];
                    }
                    $body = [
                        'address' => $address,
                        'ip' => $item,
                        'long' => ip2long($item),
                        'position' => $position,
                    ];
                    $tbody[] = $body;
                }
            }
            $this->view->assign('tbody', $tbody);
        }
        //获取用户IP
        $userIp = $this->request->ip();
        $userPosition = '未知';
        $res = $this->getIpPosition($userIp);
        if ($res['code'] == 0) {
            $data = $res['data'];
            $userPosition = $data['country'] . $data['region'] . $data['city'] . ' ' . $data['isp'];
        }
        $this->view->assign('address', $address);
        $this->view->assign('user_ip', $userIp);
        $this->view->assign('user_position', $userPosition);
        $tabContent = $this->view->fetch("ipaddress/ip");
        $this->view->assign('tab_content', $tabContent);
        return view('ipaddress/index');
    }

    public function ipwhois()
    {
        include(EXTEND_PATH . 'whois/whois.main.php');
        $domain = 'prow.cc';
        $whois = new \Whois();
        $result = $whois->Lookup($domain);
        $rawData = [];
        foreach ($result['rawdata'] as $item) {
            $rawData[] = trim($item);
        }
        $this->view->assign('raw_data', $rawData);
        $tabContent = $this->view->fetch("ipaddress/ipwhois");
        $this->view->assign('tab_content', $tabContent);
        return view('ipaddress/index');
    }

    /**
     * SELECT切换事件
     */
    public function selectChange()
    {
//        if ($this->request->isPost() && $this->request->isAjax()) {
//            $selectName = $this->request->post('select-name');
//            $cipherType = $this->request->post('cipher_type');
//            $cipher = $this->request->post('cipher');
//
//            $data = [];
//            //算法
//            if (in_array($selectName, ['cipher_type'])) {
//                $methods = $this->getCipherMethods($cipherType);
//                $cipher = key($methods);
//                $data['cipher'] = \Tag::select($methods, ['name' => 'cipher', 'class' => 'form-control', 'event-type' => 'SELECT', 'event-url' => url('encrypt/selectChange')], $cipher);
//            }
//
//            //偏移量
//            if (in_array($selectName, ['cipher_type', 'cipher'])) {
//                $length = openssl_cipher_iv_length($cipher);
//                if ($length > 0) {
//                    $input = \Tag::inputText(['name' => 'secret_iv', 'class' => 'form-control', 'placeholder' => "{$length}个字符长度"]);
//                } else {
//                    $input = \Tag::inputText(['name' => 'secret_iv', 'class' => 'form-control', 'placeholder' => "不用填写", 'disabled' => 'disabled']);
//                }
//                $data['secret_iv'] = $input;
//            }
//            exit(json_encode(['status' => 0, 'msg' => '成功', 'data' => $data]));
//        }
//        exit(json_encode(['status' => 1, 'msg' => '错误']));
    }

    /**
     * 获取IP位置
     * @param string $ip
     * @return array|null
     */
    private function getIpPosition($ip)
    {
        $host = "https://dm-81.data.aliyun.com";
        $path = "/rest/160601/ip/getIpInfo.json";
        $method = "GET";
        $appCode = "cf75ae1a68f94ec1a5affb5b8a198dce";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appCode);
        $query = "ip=$ip";
        $url = $host . $path . "?" . $query;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        };
        $res = curl_exec($curl);
        return json_decode($res, true);
    }
}
