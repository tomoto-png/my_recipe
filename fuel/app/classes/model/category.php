<?php

class Model_Category
{
    public static function get_list()
    {
        return \DB::select('id', 'name')
            ->from('categories')
            ->order_by('id', 'asc')
            ->execute()
            ->as_array('id', 'name'); //配列に変換[id => name, ...]
    }
}
