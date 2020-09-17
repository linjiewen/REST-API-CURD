<?php

namespace yiiComponent\restApi\restAction;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Class RestDelete
 *
 * @package app\components\actions
 */
class RestDelete extends YiiRestAction
{
    /**
     * @param $id
     * @return void
     * @throws ServerErrorHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
