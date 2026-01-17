<?php

class Model_Recipe_Ingredient
{
    protected static $_table_name = 'recipe_ingredients';
    public static function find_by_recipe_ids(array $recipe_ids)
    {
        $table = static::$_table_name;

        if (empty($recipe_ids)) {
            return [];
        }

        return DB::select('recipe_id', 'name')
            ->from($table)
            ->where('recipe_id', 'IN', $recipe_ids)
            ->order_by('recipe_id')
            ->order_by('id')
            ->execute()
            ->as_array();
    }

    public static function find_by_recipe_id($recipe_id)
    {
        $table = static::$_table_name;

        return DB::select('name', 'quantity')
            ->from($table)
            ->where('recipe_id', $recipe_id)
            ->order_by('created_at', 'desc')
            ->execute()
            ->as_array();
    }

    public static function create($recipe_id, $ingredients, $now)
    {
        $table = static::$_table_name;
        $query = \DB::insert($table)
            ->columns(['recipe_id', 'name', 'quantity', 'created_at', 'updated_at']);

        foreach ($ingredients as $ingredient) {
            $query->values([
                $recipe_id,
                $ingredient['name'],
                $ingredient['quantity'],
                $now,
                $now,
            ]);
        }

        $query->execute();
    }
}
