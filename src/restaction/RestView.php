<?php

namespace yiiComponent\restApi\restAction;

use yii\web\NotFoundHttpException;

/**
 * Class RestView
 *
 * @package app\components\actions
 */
class RestView extends YiiRestAction
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecordInterface
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        return $model;
    }
}
