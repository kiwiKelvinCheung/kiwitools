<?php
namespace backend\models;

use common\models\Post;
//use common\models\MerchantAccount;

/**
 * Signup form
 */
class CreatePost extends Post
{
    
    public $title;
    public $today_view;
    public $img_path;
    public $author;
    public $post_date;
    public $post_date_timestamp;
    public $category;
    public $used_by;
    public $txt_title;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //['title', 'required'],
            ['title', 'unique', 'targetClass' => '\common\models\Post', 'message' => 'This Post has already in Database.'],
            //['title', 'string', 'min' => 2, 'max' => 255],
            [['today_view','author','category','used_by','img_path','post_date','post_date_timestamp','txt_title'],'safe']
        ];
    }
    public function createpost()
    {

        if (!$this->validate()) {
            return null;
        }
        
        $post = new Post();
        $post->title = $this->title;
        $post->today_view = $this->today_view;
        $post->author = $this->author;
        $post->post_date = $this->post_date;
        $post->category = $this->category;
        $post->used_by = $this->used_by;
        $post->post_date_timestamp = strtotime($this->post_date);
        $post->img_path = $this->img_path;
        $post->txt_title = $this->txt_title;

        $post->save();
    }
}
