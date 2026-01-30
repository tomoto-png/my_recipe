<?php

class Model_Shopping_List_Item
{
    protected static $_table_name = 'shopping_list_items';
    //ユーザーの買い物リストを取得
    public static function find_by_user($user_id)
    {
        $table = static::$_table_name;
        return \DB::select(
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

    //指定IDの買い物リストをユーザーIDで検証して取得する
    public static function find_by_id_and_user($id, $user_id)
    {
        $table = static::$_table_name;
        return \DB::select('id')
            ->from($table)
            ->where('id', '=', $id)
            ->where('user_id', '=', $user_id)
            ->execute()
            ->current();
    }

    //ユーザーに紐づく買い物リスト1件を取得
    public static function find_detail_by_id_and_user($id, $user_id)
    {
        $table = static::$_table_name;

        return \DB::select(
            "{$table}.id",
            "{$table}.recipe_ingredient_id",
            "recipe_ingredients.recipe_id"
        )
            ->from($table)
            ->join('recipe_ingredients', 'INNER')
            ->on("{$table}.recipe_ingredient_id", '=', 'recipe_ingredients.id')
            ->where("{$table}.id", '=', $id)
            ->where("{$table}.user_id", '=', $user_id)
            ->execute()
            ->current();
    }

    //レシピ材料を買い物リストに一括追加する
    public static function create_all_by_ingredients($ingredient_ids, $user_id, $now)
    {
        $table = static::$_table_name;

        $query = \DB::insert($table)
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

    //材料を新規作成し、買い物リストに追加する
    public static function create_with_ingredient($data, $user_id, $now)
    {
        $table = static::$_table_name;
        $ingredient_id = Model_Recipe_Ingredient::create($data);

        return \DB::insert($table)
            ->columns(['user_id', 'recipe_ingredient_id', 'created_at', 'updated_at'])
            ->values([
                $user_id,
                $ingredient_id,
                $now,
                $now
            ])
            ->execute();
    }

    //買い物リストのチェック状態を更新する
    public static function update_checked_status($id, $user_id, $checked)
    {
        $table = static::$_table_name;
        $item = self::find_by_id_and_user($id, $user_id);
        if (! $item) {
            throw new \HttpNotFoundException();
        }

        return \DB::update($table)
            ->set(['is_checked' => $checked])
            ->where('id', '=', $id)
            ->where('user_id', '=', $user_id)
            ->execute();
    }

    public static function delete_by_id_and_user($id, $user_id)
    {
        $table = static::$_table_name;

        return \DB::delete($table)
            ->where('id', '=', $id)
            ->where('user_id', '=', $user_id)
            ->execute();
    }
}
