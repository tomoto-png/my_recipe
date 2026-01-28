<?php

namespace Fuel\Migrations;

class Create_Recipe_Ingredients
{
    public function up()
    {
        \DBUtil::create_table('recipe_ingredients', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'recipe_id' => array('type' => 'int', 'constraint' => 11, 'null' => true),
            'name' => array('type' => 'varchar', 'constraint' => 100),
            'quantity' => array('type' => 'varchar', 'constraint' => 50, 'null' => true),
            'created_at' => array('type' => 'datetime'),
            'updated_at' => array('type' => 'datetime'),
        ), array('id'), false, 'InnoDB');

        \DBUtil::add_foreign_key(
            'recipe_ingredients',
            [
                'constraint' => 'fk_recipe_ingredients_recipe',
                'key'        => 'recipe_id',
                'reference'  => [
                    'table'  => 'recipes',
                    'column' => 'id',
                ],
                'on_delete'  => 'CASCADE',
                'on_update'  => 'CASCADE',
            ]
        );
    }

    public function down()
    {
        \DBUtil::drop_table('recipe_ingredients');
    }
}
