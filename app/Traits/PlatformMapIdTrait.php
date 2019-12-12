<?php
namespace App\Traits;

trait PlatformMapIdTrait
{
    public function getPlatformIdList($platformSlug)
    {
        $platformOptions = __('common.platform_slug_map');
        $outputPlatform = [];
        $outputPlatform[] = 3;
        $outputPlatform[] = ($platformOptions[$platformSlug]) ?? 3;
        $outputPlatform = array_unique($outputPlatform);
        return $outputPlatform;
    }
}
