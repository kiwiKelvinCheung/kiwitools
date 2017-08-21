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
                        'actions' => ['logout', 'index','get-post','update'],
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

    public function actionIndex(){
        $max_page = 10;
        $start_page = 0;
        $page = 2;
        $date = date('Y-m-d');
        
        $url = 'http://www.coco01.net/user/dailypost';
        $post = new CreatePost();
                return $this->render('post_loading', [
                    'process' => 0,
                ]);
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
                    ['name' => '__RequestVerificationToken', 'value' => 'P805Md_7D9CMa8mCJQo9sFcyZCO_BBf-qJ7zqJ1tp9PSlO4cQUD2b5INOjCsNvycnhDYRmyIDqBTAWibRD0mZrG5xsleJ8Hd4zeWq58pCcM1'],
                    ['name' => 'COCO01Identity', 'value' => 'zinoUsvmHo39O1f49PjOL9dxDpdINiIcplvwgt0UJPWU3C7Cp3l2WxiEo-JJmaJbLt4ojlaqbf9uN1oFYXBrH3t9O3V2h9gMTNG0f1cai5rvubw0Z9pYvZp5Nid-Smsbcsv7p94Q0fvzFTAbPMpf1so2yHtFiMh9Muh6b3xHMfHr1muAOQF_UJ_-Tun87oTtchvcLXUdQim5ETSaLL1xp-5sGyb1qqsTJq-o9JacmBp571feKidiTe1W2Ou3aPjoGb5Bm1EYSIETKJHb187jtWMaywRmCXqAWScsTNSS5844NHgDpQX_P5i0IHQ9vliYh5yl-tAzqhshp_2I2NHblkC-rpDEejuJS6dXHd1FoLxcqEbG4IaD9ZPTmF-0RyEklyqe1U1bN3TVZdT_xSUKpjLp8hqpkfmNaNNyOs178uxneuDsGrfEIriCqt0-DRe7zTEBYsTUCvmry8cMz5Aret0liPEHrWbrxiekOUG3WA4oTgDerGgKm1nYHqw0wano'],
                    ['name' => '__cfduid', 'value' => 'db575400fd2004f9ac2e9f40f311f923d1502166370'],
                    ])
                  ->setMethod('post')
                  ->setUrl($request_url)
                  ->setHeaders(array(
                    'postman-token' => '0236e922-20fb-5b78-17bc-33687a9001d5',
                    'cache-control' => 'no-cache',
                    'Content-Length' => 0,
                    ''
                  ))->send();
    
              try {
                  
                  $html = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($response->content);
    
                  foreach($html->find('.post-list .list-item') as $element){
                      //$post_div = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($element);
                      
                        foreach($element->find('.title') as $post_title){
                            $post->title = $post_title->outertext;
                        }
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
                        //$post->createpost();
                  }
                return $this->render('post_loading', [
                    'process' => 10,
                ]);
                $start_page++;
                $page++;
          } catch (HttpException $ex) {
            echo $ex;
          }
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
        $model = $this->findModel($id);
        $model->used_by  = Yii::$app->user->identity->id;
        $model->save();
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['get-post']);
        }
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
