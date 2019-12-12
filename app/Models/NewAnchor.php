<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\EloquentSortable\Sortable;
use App\Models\EloquentSortable\SortableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class NewAnchor.
 *
 * @package namespace App\Models;
 */
class NewAnchor extends Model implements Transformable, Sortable
{
    use TransformableTrait;
    use SortableTrait;

    protected $fillable = ['user_id', 'weight'];
    public $sortable = [
        'order_column_name' => 'weight',
        'sort_when_creating' => true,
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
