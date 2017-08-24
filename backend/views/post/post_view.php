<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Post;
use jino5577\daterangepicker\DateRangePicker;

$this->title = 'Post View Of Coco01';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="post-get-loading">
    <div class="spinner">
      <div class="cube1"></div>
      <div class="cube2"></div>
    </div>
</div>
<div class="post-view-index">
    <input type="text" id="clipboard" class="clipboard" style="top:-99px;position:absolute;">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
        $url = '/post/update-post-list';
    echo Html::a('<span class="glyphicon glyphicon-download-alt"></span> 更新列表', '#', 
                            ['title' => Yii::t('yii', 'update-post-list'),
                            'aria-label' => Yii::t('yii', 'update-post-list'),
                             'class'=>'btn btn-twitter action_btn',
                             'onclick' => "
                                if (confirm('確認更新?')) {
                                    $('.post-get-loading').show();
                                    $.ajax('$url', {
                                        type: 'POST',
                                    }).done(function(data) {
                                        $.pjax.reload({container: '#pjax-container'});
                                        $('.post-get-loading').hide();
                                    });
                                }
                                return false;
                            ",
                        ]);?>
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
                'filter'=>Html::activeDropDownList($searchModel,'category',$dropdown_category,['class'=>'form-control','prompt' => '選擇分類']),
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
                                        console.log(data);
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


