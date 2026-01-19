<?php

class Model_Shopping_List_Item
{
    protected static $_table_name = 'shopping_list_items';
    public static function find_by_id_and_user($id, $user_id)
    {
        $table = static::$_table_name;
        return DB::select('id')
            ->from($table)
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute()
            ->current();
    }
    public static function find_by_user($user_id)
    {
        $table = static::$_table_name;
        return DB::select(
            "{$table}.id",
            "{$table}.is_checked",
            "recipe_ingredients.recipe_id",
            "recipe_ingredients.name",
            "recipe_ingredients.quantity"
        )
            ->from($table)
            ->join('recipe_ingredients', 'INNER')
            ->on("{$table}.recipe_ingredient_id", '=', 'recipe_ingredients.id')
            ->where("{$table}.user_id", $user_id)
            ->order_by("{$table}.created_at", 'asc')
            ->execute()
            ->as_array();
    }

    public static function add_by_ingredients($ingredient_ids, $user_id, $now)
    {
        $table = static::$_table_name;

        $query = DB::insert($table)
            ->columns(['user_id', 'recipe_ingredient_id', 'created_at', 'updated_at']);

        foreach ($ingredient_ids as $ingredient_id) {
            $query->values([
                $user_id,
                $ingredient_id,
                $now,
                $now,
            ]);
        }

        $query->execute();
    }

    public static function create($data, $user_id, $now)
    {
        $table = static::$_table_name;
        $ingredient = Model_Recipe_Ingredient::create($data);

        if (!$ingredient || !isset($ingredient[0])) {
            throw new \RuntimeException('Ingredient insert failed');
        }

        return DB::insert($table)
            ->columns(['user_id', 'recipe_ingredient_id', 'created_at', 'updated_at'])
            ->values([
                $user_id,
                $ingredient[0],
                $now,
                $now
            ])
            ->execute();
    }

    public static function update_checked($id, $user_id, $checked)
    {
        $table = static::$_table_name;
        $item = self::find_by_id_and_user($id, $user_id);
        if (! $item) {
            throw new \HttpNotFoundException();
        }

        return DB::update($table)
            ->set(['is_checked' => $checked])
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute();
    }

    public static function delete_by_id_and_user($id, $user_id)
    {
        $table = static::$_table_name;

        DB::start_transaction();

        try {
            $item = DB::select(
                "{$table}.id",
                "{$table}.recipe_ingredient_id",
                "recipe_ingredients.recipe_id"
            )
                ->from($table)
                ->join('recipe_ingredients', 'INNER')
                ->on("{$table}.recipe_ingredient_id", '=', 'recipe_ingredients.id')
                ->where("{$table}.id", $id)
                ->where("{$table}.user_id", $user_id)
                ->execute()
                ->current();

            if (! $item) {
                throw new \HttpNotFoundException();
            }

            DB::delete($table)
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->execute();

            if ($item['recipe_id'] === null) {
                Model_Recipe_Ingredient::delete(
                    $item['recipe_ingredient_id']
                );
            }

            DB::commit_transaction();
        } catch (\Exception $e) {
            DB::rollback_transaction();
            throw $e;
        }
    }
}
