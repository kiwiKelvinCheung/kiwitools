<?php
namespace app\assets;

use yii\web\AssetBundle;


class AdminLTEAsset extends AssetBundle {

    public $sourcePath = '@app/themes/adminlte/';
    public $css = [
        'dist/css/AdminLTE.css',
        'font-awesome-4.5.0/css/font-awesome.min.css',
        'ionicons/2.0.1/css/ionicons.min.css',
        'dist/css/skins/_all-skins.min.css',
        ];
    public $js = [
        'dist/js/app.js',
        'plugins/slimScroll/jquery.slimscroll.min.js',
        'plugins/fastclick/fastclick.min.js',
        'dist/js/demo.js',
        ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}