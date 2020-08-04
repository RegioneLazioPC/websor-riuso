<?php

use yii\db\Migration;

/**
 * Class m190125_150828_notification_response_columns
 */
class m190125_150828_notification_response_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_invio', 'status_mail', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_pec', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_sms', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_fax', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_push', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_invio', 'status_mail');
        $this->dropColumn('mas_invio', 'status_pec');
        $this->dropColumn('mas_invio', 'status_sms');
        $this->dropColumn('mas_invio', 'status_fax');
        $this->dropColumn('mas_invio', 'status_push');
    }

}
