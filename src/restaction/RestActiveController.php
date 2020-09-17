<?php

namespace yiiComponent\restApi\restAction;

/**
 * Class RestActiveController
 *
 * @package app\base
 */
class RestActiveController extends \yii\rest\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yiiComponent\restApi\restAction\RestIndex',
                'modelClass' => $this->modelClass,
                'sort' => ['id' => SORT_DESC],
                // 可以传参的条件查询
                'where' =>[['is_trash' => 0]],
            ],
            'view' => [
                'class' => 'yiiComponent\restApi\restAction\RestView',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'delete' => [
                'class' => 'yiiComponent\restApi\restAction\RestSoftDelete',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'update' => [
                'class' => 'yiiComponent\restApi\restAction\RestUpdate',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'create' => [
                'class' => 'yiiComponent\restApi\restAction\RestCreate',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ]);
    }
}
