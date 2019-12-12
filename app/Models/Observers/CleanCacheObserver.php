<?php

namespace App\Models\Observers;

//清除 repository 的查询快取
class CleanCacheObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function created($model)
    {
        cleanRepositoryCache($model);
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function deleted($model)
    {
        cleanRepositoryCache($model);
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function restored($model)
    {
        cleanRepositoryCache($model);
    }

    public function saved($model)
    {
        cleanRepositoryCache($model);
    }

}
