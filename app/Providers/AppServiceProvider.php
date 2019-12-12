<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Queue;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('no_html', function ($attribute, $value, $parameters, $validator) {
            if ($value != strip_tags($value)) {
                // is HTML
                return false;
            } else {
                // not HTML
                return true;
            }
        });
        Validator::extend('not_exists', function ($attribute, $value, $parameters) {
            return \DB::table($parameters[0])
                ->where($parameters[1], '=', $value)
                ->count() < 1;
        });
        //支付宝帐号验证
        Validator::extendImplicit('alipay_account', function ($attribute, $value, $parameters) {
            //容许email 或是手机
            if (filter_var($value, FILTER_VALIDATE_EMAIL) || preg_match("/^\d{11}$/", $value)) {
                return true;
            } else {
                return false;
            }
        });
        Validator::extendImplicit('cellphone', function ($attribute, $value, $parameters) {
            return preg_match("/^\d{11}$/", $value);
        });
        Queue::failing(function (JobFailed $event) {
            $debugInfo = [
                '例外訊息' => $event->exception->getMessage(),
                '工作物件' => $event->job->resolveName(),
                '工作未處理內容' => $event->job->getRawBody(),
                '例外堆迭' => $event->exception->getTraceAsString(),
            ];
            smail(env('MAIL_FROM_ADDRESS'), '队列工作失败', 'mail.crontab-error', $debugInfo);
            // 通知团队失败的任务...
            // wl('Job 错误');
            // wl($connection, $job, $data);
        });
        Validator::extend('is_chinese', function ($attribute, $value, $parameters, $validator) {
            return preg_match("/\p{Han}+/u", $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);
    }
}
