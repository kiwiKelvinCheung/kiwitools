<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Progress;

$this->title = 'Post View Of Coco01';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="post-loading-index">

</div>
<?php $this->registerJsFile('@web/js/loading.js'); ?>
<?php $this->registerJsFile('@web/js/copy.js'); ?>

