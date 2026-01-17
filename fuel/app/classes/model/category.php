<?php

class Model_Category
{
    public static function find_all()
    {
        return \DB::select('id', 'name')
            ->from('categories')
            ->order_by('id', 'asc')
            ->execute()
            ->as_array('id', 'name'); //配列に変換[id => name, ...]
    }
}
