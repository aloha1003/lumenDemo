<?php

namespace App\Repositories;

use App\Repositories\Criteria\RequestCriteria;
use App\Traits\CacheableRefractor;
use Illuminate\Container\Container as Application;
//use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;

abstract class ExtendBaseRepository extends BaseRepository implements CacheableInterface
{

    use CacheableRepository, CacheableRefractor {
        CacheableRefractor::getCacheKey insteadof CacheableRepository;
    }

    protected $orderByColumn = '';
    protected $sortedDirection = '';
    protected $search = '';
    protected $searchFields = '';

    /**
     * Boot up the repository, pushing criteria
     */
    function boot()
    {
        parent::boot();
        $this->pushCriteria(app(RequestCriteria::class));
        if ($_FILES) {
            config(['repository.cache.enabled' => false]);
        }
    }
    function __construct(Application $app)
    {
        parent::__construct($app);
    }
    function setOrderBy($orderColumn)
    {
        $this->orderByColumn = $orderColumn;
        return $this;
    }

    function setSortedBy($direction = "asc")
    {
        $this->sortedDirection = $direction;
        return $this;
    }

    function getOrderBy()
    {
        if (!$this->orderByColumn) {
            return request()->get(config('repository.criteria.params.orderBy', 'orderBy'), null);
        } else {
            return $this->orderByColumn;
        }
    }

    function getSortedBy()
    {
        if (!$this->orderByColumn) {
            return request()->get(config('repository.criteria.params.sortedBy', 'sortedBy'), 'asc');
        } else {
            return $this->sortedDirection;
        }
    }

    function injectSearch($search)
    {
        $searchs = [];
        $searchFields = [];
        //示例
        //search=name:John;email:john@gmail.com&searchFields=name:like;email:=
        foreach ($search as $key => $value) {
            if (is_array($value)) {
                $searchs[] = $key . ':' . end($value);
                $searchFields[] = $key;
                // searchFields=name:like
                // $searchs[] = $key . ':' . $value;
            } else {
                $searchs[] = $key . ':' . $value;
                $searchFields[] = $key;
            }

        }
        $seachParam = config('repository.criteria.params.search', 'search');
        $searchFieldsParam = config('repository.criteria.params.searchFields', 'searchFields');
        $outputString = $seachParam . '=' . implode(';', $searchs);
        $outputString .= '&' . $searchFieldsParam . '=' . implode(';', $searchFields);
        $urlParts = parse_url(request()->url());
        if (isset($urlParts['query'])) {
            parse_str($str, $output);
            unset($output[$seachParam], $output[$searchFieldsParam]);
            $queryString = http_build_query($output) . '&' . $outputString;
        } else {
            $queryString = '?' . $outputString;
        }
        if (strpos($queryString, '?') == 0) {
            $queryString = substr($queryString, 1);
        }
        parse_str($queryString, $output);

        $this->search = $output[$seachParam] ?? '';
        $this->searchFields = $output[$searchFieldsParam] ?? '';
        return $this;
    }

    /**
     * 插入搜寻，Key 为 数字
     *
     * @param    [type]                   $search [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-16T16:14:05+0800
     */
    function injectSearchAutoIndex($search)
    {
        $searchs = [];
        $searchFields = [];
        //示例
        //search=name:John;email:john@gmail.com&searchFields=name:like;email:=
        if (!$search) {
            return $this;
        }
        foreach ($search as $key => $value) {
            if (count($value) == 3) {
                $searchs[] = $value[0] . ':' . $value[2];
                $searchFields[] = $value[0] . ':' . urlencode($value[1]);
            }
            if (count($value) == 2) {
                $searchs[] = $value[0] . ':' . $value[1];
                $searchFields[] = $value[0] . ':' . $value[1];
            }
        }
        // dd($searchs, $searchFields);
        $seachParam = config('repository.criteria.params.search', 'search');
        $searchFieldsParam = config('repository.criteria.params.searchFields', 'searchFields');
        $outputString = $seachParam . '=' . implode(';', $searchs);
        $outputString .= '&' . $searchFieldsParam . '=' . implode(';', $searchFields);
        $urlParts = parse_url(request()->url());
        if (isset($urlParts['query'])) {
            parse_str($str, $output);
            unset($output[$seachParam], $output[$searchFieldsParam]);
            $queryString = http_build_query($output) . '&' . $outputString;
        } else {
            $queryString = '?' . $outputString;
        }
        if (strpos($queryString, '?') == 0) {
            $queryString = substr($queryString, 1);
        }
        parse_str($queryString, $output);

        $this->search = $output[$seachParam] ?? '';
        $this->searchFields = $output[$searchFieldsParam] ?? '';

        return $this;
    }

    function getSearch()
    {
        if (!$this->search) {
            return request()->get(config('repository.criteria.params.search', 'search'), null);
        } else {

            return $this->search;
        }
    }

    function getSearchFileds()
    {
        if (!$this->searchFields) {
            return request()->get(config('repository.criteria.params.searchFields', 'searchFields'), null);
        } else {
            return $this->searchFields;
        }
    }
    /**
     * 取得插入网址的请求的资料
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-16T18:05:20+0800
     */
    function getInjectUrlData()
    {
        $queryParts = [];
        $queryParts[] = config('repository.criteria.params.search', 'search') . '=' . $this->getSearch();
        $queryParts[] = config('repository.criteria.params.searchFields', 'searchFields') . '=' . $this->getSearchFileds();
        $queryParts[] = config('repository.criteria.params.sortedBy', 'sortedBy') . '=' . $this->getSortedBy();
        $queryParts[] = config('repository.criteria.params.orderBy', 'orderBy') . '=' . $this->getOrderBy();
        parse_str(implode('&', $queryParts), $output);
        return $output;
    }

    /**
     * 重构 findWhere，加上 in 、 not_in
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    function findWhere(array $where, $columns = ['*'])
    {
        if ($this instanceof CacheableInterface) {
            //如果有实作 CacheableInterface
            if (!$this->allowedCache('findWhere') || $this->isSkippedCache()) {
                return $this->doFindWhere($where, $columns);
            }

            $key = $this->getCacheKey('findWhere', func_get_args());
            $minutes = $this->getCacheMinutes();
            $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($where, $columns) {
                return $this->doFindWhere($where, $columns);
            });

            $this->resetModel();
            $this->resetScope();
            return $value;
        } else {
            return $this->doFindWhere($where, $columns);
        }
    }

    /**
     * 真正做 findWhere 的地方
     *
     * @param    array                    $where   [description]
     * @param    array                    $columns [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T14:28:39+0800
     */
    function doFindWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                switch ($condition) {
                    case 'between':
                        $this->model = $this->model->whereBetween($field, $val);
                        break;
                    case 'not_between':
                        $this->model = $this->model->whereNotBetween($field, $val);
                        break;
                    case 'in':
                        $this->model = $this->model->whereIn($field, $val);
                        break;
                    case 'not_in':
                        $this->model = $this->model->whereNotIn($field, $val);
                        break;
                    default:
                        $this->model = $this->model->where($field, $condition, $val);
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
