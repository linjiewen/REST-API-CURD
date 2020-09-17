<?php

namespace yiiComponent\restApi\restAction;

use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

/**
 * Class RestSoftDelete
 *
 * @package app\components\actions
 */
class RestSoftDelete extends YiiRestAction
{
    /**
     * @param $id
     * @return ActiveRecord
     * @throws ServerErrorHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->load(['is_trash' => 1], '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        return $model;
    }
}
