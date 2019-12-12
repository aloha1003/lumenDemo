<?php
namespace App\Services;

use App\Repositories\Interfaces\AnchorReportRepository;
use App\Repositories\Interfaces\UserReportRepository;

/**
 * 举报服务
 */
class ReportService
{
    use \App\Traits\MagicGetTrait;
    protected $userReportRepository;

    protected $anchorReportRepository;
    public function __construct(UserReportRepository $userReportRepository, AnchorReportRepository $anchorReportRepository)
    {
        $this->userReportRepository = $userReportRepository;
        $this->anchorReportRepository = $anchorReportRepository;
    }

    /**
     * 主播举报
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T10:00:08+0800
     */
    public function reprtAnchor($data)
    {
        $data['report_status'] = $this->anchorReportRepository->makeModel()::REPORT_STATUS_NO;
        $this->anchorReportRepository->create($data);
    }

    /**
     * 用户举报
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T10:00:23+0800
     */
    public function reportUser($data)
    {
        $data['report_status'] = $this->userReportRepository->makeModel()::REPORT_STATUS_NO;
        $this->userReportRepository->create($data);
    }
}
