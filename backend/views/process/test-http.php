<?php 
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("xdebug.var_display_max_depth", -1);
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\models\Post;
use common\models\Category;
use backend\models\CreatePost;
use backend\models\PostSearch;


use JonnyW\PhantomJs\Client;
//use yii\httpclient\Client;
//use yii\httpclient\FormatterInterface;
//use yii\httpclient\ParserInterface;
//use yii\httpclient\Response;
//use Eddmash\Clipboard\Clipboard;
//use yii\base\ErrorException;

//$phantom_script= Yii::$app->request->baseUrl.'/phantomjs/bin/get_webpage.js'; 
//$phantom = Yii::$app->request->baseUrl.'/phantomjs/bin/'; 
//$response =  exec ($phantom+' '+$phantom_script);
//var_dump($response);
//echo  htmlspecialchars($response);
//exit;
//$client = new Client();
//$session = Yii::$app->session;
//$session['language'] = 'en-US';
//$response = $client->createRequest()
//    ->setMethod('get')
//    ->addHeaders(['userAgent'=>'Mozilla\/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) 
//AppleWebKit\/602.4.6 (KHTML, like Gecko) Version\/10.0 Mobile\/14D27 Safari\/602.1'])
//    ->setHeaders([''])
//    ->setUrl('http://test.qooza.hk/ypa_ui/index.html')
//    ->send();
//try {
//    //var_dump($response->content);
//    $html = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($response->content);
//    foreach($html->find('iframe') as $element){
//        
//    }
//}catch(HttpException $e){
//    echo $e;
//}

$client = Client::getInstance();
$client->getEngine()->setPath('/usr/local/bin/phantomjs');
$request = $client->getMessageFactory()->createRequest('http://test.qooza.hk/ypa_ui/index.html', 'GET');
$response = $client->getMessageFactory()->createResponse();
$client->send($request, $response);

if($response->getStatus() === 200) {

    echo $response->getContent();
}
?>
<script>

//    var ad_ysm_src = "/ad_ysm/ad_ysm.php?id=" + ad_ysm_id + "&r=" + ad_ysm_row + "&c=" + ad_ysm_col + "&l=" + ad_ysm_layout + "&s=" + ad_ysm_sublayout + "&uref=" + encodeURIComponent(document.referrer) + "&k=" + ad_ysm_keywords + "&n=" + ad_ysm_recstart;
//    document.write('<iframe src=' + ad_ysm_src + ' width="' + ad_ysm_frame_width + '" height="' + ad_ysm_frame_height + '" scrolling="no" marginheight=0 marginwidth=0 allowTransparency="true" frameBorder=0></iframe>');

</script>
