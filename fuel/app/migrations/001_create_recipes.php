<?php

namespace Fuel\Migrations;

class Create_Recipes
{
    public function up()
    {
        \DBUtil::create_table('recipes', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'user_id' => array('type' => 'int', 'constraint' => 11),
            'title' => array('type' => 'varchar', 'constraint' => 100),
            'category_id' => array('type' => 'int', 'constraint' => 11),
            'image_path' => array('type' => 'varchar', 'constraint' => 255),
            'created_at' => array('type' => 'datetime'),
            'updated_at' => array('type' => 'datetime'),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('recipes');
    }
}
