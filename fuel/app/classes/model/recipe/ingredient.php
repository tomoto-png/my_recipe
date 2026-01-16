<?php

class Model_Recipe_Ingredient
{
    public static function find_by_recipe_ids(array $recipe_ids)
    {
        if (empty($recipe_ids)) {
            return [];
        }

        return DB::select('recipe_id', 'name')
            ->from('recipe_ingredients')
            ->where('recipe_id', 'IN', $recipe_ids)
            ->order_by('recipe_id')
            ->order_by('id')
            ->execute()
            ->as_array();
    }

    public static function insertIngredients($recipe_id, $ingredients, $now)
    {
        $query = \DB::insert('recipe_ingredients')
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
