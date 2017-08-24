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
        $max_page = 50;
        $max_day = 18;
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
                        ['name' => '__RequestVerificationToken', 'value' => 'mBb9LevLoleLlJS919ABseKcPELgKO6oMUcSAZTpGiIUi7u94ot2b2Ln9-8oip8gW5FJUHR4qnY8tArpBrP0mn5Gc0ZluvncTxHHq0rcOuQ1'],
                        ['name' => 'COCO01Identity', 'value' => 'HjzD0cKa3nbDl5GEeRO7xnhnBu2cXTITCyyTfq24ODMfrbGLvgq1tutJL9QwHHdhkxG8MFV6S0UY7GZN4clRR6l1w9OupKsg_lExRPylZUw50iZca8HGgpGKIF1F6dgXt0lUR6vcnZS8T5JLATOJXNksxUdLqn9AMHyW3m_pFcHhHxXL2OPHt5h53a5fX7a5xzlrCHJygllOo2Mz81_oSLBYuywUe2c1CMms2IAc-bi5u3HIF1_g-GQv2eufrB6jkMz_46-ev-UrZRculWLVlji8rzKwWRsky5Ge9_TvnO0IN3sqalUhbuMpJcJOYqRrMeEUQlg9Rgenk-w4TLgmSRm3ujKLjmhJg7V39Q5VeP2DKu0pmxqvKpR9_DtAYTYeHEySqFDZELbykKAuibyl6-6lSDep_JRIfVWu2gCeIh2aJubkix4GVJcQzMRt1e3yg_xHc7K0mTeR0aHRD_GSx5XQ0gUHypuKYveFg9iP6Am5SUjq8mqaAEYdVPxANCfN'],
                        ['name' => '__cfduid', 'value' => 'db575400fd2004f9ac2e9f40f311f923d1502166370'],
                        ['name' => '_ga', 'value' => 'GA1.2.412664765.1502166372'],
                        ['name' => '_gid', 'value' => 'GA1.2.930281380.1503543082'],
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
