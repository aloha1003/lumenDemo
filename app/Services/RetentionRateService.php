<?php
namespace App\Services;

use App\Models\UserRetentionRate;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\UserRetentionRateRepository;

/**
 * 留存率服务
 */
class RetentionRateService
{
    use \App\Traits\MagicGetTrait;
    private $userRepository;
    private $retentionRateRepository;
    public function __construct(UserRepository $userRepository,
        UserRetentionRateRepository $userRetentionRateRepository) {
        $this->userRepository = $userRepository;
        $this->userRetentionRateRepository = $userRetentionRateRepository;
    }

    /**
     * 建立指定日期的留存记录
     *
     * @param    string                   $date [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T09:25:05+0800
     */
    public function makeUserRententionRecord($date)
    {
        //先从留存率表取得最近六十天的记录，因为最多要记录到六十天
        $now = strtotime($date);
        $daySeconds = 86400;
        $endDay = date("Y-m-d", $now);
        $startDay = date("Y-m-d", $now - $daySeconds * 60); //取六十天前的记录
        $records = $this->userRetentionRateRepository->makeModel()
            ->whereBetween('date', [$startDay, $endDay])
            ->get()
        ;
        //取得当天登入数量
        $loginCount = $this->userRepository
            ->makeModel()->loginAccount($date);
        if ($records->count() > 0) {
            foreach ($records as $key => $record) {
                //将当前时间-记录的date, 取出相隔几天，换算这次要更新那个那个栏位
                $diffDay = ($now - strtotime($record->date)) / $daySeconds;
                switch ($diffDay) {
                    case 1:
                        $this->writeOneDayNum($record, $loginCount);
                        break;
                    case 3:
                        $this->writeThreeDayNum($record, $loginCount);
                        break;
                    case 7:
                        $this->writeSevenDayNum($record, $loginCount);
                        break;
                    case 14:
                        $this->writeFourTeenDayNum($record, $loginCount);
                        break;
                    case 30:
                        $this->writeThirtyDayNum($record, $loginCount);
                        break;
                    case 60:
                        $this->writesSixtyDayNum($record, $loginCount);
                        break;
                    default:
                        //其他天数不处理
                        break;
                }
                $record->save();
            }
        } else {
            //都找不到的话，直接新建一笔资料
            $record = $this->userRetentionRateRepository->makeModel();
            $record->date = $date;
            $record->base_num = $loginCount;
            $record->save();
        }
    }
    /**
     * 写入次日的统计
     *
     * @param    UserRetentionRate        $record [description]
     * @param    [type]                   $num    [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:26:09+0800
     */
    protected function writeOneDayNum(UserRetentionRate $record, $num)
    {
        $record->one_day_num = $num;
        return $record;
    }

    /**
     * 写入三日的统计
     *
     * @param    UserRetentionRate        $record [description]
     * @param    [type]                   $num    [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:26:09+0800
     */
    protected function writeThreeDayNum(UserRetentionRate $record, $num)
    {
        $record->three_day_num = $num;
        return $record;
    }

    /**
     * 写入7日的统计
     *
     * @param    UserRetentionRate        $record [description]
     * @param    [type]                   $num    [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:26:09+0800
     */
    protected function writeSevenDayNum(UserRetentionRate $record, $num)
    {
        $record->three_day_num = $num;
        return $record;
    }

    /**
     * 写入7日的统计
     *
     * @param    UserRetentionRate        $record [description]
     * @param    [type]                   $num    [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:26:09+0800
     */
    protected function writeFourTeenDayNum(UserRetentionRate $record, $num)
    {
        $record->fourteen_day_num = $num;
        return $record;
    }

    /**
     * 写入三十日的统计
     *
     * @param    UserRetentionRate        $record [description]
     * @param    [type]                   $num    [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:26:09+0800
     */
    protected function writeThirtyDayNum(UserRetentionRate $record, $num)
    {
        $record->thirty_day_num = $num;
        return $record;
    }

    /**
     * 写入六十日的统计
     *
     * @param    UserRetentionRate        $record [description]
     * @param    [type]                   $num    [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:26:09+0800
     */
    protected function writesSixtyDayNum(UserRetentionRate $record, $num)
    {
        $record->sixty_day_num = $num;
        return $record;
    }
}
