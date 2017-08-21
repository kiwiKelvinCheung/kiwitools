<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Post;


/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PostSearch extends Post
{
    /**
     * @inheritdoc
     */

    public $post_date; 
    
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'today_view','author','category','post_date','post_date_timestamp'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find()->where(['used_by'=>NULL]);

        // add conditions that should always apply here
        //var_dump(Merchant::find()->joinWith('coupons')->all());
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'title' => $this->title,
            'today_view' => $this->today_view,
            'author' => $this->author,
            'category' => $this->category,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            
        ]);
        if(!empty($this->post_date) && strpos($this->post_date, '-') !== false) {
			list($start_date, $end_date) = explode(' - ', $this->post_date);
            $query->andFilterWhere(['between', 'post_date_timestamp', strtotime($start_date), strtotime($end_date)]);
        }else{
            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'today_view', $this->today_view])
                ->andFilterWhere(['like', 'author', $this->author])
                ->andFilterWhere(['like', 'category', $this->category]);
        }
        return $dataProvider;
    }
}
