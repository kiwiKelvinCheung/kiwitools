<?php
namespace backend\controllers;

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
/**
 * Site controller
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public $rvt = '4dYP-3s-BafanuK5dlKX_5BdxqWCYdwNYW__gWw0YZNvjyrC6CmULcVZOsNGNu0tyoWpPVyRDCHKpwO37t6eeVgfLc62O-O2WcUniSEPByY1';
    public $coco01id = 'XtcWwbs_FU_pP-OzHBXVOEI1CeyeaO-HvALjGkX_X3-f2xh_jpOaG6eUHZDc_sENRGwkwwlPzgMAvzRRwoNf5OTqQNESj8MCUJgJ55Xe4JlnWIgpAFZdv8xRhYCwrtNIG_l8O7z3QFe2rzSUPsOpyihkxDfdAAE-gjAUnLfi3kaozuy6Lwe-VHmhBWKHAfiJjPQ1JE6aFjG7L_Z3f7hZqPb1kcL59G7pQCEMM7tvaUEyFYCUDUQb8z-zpSO2_YYfyVG17ExPtQ8gyGBnBvpKdkCISKQZYaBcTggXkYrndz12YwJkQRuak_xaQwXQtXx8GVLtIrVbgVUsVAoqzHuYXJM_f06zI3GieLvfsuykOCD3CxJTzXW1yEBUiIcHhxR0qUgktN41GheY35-BmVqiecwYiYihrnKt1i9GaM6ZUX1_h3q5gySOZfLdqkZq0tagw5iR_PkKekv_Z0J3nEmug0gYG1xmvO9g_CimFLwNTls';
    public $cocomyid = 'lZv9iNZx9TcrSznFyUcwKUBY2Z5yN5K_pCkUsq39fUi6y324jsOep4GphdL21GIW1kkqmM4Xz4dPbfVXJzy_uPkeDooAndaeNeh17ONs85AWErGGCDUhNeosJZJnVCBU_3cNGIQJT43Kg8ExXslOveMScBwoQsPz-P1H8q6ddRzCtwGKJYA1nQQMXOqfmYZZ1gv6ainU_gzqo_CpmsARUNIzNqYs4PGaTnWf0HSpzw7QIFzVDOL6CTOJECXBBh_6fycV5ITtL2cZ85Rx_LYZxlRRiTeVHnHNisoXe2hKz-oHBTm6K8UgWhnL29rkVPXdqxAcBToPBZZROQ3D2RPlTJlSI1P1qV4u3Kusnc3Lh3eG4VoJ41ai5rnBLOBfPc78YkNeySNEmpggFPX2WOSoR2twv-R1gsEswjmJZsr5ktcZE4Pz1JmivbzjUYO3la9ndfbzyfgdFrThuWZgQVe9BVh5vdbDldR-VMCRilOVAj0';
    public $_ga = 'GA1.2.94464146.1507522332';
    public $_gid = 'GA1.2.493101457.1507522332';
    public $cfduid = 'd4a8e554fa4d97f2dbf3e031ee046a10a1507271384';
    
    const DATE_FORMAT = 'php:Y-m-d';
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
                        'actions' => ['logout', 'index','get-post','update','site-redirect','update-post-list','custom-post'],
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
        $request = Yii::$app->request;
        $max_page = $request->get('max_page',35);
        $max_day = $request->get('max_day',7);
        $catId = $request->get('catId',0);
        $start_page = 0;
        $start_day = 1;
        $page = 2;
        $date = date('Y-m-d');
        $onedayTimestamp = 60*60*24;
        $timestamp_to_day = Yii::$app->formatter->asTimestamp($date);
        $timestamp_to_pre_day = $timestamp_to_day;
        $url = 'http://www.cocomy.net/user/dailypost';
        while($start_day<$max_day){
            
            if($start_day!=0){
                $timestamp_to_pre_day -= $onedayTimestamp;
                //echo $timestamp_to_pre_day.'<br>';
                $date = date('Y-m-d',$timestamp_to_pre_day);
                //echo $date;
            }
            while($start_page<$max_page){
                if($start_page!=0){
                    $link_params = '?'.'page='.$page.'&date='.$date.'&catId='.$catId;
                    $request_url = $url.$link_params;
                }else{
                    $request_url = $url;
                }
                $client = new Client();
                $session = Yii::$app->session;
                $session['language'] = 'en-US';
                  $response = $client->createRequest()
                        ->setCookies([
                        ['name' => '__RequestVerificationToken', 'value' => $this->rvt],
                        ['name' => 'COCO01Identity', 'value' => $this->coco01id],
                        ['name' => '__cfduid', 'value' => $this->cfduid],
                        ['name' => '_ga', 'value' => $this->_ga],
                        ['name' => '_gid', 'value' => $this->_gid],
                        ['name' => '.AspNet.ApplicationCookie', 'value' => $this->cocomyid],
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
                            
                            foreach($element->find('.title') as $post_title){

                                $disable_pjax_attr ="data-pjax='0'";
                                $find_post_title = $post_title->outertext;
                                $substr = 'target="_blank"';
                                $unable_pjax_title = str_replace($substr,$substr.' '.$disable_pjax_attr,$find_post_title);
                                $real_cate_text = '';
                                foreach($element->find('.color-red',0) as $post_cate){
                                    $cate_text = $element->find('.color-red',1)->plaintext;
                                    $real_cate_text = trim(preg_replace('/\[|\]/', '', $cate_text));
                                    
                                }
                                foreach($post_title->find('strong') as $tag_strong_title){
                                    $exists = Post::find()->where(['txt_title'=>$tag_strong_title])->exists(); 
                                    if($exists){            
                                        $post = Post::find()->where( ['txt_title'=>$tag_strong_title->plaintext,'title'=>$unable_pjax_title])->one();               
                                    }else{
                                        $post = new CreatePost();
                                        $post->title = $unable_pjax_title;
                                        $post->txt_title = $tag_strong_title->plaintext;
                                        $post->category = $real_cate_text;
                                    }
                                }

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

                                $num_of_view = '';
                                for($x = 0 ; $x < sizeof($p_view[0]);$x++){
                                    $num_of_view .= $p_view[0][$x];
                                }
                                $post->today_view = (int)$num_of_view;
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
            $start_day++;
            
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
        $db_category = Category::find()->select(['category_name'])->all();


        $cate_arr = ArrayHelper::map($db_category, 'category_name','category_name');


        //var_dump($cate_arr);
            return $this->render('post_view', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dropdown_category'=>$cate_arr,
            ]);
    }
    public function actionAddCategory(){
//        $data = ArrayHelper::toArray($db_category, [
//            'common\models\Post' => [
//                'category' => function ($post) {
//                    $cate = \serhatozles\simplehtmldom\SimpleHTMLDom::str_get_html($post->category); 
//
//                      foreach($cate->find('a') as $txt_cate){
//
//                        return trim($txt_cate->innertext);
//                      }
//                },
//            ],
//        ]);
//        foreach($cate_arr as $category_name){
//            $model_category = new Category();
//            $model_category->category_name = $category_name;
//            $model_category->save();          
//        }
    }
    public function actionUpdate($id)
    {
        $eily_author_link = '?r=Eilly';
        $model = $this->findModel($id);
        if($model->used_by==null){
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
        }else{
            echo 'error';
        }

    }
    public function actionUiCustomPost(){
        $db_category = Category::find()->select(['category_name'])->all();

        $cate_arr = ArrayHelper::map($db_category, 'category_name','category_name');
            return $this->render('custom_post', [
                'dropdown_category'=>$cate_arr,
            ]);
    }
    public function actionCustomPost(){
        $max_page = 2;
        $max_day = 2;
        $start_page = 0;
        $start_day = 1;
        $page = 2;
        $date = date('Y-m-d');
        $onedayTimestamp = 60*60*24;
        $timestamp_to_day = Yii::$app->formatter->asTimestamp($date);
        $timestamp_to_pre_day = $timestamp_to_day;
        $url = 'http://www.coco01.net/user/dailyPost';
        while($start_day<$max_day){
            
            if($start_day!=0){
                $timestamp_to_pre_day -= $onedayTimestamp;
                //echo $timestamp_to_pre_day.'<br>';
                $date = date('Y-m-d',$timestamp_to_pre_day);
                //echo $date;
            }
            while($start_page<$max_page){
                if($start_page!=0){
                    $link_params = '?'.'page='.$page.'&date='.$date.'&catId=0';
                    $request_url = $url.$link_params;
                }else{
                    $request_url = $url;
                }
                $client = new Client();
                $session = Yii::$app->session;
                $session['language'] = 'en-US';
                  $response = $client->createRequest()
                        ->setCookies([
                        ['name' => '__RequestVerificationToken', 'value' => $this->rvt],
                        ['name' => 'COCO01Identity', 'value' => $this->coco01id],
                        ['name' => '__cfduid', 'value' => $this->cfduid],
                        ['name' => '_ga', 'value' => $this->_ga],
                        ['name' => '_gid', 'value' => $this->_gid],
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
                            
                            foreach($element->find('.title') as $post_title){

                                $disable_pjax_attr ="data-pjax='0'";
                                
                                $find_post_title = $post_title->outertext;

                                $substr = 'target="_blank"';
                                $unable_pjax_title = str_replace($substr,$substr.' '.$disable_pjax_attr,$find_post_title);
                                foreach($post_title->find('strong') as $tag_strong_title){
                                    $exists = Post::find()->where(['txt_title'=>$tag_strong_title])->exists(); 
                                    if($exists){            
                                        $post = Post::find()->where( ['txt_title'=>$tag_strong_title->plaintext,'title'=>$unable_pjax_title])->one();               
                                    }else{
                                        $post = new CreatePost();
                                        $post->title = $unable_pjax_title;
                                        $post->txt_title = $tag_strong_title->plaintext;
                                    }
                                }

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
            $start_day++;
            
        }
        if (!Yii::$app->request->isAjax) {
            //return $this->redirect(['post/get-post']);
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
