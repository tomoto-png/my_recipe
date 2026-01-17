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

        // 外部キー制約
        \DB::query("
            ALTER TABLE recipe_ingredients
            ADD CONSTRAINT fk_recipe_ingredients_recipe
            FOREIGN KEY (recipe_id)
            REFERENCES recipes(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        ")->execute();
    }

    public function down()
    {
        \DB::query("
            ALTER TABLE recipe_ingredients
            DROP FOREIGN KEY fk_recipe_ingredients_recipe
        ")->execute();

        \DBUtil::drop_table('recipe_ingredients');
    }
}
