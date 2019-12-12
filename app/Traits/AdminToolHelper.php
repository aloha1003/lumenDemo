<?php

namespace App\Traits;

use Encore\Admin\Form\Field;
trait AdminToolHelper
{
    /**
     * 将语系、设定，塞到 Encore\Admin\Form
     * 就像 $grid->column('column', '语系')
     * 再透过 buildChain 达成方法的连锁
     *
     * @param    [type]                   &$form       [description]
     * @param    [type]                   $viewColumn  [description]
     * @param    [type]                   $modelColumn [description]
     *
     * @return   [type]                                [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T09:29:46+0800
     */
    public function assignFormField(&$form, $viewColumn, $modelColumn)
    {
        $isEmpty = empty($form->model()->toArray());
        foreach ($viewColumn as $key => $val) {
            if (is_int($key)) {
                $column = $modelColumn[$val] ?? $val;
                if (!$isEmpty) {
                    $form->text($val, $column)->default(function ($form) use ($val) {
                        $field = explode('.', $val);
                        if (count($field) > 1) {
                            $relation = $field[0];
                            $relationColumn = $field[1];
                            return $form->model()->$relation->$relationColumn;
                        } else {
                            return $form->model()->$val;
                        }
                    });
                } else {
                    $form->text($val, $column);
                }
            } else {
                $column = $modelColumn[$key] ?? $key;
                $method = $val['method'] ?? 'text';
                $methodCallback = $val['methodCallback'] ?? null;
                if ($method == 'wangEditor' && is_null($form->model()->$key)) {
                    $form->model()->$key = '&nbsp;';
                }
                if ($method == 'password') {
                    if (!isset($val['chains'])) {
                        $val['chains'] = [];
                    }
                    $originAttribute = $val['chains']['attribute'] ?? [];
                    $val['chains']['attribute'] = array_merge($originAttribute, ['autocomplete' => 'new-password']);
                }
                if (isset($val['chains']) && $val['chains']) {
                    if (!$isEmpty) {
                        $this->buildChain($form->$method($key, $column, $methodCallback)->default(function ($form) use ($method, $key) {
                            if ($method == 'password') {
                                return "";
                            }
                            $field = explode('.', $key);
                            if (count($field) > 1) {
                                $relation = $field[0];
                                $relationColumn = $field[1];
                                return $form->model()->$relation->$relationColumn;
                            } else {
                                return $form->model()->$key;
                            }
                        }), $val['chains']);
                    } else {
                        $this->buildChain($form->$method($key, $column, $methodCallback), $val['chains']);
                    }
                } else {
                    if (!$isEmpty) {
                        $form->$method($key, $column, $methodCallback)->default(function ($form) use ($key, $method) {
                            if ($method == 'password') {
                                return "";
                            }
                            $field = explode('.', $key);
                            if (count($field) > 1) {
                                $relation = $field[0];
                                $relationColumn = $field[1];
                                return $form->model()->$relation->$relationColumn;
                            } else {
                                return $form->model()->$key;
                            }
                        });
                    } else {
                        $form->$method($key, $column, $methodCallback);
                    }
                }
            }
        }
        $data = $form->model()->toArray();
        $form->builder()->fields()->each(function (Field $field) use ($data) {
            $field->fill($data);
        });
    }
    /**
     * 将语系、设定，塞到 Encore\Admin\Grid
     * 就像 $grid->column('column', '语系')
     * 再透过 buildChain 达成方法的连锁
     *
     * @param    [type]                   &$form       [description]
     * @param    [type]                   $viewColumn  [description]
     * @param    [type]                   $modelColumn [description]
     *
     * @return   [type]                                [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T09:29:46+0800
     */
    public function assignGridField(&$form, $viewColumn, $modelColumn)
    {
        foreach ($viewColumn as $key => $val) {
            if (is_int($key)) {
                $form->column($val, $modelColumn[$val] ?? $val);
            } else {
                $column = $modelColumn[$key] ?? $key;
                $method = $val['method'] ?? 'column';
                if (isset($val['chains']) && $val['chains']) {
                    $this->buildChain($form->$method($key, $column), $val['chains']);
                } else {
                    $form->$method($key, $column);
                }
            }
        }
    }
    /**
     * 建立方法的连锁
     *
     * @param    [type]                   $form   [description]
     * @param    [type]                   $chains [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T09:29:07+0800
     */
    public function buildChain($form, $chains)
    {
        if (count($chains) == 0) {
            return $form;
        }
        $chain = array_splice($chains, 0, 1);

        foreach ($chain as $chainName => $closure) {
            if (is_int($chainName)) {
                return $this->buildChain($form->$closure(), $chains);
            } else {
                return $this->buildChain($form->$chainName($closure), $chains);
            }
        }
        return $form;
    }

    /**
     * 确认是否已经有传入Grid指定的 filter 设定的变数
     *
     * @param    Encore\Admin\Grid                   $grid [description]
     * @param    array                   $input   request()->all
     *
     * @return   boolean                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T09:28:04+0800
     */
    public function checkIsPassFilter($grid, $input)
    {
        $filters = $grid->getFilter()->filters();
        $isPassFilter = false;

        foreach ($filters as $key => $filter) {
            if ($isPassFilter) {
                break;
            }
            try {
                if (is_array($filter->getId())) {
                    $ids = $filter->getId();
                    foreach ($ids as $key => $subFilterKey) {
                        if ($isPassFilter) {
                            break;
                        }
                        $subFilterKeyParts = explode('_', $subFilterKey);

                        if (isset($input[current($subFilterKeyParts)])) {
                            $isPassFilter = true;
                        }
                    }
                } else {
                    if (isset($input[$filter->getId()])) {
                        $isPassFilter = true;
                    }
                }

            } catch (\Exception $ex) {

            }

        }
        return $isPassFilter;
    }
}
