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
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php endif; ?>

/**
* Class <?= $controllerClass ?>
*
* @package <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>
*/
class <?= $controllerClass ?> extends \yiiComponent\restApi\restAction\RestActiveController
{
    public $modelClass = '<?= ltrim($generator->modelClass, '\\') ?>';

    /**
    * {@inheritdoc}
    */
    public function actions()
    {
        $parentActions = parent::actions();
        return array_merge($parentActions, [
            'index' => array_merge($parentActions['index'], $this->index()),
        ]);
    }


    /* ----private---- */

    /**
    * index
    * @return array
    */
    protected function index()
    {
    <?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $query = $searchModel->search(Yii::$app->request->queryParams);
        $sort = [];
    <?php else: ?>
        $query = '';
    <?php endif; ?>

        return [
            'query' => $query,
            'sort' => $sort,
        ];
    }
}
