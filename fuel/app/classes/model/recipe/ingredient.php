<?php

class Model_Recipe_Ingredient
{
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
