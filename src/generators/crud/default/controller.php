<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
$oasPathName = $generator->usePluralize ? \yii\helpers\Inflector::pluralize(substr($controllerClass,0,-10)) : substr($controllerClass,0,-10);
$oasPath = strtolower(\yii\helpers\Inflector::camel2id($oasPathName)) ;
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php endif; ?>
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
* Class <?= $controllerClass ?>
*
* @package <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>
*/
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public $modelClass = '<?= ltrim($generator->modelClass, '\\') ?>';

    /**
    * Index
    * @return ActiveDataProvider
    */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $query = $searchModel->search(Yii::$app->request->queryParams);
<?php else: ?>
        $query = <?= $modelClass ?>::find()->with('creator')->with('updater');
<?php endif; ?>

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);
    }

    /**
    * View
    * @param  int $id primaryKey
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionView(<?= $actionParams ?>)
    {
        return $this->findModel(<?= $actionParams ?>);
    }

    /**
    * Create
    * @return object
    * @throws HttpException
    * @throws \yii\base\InvalidConfigException
    */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return $model;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }

    /**
    * Update
    * @param  int $id primaryKey
    * @return object
    * @throws \yii\web\HttpException
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->save()) {
            return $model;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }

    /**
    * Delete
    * @param  int $id 主键
    * @return void
    * @throws \yii\web\HttpException
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionDelete($id)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if (!$model->softDelete()) {
            throw new HttpException(500, json_encode($model->errors));
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?= $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
    <?php
    if (count($pks) === 1) {
        $condition = '$id';
    } else {
        $condition = [];
        foreach ($pks as $pk) {
            $condition[] = "'$pk' => \$$pk";
        }
        $condition = '[' . implode(', ', $condition) . ']';
    }
    ?>

        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(<?= $generator->generateString('The requested '.$modelClass.' does not exist.') ?>);
    }
}
