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

        // カテゴリは固定データなので初期投入
        $now = date('Y-m-d H:i:s');

        \DB::insert('categories')
            ->columns(['name', 'created_at', 'updated_at'])
            ->values([
                ['和食', $now, $now],
                ['洋食', $now, $now],
                ['中華', $now, $now],
                ['イタリアン', $now, $now],
                ['デザート', $now, $now],
                ['その他', $now, $now],
            ])
            ->execute();
    }

    public function down()
    {
        \DBUtil::drop_table('categories');
    }
}
