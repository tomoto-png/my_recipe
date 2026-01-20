<?php

class Model_Category
{
    // カテゴリ一覧を id => name の配列で取得
    public static function find_all()
    {
        return \DB::select('id', 'name')
            ->from('categories')
            ->order_by('id', 'asc')
            ->execute()
            ->as_array('id', 'name');
    }
}
