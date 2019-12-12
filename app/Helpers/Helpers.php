<?php
/**
 * 取得前端登入用户
 *
 * @return   App\Models\User                   用户
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:48:43+0800
 */
if (!function_exists('whoami')) {
    function whoami()
    {
        return \Auth::user();
    }
}
/**
 * 取得前端当前登入id
 *
 * @return   integer                   id
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:49:11+0800
 */
if (!function_exists('id')) {
    function id()
    {
        return \Auth::guard('web')->id();
    }
}

/**
 * 取得当前后端登入id
 *
 * @return   integer                   id
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:49:29+0800
 */
if (!function_exists('adminId')) {
    function adminId()
    {
        if (\Admin::user()) {
            return \Admin::user()->id;
        } else {
            return 0;
        }
    }
}
/**
 * 取得后台当前登入的使用者物
 *
 * @return   config('admin.auth.providers.admin.model')                  [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:50:32+0800
 */
if (!function_exists('admin')) {
    function admin()
    {
        return \Admin::user();
    }
}
/**
 * 是否是后台访客
 *
 * @return   boolean
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:54:43+0800
 */
if (!function_exists('isAdminGuest')) {
    function isAdminGuest()
    {
        if (\Admin::user()) {
            return false;
        } else {
            return true;
        }
    }
}
/**
 * 取得当前ip
 *
 * @return   string                   ip
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:55:08+0800
 */
if (!function_exists('ip')) {
    function ip()
    {
        return request()->ip();
    }
}
if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string  $key
     * @param  mixed   $default
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}
/**
 *  返回 数值对应的中文意义
 *
 * @param    array                   $titles
 * @param    string/integer          $value
 *
 * @return   string
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-19T15:11:31+0800
 */
if (!function_exists('valueToTitle')) {
    function valueToTitle(array $titles, $value)
    {
        return $titles[$value] ?? __('common.not_found_title', ['title' => $value]);
    }
}
/**
 *  返回 DB schem 对应栏位的中文说明
 *
 * @param    string          $modelName
 * @param    string          $value
 *
 * @return   string
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-19T15:11:31+0800
 */
if (!function_exists('modelColumn')) {
    function modelColumn($modelName)
    {
        if (!is_string($modelName)) {
            $modelName = get_class($modelName);
        }
        $splitModel = explode('\\', $modelName);
        $splitModel = array_reverse($splitModel);
        $name = 'modelColumn' . ucfirst($splitModel[0]);
        return __($name);
    }
}
/**
 * 格式化例外
 *
 * @param    Exception                   $exception 例外
 *
 * @return   [type]                              [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-21T15:34:04+0800
 */
if (!function_exists('formatException')) {
    function formatException($exception, $forFlash = true, $forceShowFullError = false)
    {
        if ($forceShowFullError || config('app.env') != 'production') {
            $output = [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'message' => $exception->getMessage(),
            ];
            if (method_exists($exception, 'getPayload')) {
                $output['payload'] = $exception->getPayload();
            }
            if ($forFlash) {
                foreach ($output as $key => $value) {
                    if (!is_string($value)) {
                        $output[$key] = $key . ' : ' . json_encode($value, JSON_PRETTY_PRINT);
                    } else {
                        $output[$key] = $key . ' : ' . $value;
                    }
                }
            }
        } else {
            $output = [
                'message' => __('common.system_error', ['error_no' => 'EX' . uniqid()]),
            ];
            wl($exception);
        }
        return $output;
    }
}
/**
 * 写Log
 *
 * @param    [type]                   $data [description]
 *
 * @return   [type]                         [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-21T15:33:30+0800
 */
if (!function_exists('wl')) {
    function wl($data)
    {
        if (is_a($data, 'Exception')) {
            $output = [
                'line' => $data->getLine(),
                'file' => $data->getFile(),
                'message' => $data->getMessage(),
            ];
            if (method_exists($data, 'getPayload')) {
                $output['payload'] = $data->getPayload();
            }
            Log::debug(json_encode($output));
        } else {
            Log::debug(json_encode($data));
        }
    }
}
/**
 * 快速产生respoitory 查询字串
 *
 * @param    [type]                   $column  [description]
 * @param    string                   $rootUrl [description]
 *
 * @return   [type]                            [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-21T15:33:01+0800
 */
