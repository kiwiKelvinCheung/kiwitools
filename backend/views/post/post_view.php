<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use jino5577\daterangepicker\DateRangePicker;

$this->title = 'Post View Of Coco01';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="post-view-index">
    <input type="text" id="clipboard" class="clipboard hide" value="">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?=Html::beginForm(['post/select-delete'],'post');?>
<?php Pjax::begin(['id' => 'pjax-container']); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
             [
                'attribute'=> 'title',
                'format' => 'raw',
            ],
             [
                'attribute'=> 'today_view',
                'format' => 'raw',
            ],
            [
                'attribute'=> 'img_path',
                'format' => 'raw',
                'contentOptions' => ['class' => 'grid-img'],
                'headerOptions' => ['class' => 'grid-img'],

            ],
            [
                'attribute'=> 'author',
                'format' => 'raw',
            ],
            [
                'attribute'=> 'category',
                'format' => 'raw',
            ],
            
            [
                'attribute'=> 'post_date',
                'format' => 'raw',
                'filter' => DateRangePicker::widget([
				    'model' => $searchModel,
				    'attribute' => 'post_date',
				    'pluginOptions' => [
				    'format' => 'd-m-Y',
				    'autoUpdateInput' => false
			     ]
			     ])
            ],
//            ['class' => 'yii\grid\ActionColumn',
//             'template'=>'{update}',
//             'buttons'=>['update' => function ($url, $model, $key) {
//                        return Html::a('<span class="glyphicon glyphicon-bookmark"></span>使用', ['post_use', 'id'=>$model->id],['class'=>'btn btn-foursquare action_btn']);
//                    },
//            ]
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}',
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a('<span class="glyphicon glyphicon-bookmark"></span>使用', '#', 
                            ['title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                             'class'=>'btn btn-foursquare action_btn',
                             'onclick' => "
                                if (confirm('確認使用?')) {
                                    $.ajax('$url', {
                                        type: 'POST',
                                        async:false,
                                    }).done(function(data) {
                                        $.pjax.reload({container: '#pjax-container'});
                                        copytext(data);
                                    });
                                }
                                return false;
                            ",
                        ]);
                    },                    
                ],
            ],

        ],
    ]); 
    
    ?>
    
<?php Pjax::end(); ?></div>
<?= Html::endForm();?> 
<?php $this->registerJsFile('@web/js/copy.js'); ?>


