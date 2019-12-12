<?php

namespace App\Models\Observers;

//更新熱度計算公式快取
class RefreshBaseHotConfigureCacheObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function created($model)
    {
        resetBaseHotConfig();
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function updated($model)
    {
        resetBaseHotConfig();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function deleting($model)
    {
        resetBaseHotConfig();
    }
}