if (!function_exists('qs')) {
    function qs($column, $rootUrl = '')
    {
        $search = [];
        $searchFields = [];
        if ($column) {
            foreach ($column as $key => $value) {
                $search[] = $key . ':' . $value;
                $searchFields[] = $key;
            }
        }
        $seachParam = config('repository.criteria.params.search', 'search');
        $searchFieldsParam = config('repository.criteria.params.searchFields', 'searchFields');
        $outputString = $seachParam . '=' . implode(';', $search);
        $outputString .= '&' . $searchFieldsParam . '=' . implode(';', $searchFields);
        if ($rootUrl == '') {
            $rootUrl = request()->url();
        }
        $urlParts = parse_url($rootUrl);
        if (isset($urlParts['query'])) {
            parse_str($str, $output);
            unset($output[$seachParam], $output[$searchFieldsParam]);
            $queryString = http_build_query($output) . '&' . $outputString;
        } else {
            $queryString = '?' . $outputString;
        }
        $returnUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . ':' . $urlParts['port'] . $urlParts['path'] . $queryString;
        return $returnUrl;
    }
}
/**
 * 快速查询Repositoy 的search 指定的数值
 *
 * @param    [type]                   $column  [description]
 * @param    string                   $rootUrl [description]
 *
 * @return   [type]                            [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-21T14:44:39+0800
 */
if (!function_exists('qg')) {
    function qg($column)
    {
        $search = request()->get(config('repository.criteria.params.search', 'search'));
        $searchPart = explode(';', $search);
        foreach ($searchPart as $key => $value) {
            $inner = explode(':', $value);
            if (count($inner) == 2) {
                if (trim($inner[0]) == $column) {
                    return $inner[1];
                }
            }
        }
        return '';
    }
}
/**
 * 根据平台，决定后台主选单Model
 *
 * @return   [type]                   [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:57:55+0800
 */
if (!function_exists('whichMenuModel')) {
    function whichMenuModel()
    {
        $menu = App\Models\AdminMenu::class;
        switch (env('ADMIN_ROUTE_PREFIX')) {
            case 'manager':
                $menu = App\Models\ManagerMenu::class;
                break;
            default:
            case 'admin':
                $menu = App\Models\AdminMenu::class;
                break;
        }
        return $menu;
    }
}
/**
 * 取得字串的开头是否和输入一致
 *
 * @param    string                   $haystack 要比对的字串
 * @param    string                   $needle   检查字串是否在开头
 *
 * @return   boolean                             [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T09:58:20+0800
 */
if (!function_exists('startsWith')) {
    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}
/**
 * 取得字串的结尾是否和输入一致
 *
 * @param    string                   $haystack 要比对的字串
 * @param    string                   $needle   检查字串是否在结尾
 *
 * @return   boolean                             [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:00:06+0800
 */
if (!function_exists('endsWith')) {
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}
/**
 * 统一处理后台错误返回
 *
 * @param    Exception                   $ex 例外
 *
 * @return   Response                       [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:00:37+0800
 */
if (!function_exists('error')) {
    function error($ex)
    {
        admin_error('Error', implode(',', formatException($ex)));
        return redirect()->back()->withInput()->withErrors(formatException($ex));
    }
}

/**
 * 格式化时间
 *
 * @param    integer                   $period 时间长度
 *
 * @return   string                           格式化的时间
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:01:27+0800
 */
if (!function_exists('formatTime')) {
    function formatTime($period)
    {
        if (!is_numeric($period)) {
            return $period;
        }
        //计算天
        $day = floor($period / 86400);
        $hour = floor(($period - $day * 86400) / 3600);
        $minute = floor(($period - $day * 86400 - $hour * 3600) / 60);
        $seconds = (($period - $day * 86400 - $hour * 3600 - $minute * 60));
        $output = '';
        if ($day) {
            $output .= str_pad($day, 2, "0", STR_PAD_LEFT) . __('common.day');
        }
        if ($hour) {
            $output .= str_pad($hour, 2, "0", STR_PAD_LEFT) . __('common.hour');
        }
        if ($minute) {
            $output .= str_pad($minute, 2, "0", STR_PAD_LEFT) . __('common.minute');
        }
        if ($seconds) {
            $output .= str_pad($seconds, 2, "0", STR_PAD_LEFT) . __('common.second');
        }
        return $output;
    }
}
/**
 * 返回系统设定
 *
 * @param    [type]                   $slug    [description]
 * @param    string                   $default [description]
 *
 * @return   [type]                            [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:02:58+0800
 */
