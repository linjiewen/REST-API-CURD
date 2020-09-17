<?php

namespace yiiComponent\restApi\restAction;

use yii\web\NotFoundHttpException;

/**
 * Class YiiRestAction
 *
 * @package app\components\actions
 */
class YiiRestAction extends \yii\rest\Action
{
    /**
     * @var array $where 条件
     */
    public $where = ['is_trash' => 0];

    /**
     * @param string $id
     * @return \yii\db\ActiveRecordInterface
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $modelClass = $this->modelClass;
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $where = array_merge($this->where, array_combine($keys, $values));
                $model = $modelClass::findOne($where);
            }
        } elseif ($id !== null) {
            $where = array_merge($this->where, [$keys[0] => $id]);
            $model = $modelClass::findOne($where);
        }

        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }
}
