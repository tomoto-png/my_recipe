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
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('shopping_list_items');
	}
}