if (!function_exists('sc')) {
    function sc($slug, $default = "")
    {
        if (class_exists(\Cache::class)) {
            $systems = \Cache::get(\App\Models\SystemConfig::CACHE_KEY);
            if (!$systems) {
                resetSystemConfig();
            }
            return $systems[$slug]['value'] ?? $default;
        } else {
            return $default;
        }
    }
}
/**
 * 重设系统设定
 *
 * @param    [type]                   $slug    [description]
 * @param    string                   $default [description]
 *
 * @return   [type]                            [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-03T16:16:24+0800
 */
if (!function_exists('resetSystemConfig')) {
    function resetSystemConfig()
    {
        $all = app(\App\Models\SystemConfig::class)->all()->keyBy('slug')->toArray();
        \Cache::forever(\App\Models\SystemConfig::CACHE_KEY, $all);
    }
}
/**
 * 語系檔合併
 *
 * @param    array                   $originLang       原來的語系檔
 * @param    String                   $assignColumnNamePrefix 新的語系檔的前綴
 * @param    array                   $targetLang       新插入的語系檔
 *
 * @return   array                                     合併後的語系檔
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-27T14:11:25+0800
 */
if (!function_exists('injectLocale')) {
    function injectLocale(&$originLang, $assignColumnNamePrefix, $targetLang)
    {
        if (!is_array($originLang)) {
            throw new \Exception(__('not_found_model_column', ['model' => $originLang]));
        }
        if ($targetLang && is_array($targetLang)) {
            foreach ($targetLang as $key => $value) {
                $columnKey = $assignColumnNamePrefix . '.' . $key;
                $originLang[$columnKey] = $value;
            }
        }

        return $originLang;
    }
}

/**
 * 等級經驗對照資料
 *
 * @param    [type]                   $slug    [description]
 * @param    string                   $default [description]
 *
 * @return   [type]                            [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:02:58+0800
 */
if (!function_exists('getLevelMap')) {
    function getLevelMap()
    {
        $levelMap = \Cache::get(\App\Models\BaseLevel::CACHE_KEY);
        if ($levelMap == null || $levelMap == []) {
            // 寫入快取
            $levelMap = app(\App\Models\BaseLevel::class)->all()->keyBy('lv')->toArray();
            \Cache::forever(\App\Models\BaseLevel::CACHE_KEY, $levelMap);
        }

        return $levelMap;
    }
}

/**
 * 透过经验值取得當前等級
 *
 * @param    integer                  $currentExp 當前經驗值
 *
 * @return   integer                               等級
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-27T14:47:23+0800
 */
if (!function_exists('getCurrentLevelByExp')) {
    function getCurrentLevelByExp($currentExp = 0)
    {
        $levelMap = getLevelMap();
        $currentLevel = 1;
        foreach ($levelMap as $level => $data) {
            if ($currentExp >= $data['exp_sum']) {
                $currentLevel++;
            } else {
                break;
            }
        }
        return $currentLevel;
    }
}
/**
 * 透过等级取得所需经验值
 *
 * @param    integer                  $level 等級
 *
 * @return   integer                  所需經驗值
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-27T14:47:23+0800
 */
if (!function_exists('getWhichExpByLevel')) {
    function getWhichExpByLevel($level = 1)
    {
        // 取得上一級的exp_sum
        $level = $level - 1;
        $levelMap = getLevelMap();
        $exp = $levelMap[$level]['exp_sum'] ?? 0;
        return $exp;
    }
}
/**
 * 编码储存路径
 *
 * @param    string                  $path 路经
 *
 * @return   string                  编码后的储存路径
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-27T14:47:23+0800
 */
if (!function_exists('encodeStoragePath')) {
    function encodeStoragePath($path)
    {
        $tmp = pathinfo($path);
        $extension = $tmp['extension'] ?? '';
        $extension = (isset($tmp['extension']) && ($tmp['extension'] != '')) ? '.' . $extension : '';
        return '/storage/' . (base64_encode($path) . $extension);
    }
}
/**
 * 解码储存路径
 *
 * @param    string                  编码后的储存路径
 *
 * @return   string                  编码前的路径
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-08-27T14:47:23+0800
 */
