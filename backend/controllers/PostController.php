<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Post;
use backend\models\CreatePost;
use backend\models\PostSearch;


use linslin\yii2\curl;
use yii\httpclient\Client;
use yii\httpclient\FormatterInterface;
use yii\httpclient\ParserInterface;
use yii\httpclient\Response;
use Eddmash\Clipboard\Clipboard;
/**
 * Site controller
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','get-post','update','site-redirect','update-post-list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */

    public function actionUpdatePostList(){
        $max_page = 10;
        $start_page = 0;
        $page = 2;
        $date = date('Y-m-d');
        
        $url = 'http://www.coco01.net/user/dailypost';
        while($start_page<10){
            if($start_page!=0){
                $link_params = '?'.'page='.$page.'&date='.$date.'&catId=0';
                $request_url = $url.$link_params;
            }else{
                $request_url = $url;
            }
            $client = new Client([
                'formatters' => [
                    Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // override default XML formatter
                ],
            ]);
            $session = Yii::$app->session;
            $session['language'] = 'en-US';
              $response = $client->createRequest()
                    ->setCookies([
                    ['name' => '__RequestVerificationToken', 'value' => 'mBb9LevLoleLlJS919ABseKcPELgKO6oMUcSAZTpGiIUi7u94ot2b2Ln9-8oip8gW5FJUHR4qnY8tArpBrP0mn5Gc0ZluvncTxHHq0rcOuQ1'],
                    ['name' => 'COCO01Identity', 'value' => 'FQOGoDTpuun9EzxtTeAWM56CakQFE1bnu0QWqohZl3kYex0ya8ws8e54yMFwtAGytNd-5VrzCW0Qx6quyG1FmnU4AKaYhQNtTd3lZuL5ngdCrPR-U_RxaSTgOpihkRUg0tUWzERCw4ZDoKq057OEUSkj-sclEplO5aMfHkBQZR7odpIuWgV3dkMZLsl02WcH86l_aePiYoCfSX6zrLNH29ppwBU6O_UiSNNneCkdJUGoO5AKHrS3UXXTVDfTeqG9HNbb-WbAdblOBnsqZy1mWnVgg7bXL_EVirq7gesOZZxFPJxc7GDhMBtCR7d1ZoD3Wasr4EQwGs6UnLbLVh9C4Jh6vrCvRy5SFaoudD-lIo6Z51BXFPbuEoufq-RWen8zZpZuZdbthIZk9D8K1kKTlcMw_iUipznvFgb6pRPx-mr0KORaHJH9k41GUYKI9jbqJyqSUQaTxOmjs5TUUIoEJzAcGkJYYxBx70USpCkfCBwXe7GBCtbjiLRyJ3oI5BC-'],
                    ['name' => '__cfduid', 'value' => 'db575400fd2004f9ac2e9f40f311f923d1502166370'],
                    ])
                  ->setMethod('post')
                  ->setUrl($request_url)
                  ->setHeaders(array(
                    'postman-token' => 'e9b5ea37-d14c-e4ab-c854-dd99c62bc272',
                    'cache-control' => 'no-cache',
                    'Content-Length' => 0,
                  ))->send();
    
              try {
                  $html = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($response->content);
    
                  foreach($html->find('.post-list .list-item') as $element){
                      //$post_div = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($element);
                        $post = new CreatePost();
                        foreach($element->find('.title') as $post_title){
                            $disable_pjax_attr ="data-pjax='0'";
                            $find_post_title = $post_title->outertext;
                            $substr = 'target="_blank"';
                            $unable_pjax_title = str_replace($substr,$substr.' '.$disable_pjax_attr,$find_post_title);
                            $exists = Post::find()->where(['title'=>$unable_pjax_title])->exists(); 
                            $post->title = $unable_pjax_title;
                        }
                        if($exists){
                            $post = Post::find()->where( ['title'=>$unable_pjax_title])->one();
                        }
                        //if(findBytitle($post->title))
                        foreach($element->find('a.username') as $post_author){
                            $author = preg_replace("/<img[^>]+\>/i", "", $post_author->outertext);
                            $post->author = $author;
                        }                    
                        foreach($element->find('.l-date') as $post_date){
                            $post->post_date = $post_date->plaintext;
                        }                      
                        foreach($element->find('.list-img img') as $post_img){
                            $post->img_path = $post_img->outertext;
                        }                    
                        foreach($element->find('.l-views') as $post_view){
                            preg_match_all('!\d+!', $post_view, $p_view);
                            $post->today_view = $p_view[0][0];
                        }                    
                        foreach($element->find('.l-catId') as $post_cate){
                            $post->category = $post_cate->outertext;
                        }
                        if($exists){
                            $post->save();
                        }else{
                            $post->createpost();
                        }
                  }
                $start_page++;
                $page++;
          } catch (HttpException $ex) {
            echo $ex;
          }
        } 
        if (!Yii::$app->request->isAjax) {
            //return $this->redirect(['post/get-post']);
        }
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionGetPost()
    {
        $searchModel = new PostSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
            return $this->render('post_view', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
    }
    public function actionUpdate($id)
    {
        $eily_author_link = '?r=Eilly';
        $model = $this->findModel($id);
        $model->used_by  = Yii::$app->user->identity->id;
        $model->save();
        $html = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($model->title);
        foreach($html->find('a') as $element){
            $post_link = explode('?r=',$element->href)[0]; 
            
        }
        if (!Yii::$app->request->isAjax) {
            echo Yii::getAlias('@coco01').$post_link.$eily_author_link;
        }else{
            echo Yii::getAlias('@coco01').$post_link.$eily_author_link;
        }
    }
    public function actionSiteRedirect(){
        $request = Yii::$app->request;
        //echo Yii::getAlias('@coco01').$request->url;
        $this->redirect(Yii::getAlias('@coco01').$request->url);
        
    }
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
