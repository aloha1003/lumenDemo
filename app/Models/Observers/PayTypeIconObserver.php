<?php
namespace App\Models\Observers;

class PayTypeIconObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function created($model)
    {
        $this->mergePayType();
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function saved($model)
    {
        $this->mergePayType();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function deleted($model)
    {
        $this->mergePayType();
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function restored($model)
    {
        $this->mergePayType();
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function forceDeleted($model)
    {
        $this->mergePayType();
    }

    public function mergePayType()
    {
        $service = app(\App\Services\PayTypeIconService::class);
        $service->refreshPayTypeIcon();
    }

}