if (!function_exists('decodeStoragePath')) {
    function decodeStoragePath($fileName)
    {
        if (config('filesystems.default') == 'local') {
            $pathinfo = pathinfo($fileName);
            $extension = $pathinfo['extension'] ?? '';
            $extension = (isset($tmp['extension']) && ($tmp['extension'] != '')) ? '.' . $extension : '';
            $tmp = str_replace($extension, '', $fileName);
            $path = base64_decode($tmp);
            return $path;
        } else {
            return $fileName;
        }

    }
}

/**
 * 將switch的on/off轉換成1/0
 */
if (!function_exists('switchValueToBoolean')) {
    function switchValueToBoolean($switch)
    {
        if ($switch == 'on') {
            return 1;
        } else {
            return 0;
        }
    }
}

/**
 * 取得經濟公司報表結算日期區間
 * 1號~10號 / 11號~20號 / 21號~月底
 *
 * @param int $monthOffset
 */
if (!function_exists('getManagerCompanyReportSettledRange')) {
    function getManagerCompanyReportSettledRange(int $monthOffset = 0)
    {
        $startMonthDatetime = \Carbon\Carbon::now()->addMonth($monthOffset)->startOfMonth();
        $endMonthDatetime = \Carbon\Carbon::now()->addMonth($monthOffset)->endOfMonth();

        $firstStartRange = $startMonthDatetime->format('Y-m-d');
        $firstEndRange = $startMonthDatetime->addDays(9)->format('Y-m-d');

        $secondStartRange = $startMonthDatetime->addDays(1)->format('Y-m-d');
        $secondEndRange = $startMonthDatetime->addDays(9)->format('Y-m-d');

        $thirdStartRange = $startMonthDatetime->addDays(1)->format('Y-m-d');
        $thirdEndRange = $endMonthDatetime->format('Y-m-d');

        return [
            'first_range' => [
                'start' => $firstStartRange,
                'end' => $firstEndRange,
            ],
            'second_range' => [
                'start' => $secondStartRange,
                'end' => $secondEndRange,

            ],
            'third_range' => [
                'start' => $thirdStartRange,
                'end' => $thirdEndRange,
            ],
        ];
    }
}
/**
 * 將date轉成datetime
 *
 * @param bool $isMin
 */
if (!function_exists('dateToDatetime')) {
    function dateToDatetime($date, $isMin = false)
    {
        if ($isMin) {
            return $date . ' 00:00:00';
        }
        return $date . ' 23:59:59';
    }
}
/**
 * 替换 语系字串，直接用array
 *
 * @param    array                   $locales    语系档指定的阵列
 * @param    string                   $key        语系对应的key
 * @param    array                $replaceAry 要取代的字元组
 *
 * @return   string                               对应的语系
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-09-10T14:42:24+0800
 */
if (!function_exists('lc')) {
    function lc($locales, $key, $replaceAry = [])
    {
        if (!is_array($locales)) {
            return $locales . '.' . $key;
        }
        if (isset($locales[$key])) {
            if ($replaceAry && is_array($replaceAry)) {
                $keys = array_keys($replaceAry);
                $values = array_values($replaceAry);
                array_walk($keys, function (&$value, $key) {$value = ':' . $value;});
                $str = str_replace($keys, $values, $locales[$key]);
            } else {
                $str = $locales[$key];
            }
            return $str;
        } else {
            return $key;
        }
    }
}
/**
 * 取得用户讯息服务实例
 *
 * @return   UserMessageService                   [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:36:14+0800
 */
if (!function_exists('userMessage')) {
    function userMessage()
    {
        return app(App\Services\UserMessageService::class);
    }
}
/**
 * 金币转现金
 *
 * @param    [type]                   $gold [description]
 *
 * @return   [type]                         [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-09-25T09:34:43+0800
 */
if (!function_exists('goldToCoin')) {
    function goldToCoin($gold)
    {
        $ratio = sc('coinRatio');
        if (!$ratio) {
            //重新设定
            resetSystemConfig();
        }
        return round($gold / $ratio);
    }
}
/**
 * 现金转金币
 *
 * @param    [type]                   $gold [description]
 *
 * @return   [type]                         [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-09-25T09:34:55+0800
 */
