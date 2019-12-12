<?php
namespace App\Services\Payments\Instances\JPAY;

class Rsa2Client
{

    private $platformPubKey = '';
    private $clientPrivateKey = '';

    public function __construct($platformPubKey, $clientPrivateKey)
    {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
        wordwrap($platformPubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        $this->platformPubKey = $res;
        $res2 = "-----BEGIN RSA PRIVATE KEY-----\n" .
        wordwrap($clientPrivateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        $this->clientPrivateKey = $res2;
    }

/**
 * 创建签名
 * @param string $data 数据
 * @return null|string
 */
    public function createSign($data = [])
    {
//  var_dump(self::getPrivateKey());die;

        if (!is_array($data)) {
            return null;
        }
        $str = $this->getSignData($data);

        return openssl_sign($str, $sign, $this->clientPrivateKey, OPENSSL_ALGO_SHA256) ? base64_encode($sign) : null;
    }

    /**
     * 验证签名
     * @param string $data 数据
     * @param string $sign 签名
     * @return bool
     */
    public function verifySign($data = [], $sign = '')
    {
        if (!is_string($sign) || !is_string($sign)) {
            return false;
        }

        $str = $this->getSignData($data);
        return (bool) openssl_verify(
            $str,
            base64_decode($sign),
            $this->platformPubKey,
            OPENSSL_ALGO_SHA256
        );
    }

    public function getSignData($data)
    {

        ksort($data);
        $strs = [];
        foreach ($data as $key => $v) {
            if (!empty($v) && $key != "sign") {
                $strs[] = $key . "=" . $v;
            }
        }
        $str = implode($strs, "&");
        return $str;
    }
}
