<?php

namespace App\Services\Responses\Macro;

use App\Services\Responses\ResponseMacroInterface;

class Error implements ResponseMacroInterface
{
    public function run($factory)
    {
        $factory->macro('error', function ($data = [], int $code = 400, string $message = '', array $options = [], int $status = 400) use ($factory) {
            wl($data);
            if (is_a($data, 'Exception')) {

                $result['code'] = $data->getCode();
                $result['message'] = $data->getMessage();
                if (config('app.env') != 'production') {
                    $result['file'] = $data->getFile();
                    $result['line'] = $data->getLine();
                }
                $result['data'] = [];
            } else {
                $result['code'] = $code;
                $result['message'] = !empty($message) ? trans("response.{$message}") : trans("response.code.{$code}");
                $result['data'] = $data;
            }

            $result += !empty($options) ? $options : [];
            $header = [
                'Content-Type' => 'application/json',
            ];
            return $factory->json($result, $status, $header);
        });
    }
}