if (!function_exists('coinToGold')) {
    function coinToGold($gold)
    {
        $ratio = sc('coinRatio');
        return ($gold * $ratio);
    }
}

/**
 * 替换 语系字串，直接用array
 *
 * @param    array                   $locales    语系档指定的阵列
 * @param    string                   $key        语系对应的key
 * @param       array                $replaceAry 要取代的字元组
 *
 * @return   string                               对应的语系
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-09-10T14:42:24+0800
 */
if (!function_exists('batchReplaceLocaleByArray')) {
    function batchReplaceLocaleByArray($localesKey, $replaceAry = [])
    {
        $locales = __($localesKey);
        if (!is_array($locales)) {
            throw new \Exception(__('not_is_array'));
        }
        foreach ($locales as $key => $val) {
            if ($replaceAry && is_array($replaceAry)) {
                $keys = array_keys($replaceAry);
                $values = array_values($replaceAry);
                array_walk($keys, function (&$value, $key) {$value = ':' . $value;});
                $str = str_replace($keys, $values, $locales[$key]);
            } else {
                $str = $locales[$key];
            }
            $locales[$key] = $str;
        }
        return $locales;
    }
}
/**
 * 替换 语系字串，直接用array
 *
 * @param    array                   $locales    语系档指定的阵列
 * @param    string                   $key        语系对应的key
 * @param       array                $replaceAry 要取代的字元组
 *
 * @return   string                               对应的语系
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-09-10T14:42:24+0800
 */
if (!function_exists('modelName')) {
    function modelName($modelClass)
    {
        $modelNameParts = explode('\\', $modelClass);
        $modelName = end($modelNameParts);
        return __('modelName.' . $modelName);
    }
}

/**
 * 取得当下平台的使用者id
 *
 * @return   [type]                   [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-09-17T15:38:33+0800
 */
if (!function_exists('platformId')) {
    function platformId()
    {
        switch (config('app.currentenv', 'admin')) {
            case 'api':
                $userId = id();
                break;
            case 'admin':
            default:
                $userId = adminId();
                break;
        }
        return $userId;
    }
}
/**
 * 取得四方交易实例
 *
 * @param    string                   $channel     渠道号
 * @param    string                   $paymentType 交易方式
 *
 * @return   App\Services\Payments\Instances\{$channel}\{$paymentType}Payment
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:39:13+0800
 */
if (!function_exists('payment')) {
    function payment($channel, $paymentType)
    {
        $paymentManager = app(PaymentManager::class);
        return $paymentManager->getInstance($channel, $paymentType);
    }
}
/**
 * 取得四方渠道通知实例
 *
 * @param    string                   $channel 渠道
 *
 * @return   \App\Services\Payments\{$channel}Notify
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:42:52+0800
 */
if (!function_exists('channel')) {
    function channel($channel)
    {
        $notifyManager = app(NotifyManager::class);
        return $notifyManager->getInstance($channel);
    }
}

/**
 * 根据当前Model取得对应的Repository
 *
 * @param    App\Models\BaseModel                   $modelClass
 *
 * @return   string                               对应的Repository
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:44:44+0800
 */
if (!function_exists('getRepository')) {
    function getRepository($modelClass)
    {
        $modelNameParts = explode('\\', get_class($modelClass));
        $modelName = end($modelNameParts);
        $generator = app(\Prettus\Repository\Generators\BindingsGenerator::class, ['options' => ['name' => $modelName]]);
        $repositoryName = ($generator->getEloquentRepository());
        return $repositoryName;
    }
}
/**
 * 根据当前Model Name取得对应的Repository
 *
 * @param    App\Models\BaseModel                   $modelClass
 *
 * @return   string                               对应的Repository
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-04T10:44:44+0800
 */
if (!function_exists('getRepositoryByName')) {
    function getRepositoryByName($modelClass)
    {
        $modelNameParts = explode('\\', $modelClass);
        $modelName = end($modelNameParts);
        $generator = app(\Prettus\Repository\Generators\BindingsGenerator::class, ['options' => ['name' => $modelName]]);
        $repositoryName = ($generator->getEloquentRepository());
        return $repositoryName;
    }
}
/**
 * 清除 Model 快取
 *
 * @param    App\Models\BaseModel                   $modelClass
 *
 * @return   void
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-03T16:10:56+0800
 */
