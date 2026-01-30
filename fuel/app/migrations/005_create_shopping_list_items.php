<?php

namespace Fuel\Migrations;

class Create_shopping_list_items
{
	public function up()
	{
		\DBUtil::create_table('shopping_list_items', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('type' => 'int', 'constraint' => 11),
			'recipe_ingredient_id' => array('type' => 'int', 'constraint' => 11),
			'is_checked' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0,),
			'created_at' => array('type' => 'datetime'),
			'updated_at' => array('type' => 'datetime'),
		), array('id'), false, 'InnoDB');

		\DBUtil::add_foreign_key(
			'shopping_list_items',
			[
				'constraint' => 'fk_shopping_list_items_recipe_ingredient',
				'key'        => 'recipe_ingredient_id',
				'reference'  => [
					'table'  => 'recipe_ingredients',
					'column' => 'id',
				],
				'on_delete'  => 'CASCADE',
			]
		);
	}

	public function down()
	{
		\DBUtil::drop_table('shopping_list_items');
	}
}
