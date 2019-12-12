<?php

namespace App\Services\Payments;

/**
 * @SWG\Definition(
 *      definition="付费结果",
 *      @SWG\Property(
 *          property="link",
 *          type="string",
 *          description="付费连结"
 *      )
 * )
 */
class PayResult
{
    protected $link;
    protected $payTransactionNo;
    protected $callBackType;
    public function __construct(string $link, string $callBackType, string $payTransactionNo = '')
    {
        $this->link = $link;
        $this->payTransactionNo = $payTransactionNo;
        $this->callBackType = $callBackType;
    }
    /**
     * 格式化输出结果
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:29:06+0800
     */
    public function formatResult()
    {
        //封装一下连结
        $linkKey = \App\Services\UserTopupOrderService::getLinkCacheKey($this->payTransactionNo);
        $data = [
            'link' => $this->link,
            'payTransactionNo' => $this->payTransactionNo,
            'callBackType' => $this->callBackType,
        ];
        // \Cache::put($linkKey, $data, 60);
        // $data['link'] = config('app.external_url') . '/payment/' . $this->payTransactionNo;
        return $data;
    }
}