if (!function_exists('cleanRepositoryCache')) {
    function cleanRepositoryCache($model)
    {
        if (!config("repository.cache.clean.enabled", true)) {
            return true;
        }
        //检查如果只有金额变动
        if (method_exists($model, 'getGoldUpdateSourceModel') && $model->getGoldUpdateSourceModel()) {
            //只有变更金币的话，不做快取更新
            return true;
        }

        $wrapRepository = getRepository($model);
        if (class_exists($wrapRepository)) {
            $reflect = app($wrapRepository);
            if ($reflect instanceof \Prettus\Repository\Contracts\CacheableInterface) {
                $cleanEnabled = config("repository.cache.clean.enabled", true);
                if ($cleanEnabled) {
                    $calledClass = str_replace("\\", "_", $wrapRepository);

                    $key = config('cache.prefix') . sprintf(':l5_auto_cache:%s:*', $calledClass);
                    $redis = \Cache::store('redis')->getRedis();

                    $allKey = $redis->keys($key);

                    foreach ($allKey as $pKey) {
                        $redis->del($pKey);
                    }
                }
            }
        }
    }
}

/**
 * 發送Email
 *
 * @param    string                   $to            目標email
 * @param    string                   $view  指定的樣版路徑，空白為 mail.common
 * @param    array                    $mailData      樣版取用的資料，預設會包在mailData裡面
 * @param    array                   $extendOptions 額外方法(bcc、cc，以後再補上)
 *
 * @return   [type]                                  [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-07T16:02:26+0800
 */
if (!function_exists('smail')) {
    function smail($to, $subject = '', $view = '', $mailData = [], $extendOptions = [])
    {
        \Mail::to($to)
            ->queue((new \App\Mail\Mail($subject, $view, $mailData))->onQueue('mails'))
        ;
    }
}

/**
 * 從快取取得tencent live 設定檔
 */
if (!function_exists('getTencentLiveConfigFromCache')) {
    function getTencentLiveConfigFromCache()
    {
        $tencentLiveConfig = sc('tencent');
        if (!isset($tencentLiveConfig['TENCENT_IM_SDK_APPID'])) {
            resetSystemConfig();
            $tencentLiveConfig = sc('tencent');
        }
        return [
            'headers' => [
                'Content-Type' => 'application/json;charset=utf-8',
            ],
            'domain' => $tencentLiveConfig['TENCENT_LIVE_CONSOLE_DOMAIN'] ?? '',
            'code' => ['success' => 200],
            'log_filename' => 'tencent',
            'api_path' => [],
            'system' => [
                // 'app_id' => $tencentLiveConfig['TENCENT_LIVE_APPID'], //先注记不用
                'play_app_id' => $tencentLiveConfig['TENCENT_IM_SDK_APPID'], // 从 TENCENT_LIVE_PLAY_APPID 更名为 TENCENT_IM_SDK_APPID
                'domain' => $tencentLiveConfig['TENCENT_LIVE_DOMAIN'],
                'play_domain' => $tencentLiveConfig['TENCENT_LIVE_PLAY_DOMAIN'],
                'app_name' => $tencentLiveConfig['TENCENT_LIVE_APP_NAME'],
                'key' => $tencentLiveConfig['TENCENT_LIVE_KEY'],
                'locale' => $tencentLiveConfig['TENCENT_LIVE_LOCALE'],
                'app' => $tencentLiveConfig['TENCENT_LIVE_APP'],
                'identifier' => $tencentLiveConfig['TENCENT_IM_IDENTIFIER'] ?? 'ddliveadmin',
                'secret_id' => $tencentLiveConfig['TENCENT_LIVE_SECRET_ID'],
                'secret_key' => $tencentLiveConfig['TENCENT_LIVE_SECRET_KEY'],
                'end_point' => $tencentLiveConfig['TENCENT_LIVE_END_POINT'],
                'live_call_back_key' => $tencentLiveConfig['TENCENT_LIVE_CALLBACK_KEY'],
                'private_key' => file_get_contents(base_path($tencentLiveConfig['TENCENT_LIVE_PRIVATE_KEY_PATH'])),
                'public_key' => file_get_contents(base_path($tencentLiveConfig['TENCENT_LIVE_PUBLIC_KEY_PATH'])),
            ],
        ];
    }
}

