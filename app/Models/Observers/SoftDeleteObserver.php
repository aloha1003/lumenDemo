<?php
namespace App\Models\Observers;

class SoftDeleteObserver
{
    public function deleting($model)
    {
        if (method_exists($model, 'forceDelete')) {
            $column = $model->getDeletedAtColumn();
            $model->name = $model->name . '_forceDelete@' . $model->$column;
        }
    }
}
