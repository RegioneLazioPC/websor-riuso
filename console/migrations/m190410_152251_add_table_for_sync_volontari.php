<?php

use yii\db\Migration;
use yii\db\Schema;

use common\models\TblRuoloVolontario;
use common\models\VolVolontario;
/**
 * Class m190410_152251_add_table_for_sync_volontari
 */
class m190410_152251_add_table_for_sync_volontari extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $volontari = VolVolontario::find()->asArray()->all();
        $this->addColumn('utl_contatto', 'use_type', $this->integer(1)->defaultValue(0));

        $this->createTable('tbl_ruolo_volontario', [
            'id' => $this->primaryKey(),
            'id_sync' => $this->string(),
            'descrizione' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        Yii::$app->db->createCommand("
            UPDATE vol_volontario SET ruolo = null WHERE 1=1
            ")->execute();

        Yii::$app->db->createCommand("
            ALTER TABLE vol_volontario
            ALTER COLUMN ruolo SET DATA TYPE varchar(255)
            ")->execute();

        foreach ($volontari as $volontario) {
            $r = strtolower($volontario['ruolo']);
            $role = TblRuoloVolontario::find()->where(['descrizione'=>$r])->one();
            if(!$role) {
                $c = new TblRuoloVolontario;
                $c->descrizione = $r;
                $c->save();
            }

            $v = VolVolontario::find()->where(['id'=>$volontario['id']])->one();
            $v->ruolo = $r;
            $v->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropColumn('utl_contatto', 'use_type');
        $this->dropTable('tbl_ruolo_volontario');

    }

}