/**
 * 設置Redis Lock
 *
 * @param    [type]                   $key [description]
 * @param    integer                  $ttl [description]
 *
 * @return   [type]                        [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-15T09:39:28+0800
 */
if (!function_exists('lock')) {

    function redisLock($key, $ttl = 10)
    {
        $redis = \Cache::store('redis')->getRedis();
        // $redis->multi();
        $isLock = $redis->setnx($key, 1);
        $redis->expire($key, $ttl);
        // $redis->exec();
        return $isLock;
    }
}
/**
 * 釋放 Redis lock
 *
 * @param    [type]                   $key [description]
 * @param    integer                  $ttl [description]
 *
 * @return   [type]                        [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-15T09:39:28+0800
 */
if (!function_exists('releaseRedisLock')) {
    function releaseRedisLock($key)
    {
        $redis = \Cache::store('redis')->getRedis();
        $redis->del($key);
    }
}

/**
 * 返回RepositoryCacheKey 相容於 \Prettus\Repository 的快取
 *
 * @param $method
 * @param $args
 *
 * @return string
 */
if (!function_exists('repositoryCacheKey')) {
    function repositoryCacheKey($className, $method, $args = null)
    {
        $instance = app($className);
        return $instance->getCacheKey($method, $args);
    }
}

/**
 * 下底線轉成駝峰命名
 *
 * @param    string                   $string                   [description]
 * @param    boolean                  $capitalizeFirstCharacter 第一個字為小寫
 *
 * @return   string                                             [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-15T11:08:32+0800
 */
if (!function_exists('underLineToCamelCase')) {
    function underLineToCamelCase($string, $capitalizeFirstCharacter = true)
    {

        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return ($str);
    }
}
/**
 * 取得付款类型图示
 *
 * @param    string                   $slug 付款类型
 *
 * @return   string                   图示网址
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-23T10:53:51+0800
 */
if (!function_exists('payTypeIcon')) {

    function payTypeIcon($slug)
    {
        $service = app(App\Services\PayTypeIconService::class);
        return $service->getIconBySlug($slug);

    }
}
/**
 * 取得对外路由
 *
 * @param    string                   $routeName 路由名称
 * @param    [type]                   $options   [description]
 *
 * @return   [type]                              [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-23T10:35:22+0800
 */
if (!function_exists('externalRoute')) {
    function externalRoute($routeName, $options)
    {
        $url = route($routeName, $options);
        $urlParts = parse_url($url);
        $newUrl = config('app.external_url');
        $newUrl .= ':';
        $newUrl .= $urlParts['port'] ?? '80';
        $newUrl .= $urlParts['path'] ?? '';
        if (isset($urlParts['query'])) {
            $newUrl .= '?' . $urlParts['query'];
        }
        return $newUrl;
    }
}
/**
 * 取得队列池，若在设定档找不到，则跑default这个池
 */
if (!function_exists('pool')) {
    function pool($pool)
    {
        return config('queue.pools.' . $pool) ?? 'default';
    }
}
/**
 * 取得熱度計算公式
 *
 * @return   [type]                   [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-29T10:55:02+0800
 */
if (!function_exists('getBaseHotConfig')) {
    function getBaseHotConfig()
    {
        $config = \Cache::get(\App\Models\BaseHotConfigure::CACHE_KEY);
        if (!$config) {
            return resetBaseHotConfig();
        }
        return $config;
    }
}
/**
 * 更新熱度計算公式
 *
 * @return   [type]                   [description]
 *
 * @Author   Peter(yj@tiigod.com
 *
 * @DateTime 2019-10-29T10:37:51+0800
 */
if (!function_exists('resetBaseHotConfig')) {

    function resetBaseHotConfig()
    {
        $all = app(\App\Repositories\Interfaces\BaseHotConfigureRepository::class)->all()->toArray();
        \Cache::forever(\App\Models\BaseHotConfigure::CACHE_KEY, $all);
        return $all;
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @param  string  $language
     * @return string
     */
    function str_slug($title, $separator = '-', $language = 'en')
    {
        return \Illuminate\Support\Str::slug($title, $separator, $language);
    }
}

if (!function_exists('resolve')) {
    /**
     * Resolve a service from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    function resolve($name)
    {
        return app($name);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function app_path($path = '')
    {
        return app('path') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
