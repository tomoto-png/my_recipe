<?php

namespace Fuel\Migrations;

class Create_Recipe_Ingredients
{
    public function up()
    {
        \DBUtil::create_table('recipe_ingredients', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'recipe_id' => array('type' => 'int', 'constraint' => 11),
            'name' => array('type' => 'varchar', 'constraint' => 100),
            'quantity' => array('type' => 'varchar', 'constraint' => 50, 'null' => true),
            'created_at' => array('type' => 'datetime'),
            'updated_at' => array('type' => 'datetime'),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('recipe_ingredients');
    }
}
