<?php

use yii\db\Migration;
use yii\db\Schema;

use common\models\LocComune;
use common\models\UtlAnagrafica;
use common\models\VolSede;
/**
 * Class m181116_090000_sync_MGO_WEBSOR_new_columns
 */
class m181116_090000_sync_MGO_WEBSOR_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->dropForeignKey(
            'fk-vol_volontario_comune_nascita',
            'utl_anagrafica'
        );

        $anagrafiche = UtlAnagrafica::find()->all();
        foreach ($anagrafiche as $ana) {
            if(!empty($ana->luogo_nascita)) {
                $comune = LocComune::findOne($ana->luogo_nascita);
                if($comune) {
                    $ana->luogo_nascita = $comune->comune;
                    $ana->save();
                }
            }
        }


        $this->alterColumn('utl_anagrafica', 'luogo_nascita', $this->string());
        


        $this->addColumn("utl_automezzo","id_sync",$this->string());
        $this->addColumn("vol_volontario","id_sync",$this->string());
        $this->addColumn("utl_attrezzatura","id_sync",$this->string());
        $this->addColumn("utl_automezzo_tipo","id_sync",$this->string());
        $this->addColumn("utl_attrezzatura_tipo","id_sync",$this->string());
        $this->addColumn("vol_tipo_organizzazione","id_sync",$this->string());
        $this->addColumn("vol_organizzazione","stato_iscrizione",$this->integer()->defaultValue(3));
        $this->addColumn("vol_tipo_organizzazione","elenco_territoriale",$this->integer()->defaultValue(0));

        $this->addColumn("utl_anagrafica","id_sync",$this->string());

        $this->addColumn("vol_sede","name",$this->string());
        $sedi = VolSede::find()->all();
        foreach ($sedi as $s) {
            $s->name = $s->tipo;
            $s->save();
        }

        $this->createTable('utl_contatto', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(), // 0 = email, 1 = pec, 2 = telefono, 3 = fax
            'contatto' => $this->string(),
            'note' => $this->text(),
            'id_sync' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);


        $this->createTable('utl_indirizzo', [
            'id' => $this->primaryKey(),
            'indirizzo' => $this->string(),
            'civico' => $this->string(),
            'cap' => $this->string(),
            'id_comune' => $this->integer(),
            'note' => $this->text(),
            'id_sync' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->addForeignKey(
            'fk_indirizzo_comune',
            'utl_indirizzo',
            'id_comune',
            'loc_comune',
            'id',
            'CASCADE'
        );


        $this->createTable('con_anagrafica_indirizzo', [
            'id' => $this->primaryKey(),
            'id_indirizzo' => $this->integer(),
            'id_anagrafica' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_indirizzo_anagrafica_indirizzo',
            'con_anagrafica_indirizzo',
            'id_indirizzo',
            'utl_indirizzo',
            'id',
            'CASCADE' 
        );

        $this->addForeignKey(
            'fk_indirizzo_anagrafica_anagrafica',
            'con_anagrafica_indirizzo',
            'id_anagrafica',
            'utl_anagrafica',
            'id',
            'CASCADE' 
        );


        $this->createTable('con_anagrafica_contatto', [
            'id' => $this->primaryKey(),
            'id_contatto' => $this->integer(),
            'id_anagrafica' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_contatto_anagrafica_contatto',
            'con_anagrafica_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE' 
        );

        $this->addForeignKey(
            'fk_contatto_anagrafica_anagrafica',
            'con_anagrafica_contatto',
            'id_anagrafica',
            'utl_anagrafica',
            'id',
            'CASCADE' 
        );




        

        $this->createTable('con_volontario_specializzazione', [
            'id' => $this->primaryKey(),
            'id_volontario' => $this->integer(),
            'id_specializzazione' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_volontario_specializzazione_volontario',
            'con_volontario_specializzazione',
            'id_volontario',
            'vol_volontario',
            'id',
            'CASCADE' 
        );

        $this->addForeignKey(
            'fk_volontario_specializzazione_specializzazione',
            'con_volontario_specializzazione',
            'id_specializzazione',
            'utl_specializzazione',
            'id',
            'CASCADE' 
        );



        $this->createTable('tbl_sezione_specialistica', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->createTable('con_organizzazione_sezione_specialistica', [
            'id' => $this->primaryKey(),
            'id_organizzazione' => $this->integer(),
            'id_sezione_specialistica' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_org_sezione_specialistica_organizzazione',
            'con_organizzazione_sezione_specialistica',
            'id_organizzazione',
            'vol_organizzazione',
            'id',
            'CASCADE' 
        );

        $this->addForeignKey(
            'fk_org_sezione_specialistica_specializzazione',
            'con_organizzazione_sezione_specialistica',
            'id_sezione_specialistica',
            'tbl_sezione_specialistica',
            'id',
            'CASCADE' 
        );






        $this->createTable('con_volontario_indirizzo', [
            'id' => $this->primaryKey(),
            'id_indirizzo' => $this->integer(),
            'id_volontario' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_indirizzo_volontario_indirizzo',
            'con_volontario_indirizzo',
            'id_indirizzo',
            'utl_indirizzo',
            'id',
            'CASCADE' 
        );

        $this->addForeignKey(
            'fk_indirizzo_volontario_anagrafica',
            'con_volontario_indirizzo',
            'id_volontario',
            'vol_volontario',
            'id',
            'CASCADE' 
        );


        $this->createTable('con_volontario_contatto', [
            'id' => $this->primaryKey(),
            'id_contatto' => $this->integer(),
            'id_volontario' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_contatto_volontario_contatto',
            'con_volontario_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE' 
        );

        $this->addForeignKey(
            'fk_contatto_volontario_anagrafica',
            'con_volontario_contatto',
            'id_volontario',
            'vol_volontario',
            'id',
            'CASCADE' 
        );


        $this->createTable('con_sede_contatto', [
            'id' => $this->primaryKey(),
            'id_sede' => $this->integer(),
            'id_contatto' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_con_sede_contatto_sede',
            'con_sede_contatto',
            'id_sede',
            'vol_sede',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_con_sede_contatto_contatto',
            'con_sede_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );


        $this->createTable('con_organizzazione_contatto', [
            'id' => $this->primaryKey(),
            'id_organizzazione' => $this->integer(),
            'id_contatto' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_con_organizzazione_contatto_organizzazione',
            'con_organizzazione_contatto',
            'id_organizzazione',
            'vol_organizzazione',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_con_organizzazione_contatto_contatto',
            'con_organizzazione_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey(
            'fk_con_organizzazione_contatto_organizzazione',
            'con_organizzazione_contatto'
        );

        $this->dropForeignKey(
            'fk_con_organizzazione_contatto_contatto',
            'con_organizzazione_contatto'
        );
        $this->dropTable('con_organizzazione_contatto');


        $this->dropForeignKey(
            'fk_con_sede_contatto_sede',
            'con_sede_contatto'
        );

        $this->dropForeignKey(
            'fk_con_sede_contatto_contatto',
            'con_sede_contatto'
        );
        $this->dropTable('con_sede_contatto');


        $this->dropForeignKey(
            'fk_contatto_volontario_contatto',
            'con_volontario_contatto'
        );

        $this->dropForeignKey(
            'fk_contatto_volontario_anagrafica',
            'con_volontario_contatto'
        );

        $this->dropForeignKey(
            'fk_indirizzo_volontario_indirizzo',
            'con_volontario_indirizzo'
        );

        $this->dropForeignKey(
            'fk_indirizzo_volontario_anagrafica',
            'con_volontario_indirizzo'
        );


        $this->dropTable('con_volontario_indirizzo');
        $this->dropTable('con_volontario_contatto');



        $this->dropForeignKey(
            'fk_org_sezione_specialistica_organizzazione',
            'con_organizzazione_sezione_specialistica'
        );

        $this->dropForeignKey(
            'fk_org_sezione_specialistica_specializzazione',
            'con_organizzazione_sezione_specialistica'
        );

        $this->dropTable('con_organizzazione_sezione_specialistica');
        $this->dropTable('tbl_sezione_specialistica');

        $this->dropForeignKey(
            'fk_volontario_specializzazione_volontario',
            'con_volontario_specializzazione'
        );

        $this->dropForeignKey(
            'fk_volontario_specializzazione_specializzazione',
            'con_volontario_specializzazione'
        );
        
        $this->dropTable('con_volontario_specializzazione');
        
        $this->dropForeignKey(
            'fk_contatto_anagrafica_contatto',
            'con_anagrafica_contatto'
        );

        $this->dropForeignKey(
            'fk_contatto_anagrafica_anagrafica',
            'con_anagrafica_contatto'
        );

        $this->dropTable('con_anagrafica_contatto');

        $this->dropForeignKey(
            'fk_indirizzo_anagrafica_indirizzo',
            'con_anagrafica_indirizzo'
        );

        $this->dropForeignKey(
            'fk_indirizzo_anagrafica_anagrafica',
            'con_anagrafica_indirizzo'
        );
        
        $this->dropTable('con_anagrafica_indirizzo');

        $this->dropForeignKey(
            'fk_indirizzo_comune',
            'utl_indirizzo'
        );

        $this->dropTable('utl_indirizzo');

        $this->dropTable('utl_contatto');

        $this->dropColumn("vol_tipo_organizzazione","elenco_territoriale");
        $this->dropColumn("vol_organizzazione","stato_iscrizione");
        $this->dropColumn("utl_automezzo","id_sync");
        $this->dropColumn("utl_attrezzatura","id_sync");
        $this->dropColumn("utl_automezzo_tipo","id_sync");
        $this->dropColumn("utl_attrezzatura_tipo","id_sync");
        $this->dropColumn("vol_tipo_organizzazione","id_sync");

        
        $this->dropColumn("vol_sede","name");
        $this->dropColumn("vol_volontario","id_sync");

        $arr_mod = [];
        $anagrafiche = UtlAnagrafica::find()->all();
        foreach ($anagrafiche as $ana) {
            if(!empty($ana->luogo_nascita)) {
                $comune = LocComune::find()->where(['comune'=>$ana->luogo_nascita])->one();
                if($comune) {
                    $arr_mod[] = [
                        'id'=>$ana->id,
                        'luogo_nascita'=>$comune->id
                    ];
                } 
            }
        }

        $this->dropColumn('utl_anagrafica', 'luogo_nascita');
        
        $this->addColumn('utl_anagrafica', 'luogo_nascita', $this->integer());
        
        foreach ($arr_mod as $mod) {
            $ana = UtlAnagrafica::findOne($mod['id']);
            if($ana) {
                $ana->luogo_nascita = $mod['luogo_nascita'];
                $ana->save();
            }
        }

        $this->dropColumn("utl_anagrafica","id_sync");

        $this->addForeignKey(
            'fk-vol_volontario_comune_nascita',
            'utl_anagrafica',
            'luogo_nascita',
            'loc_comune',
            'id'
        );
    }

}
