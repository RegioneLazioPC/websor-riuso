<?php
namespace common\models\rbac;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\Model;
use yii\data\ActiveDataProvider;


use common\models\user\UserAllowedIps;

class AuthItem extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'unique'],
            [['description'], 'string'],
        ];
    }

    

    public function fields()
    {
        return [
            'name',
            'description',
            'type',
            'administrative'
        ];
    }

    public function extraFields()
    {
        $expand = [
            'permissions',
            'roles'
        ];
        return $expand;
    }

    
    public function getPermissions()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])
        ->viaTable('auth_item_child', ['parent' => 'name']);
    }

    public function getRoles()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])
        ->viaTable('auth_item_child', ['child' => 'name']);
    }

    public function search($params, $type = null)
    {
        $query = AuthItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['name' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere(['ilike', 'name', $this->name]);
        $query->andFilterWhere(['ilike', 'description', $this->description]);

        if ($type) {
            $query->andWhere(['type'=>$type]);
        }

        return $dataProvider;
    }
}
