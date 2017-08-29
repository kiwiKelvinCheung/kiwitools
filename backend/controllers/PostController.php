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
        $max_page = $request->get('max_page',50);
        $max_day = $request->get('max_day',18);
        $catId = $request->get('catId',0);
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
                        ['name' => '__RequestVerificationToken', 'value' => 'uYRPuGfUFedmSDT7WORgdUXxgk6ACNJ7shaNqkQOG3wwnhoKDypu6PB6Ko8XsldlT6aCAExQBqOny_2OSYgYWHcOs60jk_ZB4sYmz1GagJw1'],
                        ['name' => 'COCO01Identity', 'value' => 'SOR2R5G1hlYvJPCGFlbBHy4Pu8PXLUhl9YkUDuOa2MA0zyTdahkKg2kMag2OF0Mu95S5qY4Vll4yX96i8PmhhRPW-7hRXTz8jTkmL0cLN0X4c6uZXSG3uIlewU1OnX0dof93HI1qeWSfUQMGpubofEltUtZFfS_NV72vU6Ii_C5STpJhTgdoXw0wSiKDOnKmcYCT4SzYcsIJhzLDOLeAQJpwag9JIWfTmxVC5s0eVGfLN7CdW5wfAXlJeUxTPdhuu0BwsA-UExt4lIpZ4rlo_uxlwPWD8V1BhDRm9DuJY4B9pZNlhP1nLbailqniDBbUWycaeDICyNfCC-v1tmyglC_bzvlGMjBT3c0sdHwU8-eZ9aJl7AV2IiBhcX2tj8JLZbtbAhuOmGg-DQatzOXsuN1bIP78ax12TzMtbGwo0HUNAPU4qz8U003ig-AsQQF9tO0I_AA4_5QOFGtJGnRTK8O9CmNn4uej8UjB2pazBMI4R6Pv3yYclV4kOlq_-v7Y'],
                        ['name' => '__cfduid', 'value' => 'd6c096ab4f7719a83d8c4a335b39ed19c1503979096'],
                        ['name' => '_ga', 'value' => 'GA1.2.27616028.1503887785'],
                        ['name' => '_gid', 'value' => 'GA1.2.892276425.1503887785'],
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
                        ['name' => '__RequestVerificationToken', 'value' => 'uYRPuGfUFedmSDT7WORgdUXxgk6ACNJ7shaNqkQOG3wwnhoKDypu6PB6Ko8XsldlT6aCAExQBqOny_2OSYgYWHcOs60jk_ZB4sYmz1GagJw1'],
                        ['name' => 'COCO01Identity', 'value' => 'SOR2R5G1hlYvJPCGFlbBHy4Pu8PXLUhl9YkUDuOa2MA0zyTdahkKg2kMag2OF0Mu95S5qY4Vll4yX96i8PmhhRPW-7hRXTz8jTkmL0cLN0X4c6uZXSG3uIlewU1OnX0dof93HI1qeWSfUQMGpubofEltUtZFfS_NV72vU6Ii_C5STpJhTgdoXw0wSiKDOnKmcYCT4SzYcsIJhzLDOLeAQJpwag9JIWfTmxVC5s0eVGfLN7CdW5wfAXlJeUxTPdhuu0BwsA-UExt4lIpZ4rlo_uxlwPWD8V1BhDRm9DuJY4B9pZNlhP1nLbailqniDBbUWycaeDICyNfCC-v1tmyglC_bzvlGMjBT3c0sdHwU8-eZ9aJl7AV2IiBhcX2tj8JLZbtbAhuOmGg-DQatzOXsuN1bIP78ax12TzMtbGwo0HUNAPU4qz8U003ig-AsQQF9tO0I_AA4_5QOFGtJGnRTK8O9CmNn4uej8UjB2pazBMI4R6Pv3yYclV4kOlq_-v7Y'],
                        ['name' => '__cfduid', 'value' => 'd6c096ab4f7719a83d8c4a335b39ed19c1503979096'],
                        ['name' => '_ga', 'value' => 'GA1.2.85912979.1503979098'],
                        ['name' => '_gid', 'value' => 'GA1.2.1326677421.1503979098'],
                        ['name' => '_gid', 'value' => 'GA1.2.1326677421.1503979098'],
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
