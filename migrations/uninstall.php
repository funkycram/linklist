<?php

class uninstall extends \humhub\components\Migration
{

    public function up()
    {

        $this->dropTable('{{%linklist_category}}');
        $this->dropTable('{{%linklist_link}}');
        $this->dropTable('{{%linklist_stream}}');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
