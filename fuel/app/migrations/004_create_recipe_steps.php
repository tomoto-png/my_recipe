<?php

namespace Fuel\Migrations;

class Create_Recipe_Steps
{
    public function up()
    {
        \DBUtil::create_table('recipe_steps', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'recipe_id' => array('type' => 'int', 'constraint' => 11),
            'step_number' => array('type' => 'int', 'constraint' => 11),
            'description' => array('type' => 'text'),
            'created_at' => array('type' => 'datetime'),
            'updated_at' => array('type' => 'datetime'),
        ), array('id'), false, 'InnoDB');

        \DBUtil::add_foreign_key(
            'recipe_steps',
            [
                'constraint' => 'fk_recipe_steps_recipe',
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
        \DBUtil::drop_table('recipe_steps');
    }
}
