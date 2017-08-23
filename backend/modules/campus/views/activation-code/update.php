<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\campus\models\ActivationCode */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Activation Code',
]) . ' ' . $model->activation_code_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Activation Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->activation_code_id, 'url' => ['view', 'id' => $model->activation_code_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="activation-code-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
