<?php

namespace App\Models;

use App\Models\Observers\CleanCacheObserver;
use App\Models\Observers\ModelLogObserver;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $isLog = true;
    /**
     * 關聯的ID 對應
     *
     * @var array
     */
    protected $relationShipIdMap = [];
    public static function getTableName()
    {
        return with(new static )->getTable();
    }

    public function getIsLog()
    {
        return $this->isLog;
    }

    public function setWrapRepository($className)
    {
        $this->wrapRepository = $className;
        return $this;
    }
    protected static function boot()
    {
        parent::boot();
        static::observe(ModelLogObserver::class);
        static::observe(CleanCacheObserver::class);
    }

    public static function getEmptyModel()
    {
        $column = modelColumn(static::class);
        $default = [];
        if (is_array($column)) {
            $columnKeys = array_keys($column);
            $default = array_fill_keys($columnKeys, '');
        }
        return $default;
    }

    /**
     * 新增或更新多筆資料
     *
     * @param array $rows
     */
    public function multiInsertOrUpdate(array $rows)
    {
        /**
         * 使用範例
         *
         * $this->multiInsertOrUpdate([
         *   ['column_a'=>1,'column_a'=>100],
         *   ['column_a'=>2,'column_b'=>200]
         * ]);
         *
         */

        // 若為空array, 不寫入DB
        if (count($rows) == 0) {
            return;
        }

        // 若有時用 laravel timestamps 補上 created_at, updated_at
        if ($this->usesTimestamps()) {
            foreach ($rows as $key => $row) {
                $rows[$key]['created_at'] = $this->freshTimestamp();
                $rows[$key]['updated_at'] = $this->freshTimestamp();
            }
        }
        // 取得 table name
        $table = static::getTableName();

        // 取得array的第一筆資料
        $first = reset($rows);

        // 要寫入資料的key
        $valuesKeys = array_keys($first);

        // 要更新資料的key, 更新時不更新created_at
        $updateKeys = $valuesKeys;
        if ($this->usesTimestamps()) {
            if (($key = array_search('created_at', $updateKeys)) !== false) {
                unset($updateKeys[$key]);
            }
        }

        // 組字串, 要寫入的db的欄位
        $columns = implode(',',
            array_map(function ($value) {return "$value";}, $valuesKeys)
        );

        // 組字串, 要寫入的db的值
        $values = implode(',', array_map(function ($row) {
            return '(' . implode(',',
                array_map(function ($value) {return '"' . str_replace('"', '""', $value) . '"';}, $row)
            ) . ')';
        }, $rows)
        );

        // 組字串, 要更新的db的欄位
        $updates = implode(',',
            array_map(function ($value) {return "$value = VALUES($value)";}, $updateKeys)
        );

        // 組sql語法
        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

        //用statement寫入資料庫
        return \DB::statement($sql);
    }

    public function saveOriginalOnly()
    {
        $dirty = $this->getDirty();

        foreach ($this->getAttributes() as $key => $value) {
            if (!in_array($key, array_keys($this->getOriginal()))) {
                unset($this->$key);
            }
        }

        $isSaved = $this->save();
        foreach ($dirty as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $isSaved;
    }
}
