<?php 
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

//var_dump(Yii::$app->request);
echo \Yii::$app->request->userIP;
//echo \Yii::$app->request->userHostAddress;
                
$client = new Client();
$session = Yii::$app->session;
$session['language'] = 'en-US';
$response = $client->createRequest()
    ->setMethod('get')
    ->setHeaders([''])
    ->setUrl('http://test.qooza.hk/ypa_ui/index.html')
    ->send();
try {
    $html = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($response->content);
    echo $html;
}catch(HttpException $e){
    echo $e;
}
?>
