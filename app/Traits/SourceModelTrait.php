<?php
namespace App\Traits;

/**
 * 透过SourceModel 取得 Model instance
 */
trait SourceModelTrait
{
    protected $_source_model_name = 'source_model_name';
    protected $_source_model_primary_key_column = 'source_model_primary_key_column';
    protected $_source_model_primary_key_id = 'source_model_primary_key_id';
    /**
     * 取得修改来源
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-25T13:15:13+0800
     */
    public function source()
    {
        return $this->hasOne($this->{$this->_source_model_name}, $this->{$this->_source_model_primary_key_column}, $this->_source_model_primary_key_id)->get();
    }

    public function getModelName($modelName)
    {
        return modelName($modelName);
    }
}
