<?php

namespace yiiComponent\restApi\restAction;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Class RestIndex
 *
 * @package app\components\actions
 */
class RestIndex extends \yii\rest\Action
{
    /**
     * @var object $query Model object
     */
    public $query;

    /**
     * @var int $modelsId Models id
     */
    public $modelsId;

    /**
     * @var array $sort The sorting
     */
    public $sort = ['id' => SORT_DESC];

    /**
     * @var int [$pageSize = 20] Number each page
     */
    public $pageSize = 20;

    /**
     * @var int [$page = 1] The current page
     */
    public $page = 1;

    /**
     * @var string $where Query conditions
     */
    public $where;

    /**
     * @var array $fields Query field
     */
    public $fields;

    /**
     * @var string $totalCountHeader Total number of record parameters
     */
    public $totalCountHeader = 'X-Pagination-Total-Count';

    /**
     * @var string $pageCountHeader Total page parameter
     */
    public $pageCountHeader = 'X-Pagination-Page-Count';

    /**
     * @var string The current page parameter
     */
    public $currentPageHeader = 'X-Pagination-Current-Page';

    /**
     * @var string The per page parameter
     */
    public $perPageHeader = 'X-Pagination-Per-Page';

    /**
     * run
     *
     * @return object|ArrayDataProvider
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->prepareDataProvider();
    }


    /* ----private---- */

    /**
     * Prepare data provider
     *
     * @return object|ArrayDataProvider
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    protected function prepareDataProvider()
    {
        $totalCount = $this->totalCount();

        if ($this->fields) {
            $data = new ArrayDataProvider([
                'allModels' => $this->allModels(), 'totalCount' => $totalCount, 'id' => $this->modelsId, 'pagination' => false,
            ]);

            Yii::$app->response->getHeaders()
                ->set($this->totalCountHeader, $totalCount)
                ->set($this->pageCountHeader, ceil($totalCount / $this->pageSize))
                ->set($this->currentPageHeader, $this->page)
                ->set($this->perPageHeader, $this->pageSize);

            return $data;
        } else {
            $requestParams = Yii::$app->getRequest()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = Yii::$app->getRequest()->getQueryParams();
            }

            return Yii::createObject([
                'class' => ActiveDataProvider::className(),
                'query' => $this->query(),
                'pagination' => [
                    'params' => $requestParams,
                ],
                'sort' => [
                    'params' => $requestParams,
                ],
            ]);
        }
    }

    /**
     * Amount of paging data
     *
     * @return array|int|mixed
     * @throws NotFoundHttpException
     */
    protected function pageSize()
    {
        $prePage = Yii::$app->request->get('per-page');
        if ($prePage) {
            $pageSizeMax = Yii::$app->params['pageSizeMax'] ? Yii::$app->params['pageSizeMax'] : 1000;

            if ($prePage > $pageSizeMax) {
                throw new NotFoundHttpException("pageSize cannot be greater than " . $pageSizeMax);
            }

            $this->pageSize = $prePage;
        }

        return $this->pageSize;
    }

    /**
     * The query
     *
     * @return object
     */
    protected function query()
    {
        $whereKeys = ['filterWhere', 'andFilterWhere', 'andWhere'];

        if (!$this->query) {
            $modelClass = $this->modelClass;
            $this->query = $modelClass::find()->where(1);

            if ($this->where) {
                if (is_array($this->where)) {
                    $andWhere = [];
                    foreach ($this->where as $key => $value) {
                        if (is_string($key) && in_array($key, $whereKeys)) {
                            $this->query->$key($value);
                        } else {
                            $andWhere = array_merge($andWhere, $value);
                        }
                    }

                    if ($andWhere) {
                        $this->query->andWhere($andWhere);
                    }
                } else {
                    if (is_string($this->where)) {
                        $this->query->andWhere($this->where);
                    }
                }
            }

            $this->sort = [];
        }

        if ($this->fields) {
            $this->query->select($this->fields);
        }

        return $this->query;
    }

    /**
     * Query data
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function allModels()
    {
        $this->page = Yii::$app->request->get('page');
        $page = $this->page ? $this->page : 1;
        $offset = $this->pageSize() * ($page - 1);

        return $this->query()
            ->orderBy($this->sort)
            ->offset($offset)
            ->limit($this->pageSize)
            ->asArray()
            ->all();
    }

    /**
     * Total number of pages
     *
     * @return mixed
     */
    protected function totalCount()
    {
        return $this->query()->count();
    }
}
