<?php

namespace App\Traits;

trait RepositoryLostMethod
{
    /**
     * 重构 findWhere，加上 in 、 not_in
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($innerField, $condition, $val) = $innerValue;
                switch ($condition) {
                    case 'in':
                        $this->model->whereIn($field, $val);
                        break;
                    case 'not_in':
                        $this->model->whereNotIn($field, $val);
                        break;
                    default:
                        $this->model = $this->model->where($innerField, $condition, $val);
                        break;
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }

        $model = $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }
}
