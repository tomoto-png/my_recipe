<?php

class Model_Recipe
{
    //レシピ登録
    public static function insertRecipe($recipe_data)
    {
        $result = \DB::insert('recipes')->set($recipe_data)->execute();
        return $result[0];
    }
}
