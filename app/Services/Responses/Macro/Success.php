<?php

namespace App\Services\Responses\Macro;

use App\Exceptions\ErrorCode;
use App\Services\Responses\ResponseMacroInterface;

class Success implements ResponseMacroInterface
{
    public function run($factory)
    {
        $factory->macro('success', function ($data = [], int $code = 200, string $message = '', array $options = [], int $status = 200) use ($factory) {
            $result['code'] = $code;
            $result['message'] = !empty($message) ? trans("response.{$message}") : trans("response.code.{$code}");
            $result['data'] = $data;
            $result += !empty($options) ? $options : [];
            $header = [
                'Content-Type' => 'application/json',
            ];
            $token = request()->header('Authorization');
            if ($token) {
                $header['Authorization'] = \JWTAuth::generate($token);
            }
            if ($code != ErrorCode::OK) {
                $result['data'] = [];
            }
            return $factory->json($result, $status, $header);
        });
    }
}
