<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->analytic();
        $this->game();
        $this->app->bind('App\Repositories\Interfaces\LiveRoomRepository', 'App\Repositories\LiveRoomRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GiftTransactionOrderRepository', 'App\Repositories\GiftTransactionOrderRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserGoldFlowRepository', 'App\Repositories\UserGoldFlowRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BaseBarrageTypeRepository', 'App\Repositories\BaseBarrageTypeRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BaseGiftTypeRepository', 'App\Repositories\BaseGiftTypeRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BaseUserTypeRepository', 'App\Repositories\BaseUserTypeRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserRepository', 'App\Repositories\UserRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserConfigRepository', 'App\Repositories\UserConfigRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserFollowRepository', 'App\Repositories\UserFollowRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserAuthRepository', 'App\Repositories\UserAuthRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserAvatarChangeTimesRepository', 'App\Repositories\UserAvatarChangeTimesRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnchorInfoRepository', 'App\Repositories\AnchorInfoRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BarrageTransactionOrderRepository', 'App\Repositories\BarrageTransactionOrderRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\ModelLogRepository', 'App\Repositories\ModelLogRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\ManagerRepository', 'App\Repositories\ManagerRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\RealNameVerifyRepository', 'App\Repositories\RealNameVerifyRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnchorAdverstingRepository', 'App\Repositories\AnchorAdverstingRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnchorAdSlugRepository', 'App\Repositories\AnchorAdSlugRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\SystemConfigRepository', 'App\Repositories\SystemConfigRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\FrontPageAdminRepository', 'App\Repositories\FrontPageAdminRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\RollAdRepository', 'App\Repositories\RollAdRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\ActivityAdRepository', 'App\Repositories\ActivityAdRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnnouceRepository', 'App\Repositories\AnnouceRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\HomePageBannerRepository', 'App\Repositories\HomePageBannerRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameRepository', 'App\Repositories\GameRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserFeedbackRepository', 'App\Repositories\UserFeedbackRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\RollAdHitRecordRepository', 'App\Repositories\RollAdHitRecordRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnnouceHitRecordRepository', 'App\Repositories\AnnouceHitRecordRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\ManagerCompanyGoldFlowRepository', 'App\Repositories\ManagerCompanyGoldFlowRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnchorReportRepository', 'App\Repositories\AnchorReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserChatBlackListRepository', 'App\Repositories\UserChatBlackListRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GoldTopupApplicationRepository', 'App\Repositories\GoldTopupApplicationRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\SpecialAccountRepository', 'App\Repositories\SpecialAccountRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\HotAnchorRepository', 'App\Repositories\HotAnchorRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\DestoryGoldRepository', 'App\Repositories\DestoryGoldRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\PaymentChannelRepository', 'App\Repositories\PaymentChannelRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\WithDrawGoldApplyRepository', 'App\Repositories\WithDrawGoldApplyRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\CompanyWithdrawRepository', 'App\Repositories\CompanyWithdrawRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserMessageRepository', 'App\Repositories\UserMessageRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\ManagerCompanyMoneyFlowRepository', 'App\Repositories\ManagerCompanyMoneyFlowRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\PaymentChannelRepository', 'App\Repositories\PaymentChannelRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\PayChannelPaymentRepository', 'App\Repositories\PayChannelPaymentRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\PayChannelRepository', 'App\Repositories\PayChannelRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserTopupOrderRepository', 'App\Repositories\UserTopupOrderRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserTopupOrderLogRepository', 'App\Repositories\UserTopupOrderLogRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\AgentNameListRepository', 'App\Repositories\AgentNameListRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AgentTransactionListRepository', 'App\Repositories\AgentTransactionListRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\UserGoldTransportRecordRepository', 'App\Repositories\UserGoldTransportRecordRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\NewAnchorRepository', 'App\Repositories\NewAnchorRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserTopupReportRepository', 'App\Repositories\UserTopupReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\FirstTopupRecordRepository', 'App\Repositories\FirstTopupRecordRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\FirstTopupRecordRepository', 'App\Repositories\FirstTopupRecordRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\SpecialUserRepository', 'App\Repositories\SpecialUserRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BlockDeviceRepository', 'App\Repositories\BlockDeviceRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserLoginRecordRepository', 'App\Repositories\UserLoginRecordRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BlockAnchorRepository', 'App\Repositories\BlockAnchorRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\ChannelRepository', 'App\Repositories\ChannelRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\GameBetRecordRepository', 'App\Repositories\GameBetRecordRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\GameAccountRepository', 'App\Repositories\GameAccountRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameCoinChangeLogRepository', 'App\Repositories\GameCoinChangeLogRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameVersionsRepository', 'App\Repositories\GameVersionsRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameRobotRepository', 'App\Repositories\GameRobotRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameDoorCtrlRepository', 'App\Repositories\GameDoorCtrlRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameBenzLogRepository', 'App\Repositories\GameBenzLogRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\MaintainRepository', 'App\Repositories\MaintainRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserReportRepository', 'App\Repositories\UserReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\PayTypeIconRepository', 'App\Repositories\PayTypeIconRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\UserTopupAppealRepository', 'App\Repositories\UserTopupAppealRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnnouceForUserRepository', 'App\Repositories\AnnouceForUserRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnnouceSlugRepository', 'App\Repositories\AnnouceSlugRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\CompanyAnchorApplyRepository', 'App\Repositories\CompanyAnchorApplyRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserBankInfoRepository', 'App\Repositories\UserBankInfoRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserDailyWithdrawTimesRepository', 'App\Repositories\UserDailyWithdrawTimesRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\WithdrawAppealRepository', 'App\Repositories\WithdrawAppealRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BaseLevelRepository', 'App\Repositories\BaseLevelRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\BaseHotConfigureRepository', 'App\Repositories\BaseHotConfigureRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserLevelAccumulationRepository', 'App\Repositories\UserLevelAccumulationRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\UserStoryWallRepository', 'App\Repositories\UserStoryWallRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameReleaseRepository', 'App\Repositories\GameReleaseRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AppReleaseRepository', 'App\Repositories\AppReleaseRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\LiveBarrageStatisticsRepository', 'App\Repositories\LiveBarrageStatisticsRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\LiveGiftStatisticsRepository', 'App\Repositories\LiveGiftStatisticsRepositoryEloquent');

        $this->app->bind('App\Repositories\Interfaces\LiveScheduleRepository', 'App\Repositories\LiveScheduleRepositoryEloquent');
    }

    /**
     * 游戏相关
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-12T17:04:08+0800
     */
    protected function game()
    {
        // 遊戲相關 Repository
        $this->app->bind('App\Repositories\Interfaces\GmAccountInfoRepository', 'App\Repositories\GmAccountInfoRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmCfgServerListRepository', 'App\Repositories\GmCfgServerListRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmCfgVipConfigRepository', 'App\Repositories\GmCfgVipConfigRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmCfgVipPlayerRepository', 'App\Repositories\GmCfgVipPlayerRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmLogDailyRewardsRepository', 'App\Repositories\GmLogDailyRewardsRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmLogImeRegisterRepository', 'App\Repositories\GmLogImeRegisterRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmLogUserrequestRepository', 'App\Repositories\GmLogUserrequestRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GmResVersionsRepository', 'App\Repositories\GmResVersionsRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\GameUserBeBankRecordRepository', 'App\Repositories\GameUserBeBankRecordRepositoryEloquent');

    }
    // analytic相關 Repository
    protected function analytic()
    {
        $this->app->bind('App\Repositories\Interfaces\AnalyticManagerCompanyGoldReportRepository', 'App\Repositories\AnalyticManagerCompanyGoldReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticManagerCompanyGoldDetailReportRepository', 'App\Repositories\AnalyticManagerCompanyGoldDetailReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticUserDailyGoldFlowReportRepository', 'App\Repositories\AnalyticUserDailyGoldFlowReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticAnchorReceiveGoldStatisticRepository', 'App\Repositories\AnalyticAnchorReceiveGoldStatisticRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticAgentTransportGoldStatisticRepository', 'App\Repositories\AnalyticAgentTransportGoldStatisticRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\DailyUserReportRepository', 'App\Repositories\DailyUserReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\DailyRevenueRepository', 'App\Repositories\DailyRevenueRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyAnchorNumberReportRepository', 'App\Repositories\AnalyticDailyAnchorNumberReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyGameIncomeReportRepository', 'App\Repositories\AnalyticDailyGameIncomeReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyLiveIncomeReportRepository', 'App\Repositories\AnalyticDailyLiveIncomeReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\DailyWithdrawReportRepository', 'App\Repositories\DailyWithdrawReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\DailyGameBetReportRepository', 'App\Repositories\DailyGameBetReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\UserRetentionRateRepository', 'App\Repositories\UserRetentionRateRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyUserNumberReportRepository', 'App\Repositories\AnalyticDailyUserNumberReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyChannelTopupRepository', 'App\Repositories\AnalyticDailyChannelTopupRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyAdminTopupApplyReportRepository', 'App\Repositories\AnalyticDailyAdminTopupApplyReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyUserWithdrawReportRepository', 'App\Repositories\AnalyticDailyUserWithdrawReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticDailyCompanyWithdrawReportRepository', 'App\Repositories\AnalyticDailyCompanyWithdrawReportRepositoryEloquent');
        $this->app->bind('App\Repositories\Interfaces\AnalyticGoldStatisticsReportRepository', 'App\Repositories\AnalyticGoldStatisticsReportRepositoryEloquent');

    }
}
