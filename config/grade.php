<?php
//熱度計算公式，請依
// https://tigo-svn.tigo.local/svn/2019GYLIVE/03_design/4_規劃文件/數值規劃設計/直播熱度文檔.doc 為準
//觀看人數*reward['addGrade']+遊戲人數*reward['addGrade']+瞬間人數增長積分+瞬間送禮增長積分+瞬間遊戲押注增長積分+道具使用積分
//
//fix 為 固定觀看人數的規則
//dynamic 為 瞬間計算的規則
// 單一規則為三維陣列
// 第一維度 觀察的 Model 名稱
// 第二維度 監測的指標欄位名稱
// 第三維度為指標符合的數值範圍，若有符合，則提取該範圍的 reward
return [
    //固定
    'fix' => [
        //直播人數
        'LiveRoom' => [
            'real_user_number' => [
                [
                    'min' => 1,
                    'max' => 100,
                    'time' => 0,
                    'reward' => [
                        'addGrade' => 1, //每一個用戶的熱度
                        'addTime' => 0, //持續分鐘
                    ],
                ],
                [
                    'min' => 101,
                    'max' => 300,
                    'time' => 0,
                    'reward' => [
                        'addGrade' => 2,
                        'addTime' => 0,
                    ],
                ],
                [
                    'min' => 201,
                    'max' => 9999,
                    'time' => 0,
                    'reward' => [
                        'addGrade' => 3,
                        'addTime' => 0,
                    ],
                ],
            ],
        ],
        'game_players' => [
            [
                'min' => 1,
                'max' => 50,
                'time' => 0,
                'reward' => [
                    'addGrade' => 1,
                    'addTime' => 0,
                ],
            ],
            [
                'min' => 51,
                'max' => 150,
                'time' => 0,
                'reward' => [
                    'addGrade' => 2,
                    'addTime' => 0,
                ],
            ],
            [
                'min' => 151,
                'max' => 9999,
                'time' => 0,
                'reward' => [
                    'addGrade' => 3,
                    'addTime' => 0,
                ],
            ],
        ],
    ],
    //動態，用Queue 去做
    'dynamic' => [
        'LiveRoom' => [
            'real_user_number' => [
                [
                    'min' => 30,
                    'max' => 50,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 3,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
                [
                    'min' => 51,
                    'max' => 100,
                    'time' => 10,
                    'reward' => [
                        'addGrade' => 5,
                        'addTime' => 10,
                    ],
                ],
                [
                    'min' => 101,
                    'max' => 9999,
                    'time' => 10,
                    'reward' => [
                        'addGrade' => 3,
                        'addTime' => 0,
                    ],
                ],
            ],
        ],
        'GiftTransactionOrder' => [ //送禮
            'gold_price' => [
                [
                    'min' => 1000000,
                    'max' => 3000000,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 3,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
                [
                    'min' => 3000001,
                    'max' => 7000000,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 5,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
                [
                    'min' => 7000001,
                    'max' => 99999999,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 7,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
            ],
        ],
        'GameBetRecord' => [ //下注
            'bet_gold' => [
                [
                    'min' => 1000000,
                    'max' => 3000000,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 3,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
                [
                    'min' => 3000001,
                    'max' => 7000000,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 5,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
                [
                    'min' => 7000001,
                    'max' => 99999999,
                    'time' => 10, //十分鐘
                    'reward' => [
                        'addGrade' => 7,
                        'addTime' => 10, //持續十分鐘
                    ],
                ],
            ],
        ],
    ],
];
