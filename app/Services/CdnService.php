<?php

namespace App\Services;

//cdn服务
class CDNService
{

    public function preFetch($url)
    {
        $curl = curl_init();
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        ];
        $ret = curl_exec($curl);
        curl_close($curl);
    }
}
