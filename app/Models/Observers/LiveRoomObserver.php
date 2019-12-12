<?php
namespace App\Models\Observers;

class LiveRoomObserver
{
    public function saved($model)
    {
        //TODO
        if ($model->status == $model::STATUS_FORBIDDEN) {
            try {
                //TODO
                \Live::cut(['stream_name' => $model->id]);
            } catch (\Exception $ex) {
                wl($ex);
            }
        }
    }
}
