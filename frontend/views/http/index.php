<?php 
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\models\Post;
use common\models\Category;
use backend\models\CreatePost;
use backend\models\PostSearch;


use linslin\yii2\curl;
use yii\httpclient\Client;
use yii\httpclient\FormatterInterface;
use yii\httpclient\ParserInterface;
use yii\httpclient\Response;
use Eddmash\Clipboard\Clipboard;
use yii\base\ErrorException;
                
$client = new Client();
$session = Yii::$app->session;
$session['language'] = 'en-US';
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('https://test.qooza.hk/ypa_ui/index.html')
    ->setHeaders(array(
        'postman-token' => 'e9b5ea37-d14c-e4ab-c854-dd99c62bc272',
        'cache-control' => 'no-cache',
        'Content-Length' => 0,
    ))->send();
          try {
              $html = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($response->content);
          }
?>
