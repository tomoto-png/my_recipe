<?php

namespace Fuel\Migrations;

class Create_Categories
{
    public function up()
    {
        \DBUtil::create_table('categories', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'name' => array('type' => 'varchar', 'constraint' => 100),
            'created_at' => array('type' => 'datetime'),
            'updated_at' => array('type' => 'datetime'),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('categories');
    }
}
