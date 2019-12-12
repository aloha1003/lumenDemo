<?php

/**
 * 網易雲信 Server 端 API 文件: 
 *  https://dev.yunxin.163.com/docs/product/%E7%9B%B4%E6%92%AD/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3
 */
return [
    'headers' => [
        'Content-Type' => 'application/json;charset=utf-8',
    ],
    'domain' => 'https://vcloud.163.com/',
    'code' => [
        'success' => 200,
    ],
    'log_filename' => 'netease',
    'api_path' => [
        /**
         * [POST]建立頻道
         */
        'create' => 'app/channel/create',
        /**
         * [POST]修改頻道
         */
        'update' => 'app/channel/update',
        /**
         * [POST]刪除頻道
         */
        'delete' => 'app/channel/delete',
        /**
         * [POST]取得頻道狀態
         */
        'get_status' => 'app/channel/channelstats',
        /**
         * [POST]取得頻道列表
         */
        'lists' => 'app/channel/channellist',
        /**
         * [POST]取得推流地址
         */
        'address' => 'app/address',
        /**
         * [POST]設定為錄製狀態
         */
        'set_record' => 'app/channel/setAlwaysRecord',
        /**
         * [POST]禁用頻道
         */
        'pause' => 'app/channel/pause',
    ],
];
