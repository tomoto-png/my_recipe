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
        ), array('id'));

        \DB::query("
            ALTER TABLE recipe_steps
            ADD CONSTRAINT fk_recipe_steps_recipe
            FOREIGN KEY (recipe_id)
            REFERENCES recipes(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        ")->execute();
    }

    public function down()
    {
        \DB::query("
            ALTER TABLE recipe_steps
            DROP FOREIGN KEY fk_recipe_steps_recipe
        ")->execute();

        \DBUtil::drop_table('recipe_steps');
    }
}
