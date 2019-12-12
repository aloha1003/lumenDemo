<?php
namespace App\Models\Observers;

// use App\Models\ModelLog;

class ModelLogObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function created($model)
    {
        if ($model->getIsLog()) {
            $this->writeLog($model);
        }
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function saved($model)
    {
        if ($model->getIsLog()) {
            $this->writeLog($model);
        }
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function deleting($model)
    {
        if ($model->getIsLog()) {
            $this->writeLog($model);
        }
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function restored($model)
    {
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function forceDeleted($model)
    {
        //
    }

    private function writeLog($model)
    {
        $request = json_encode(request()->all());
        if ($model->id) {
            \Queue::pushOn('log', new \App\Jobs\ModelLogJob($model, config('app.currentenv', 'admin'), $request, platformId(), ip(), $model->getOriginal()));
        }
    }

    private function modelDiff($model)
    {
        $origin = $model->getOriginal();

        $now = $model->toArray();

        if ($origin) {
            foreach ($now as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    unset($now[$key]);
                }
            }
            foreach ($origin as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    unset($origin[$key]);
                }
            }
            $diff = array_diff($now, $origin);
        } else {
            $diff = $now;
        }
        return $diff;
    }

}
