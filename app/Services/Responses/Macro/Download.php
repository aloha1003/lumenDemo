<?php

namespace App\Services\Responses\Macro;

use App\Services\Responses\ResponseMacroInterface;

class Download implements ResponseMacroInterface
{
    public function run($factory)
    {
        $factory->macro('download', function (string $filepath, string $filename, array $headers = []) use ($factory) {
            $headers = !empty($headers) ? $headers : [
                'Content-Type: application/zip, application/octet-stream',
            ];

            return $factory->download($filepath, $filename, $headers);
        });
    }
}
