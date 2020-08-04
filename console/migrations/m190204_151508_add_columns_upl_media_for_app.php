<?php

use yii\db\Migration;

/**
 * Class m190204_151508_add_columns_upl_media_for_app
 */
class m190204_151508_add_columns_upl_media_for_app extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'upl_media', 'md5', $this->string(32) );
        $this->addColumn( 'upl_media', 'exif', $this->text() );
        $this->addColumn( 'upl_media', 'lat', $this->double(11,5) );
        $this->addColumn( 'upl_media', 'lon', $this->double(11,5) );
        $this->addColumn( 'upl_media', 'orientation', $this->integer() );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'upl_media', 'md5');
        $this->dropColumn( 'upl_media', 'exif');
        $this->dropColumn( 'upl_media', 'lat');
        $this->dropColumn( 'upl_media', 'lon');
        $this->dropColumn( 'upl_media', 'orientation');
    }

    
}
