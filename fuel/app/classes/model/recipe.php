<?php

class Model_Recipe
{
    protected static $_table_name = 'recipes';
    //ユーザーレシピ一覧取得
    public static function find_by_user($user_id, array $params = [])
    {
        $table = static::$_table_name;

        $query = DB::select(
            "{$table}.id",
            "{$table}.title",
            "{$table}.category_id",
            "{$table}.image_path",
            "{$table}.created_at",
            "categories.name"
        )
            ->from($table)
            ->join('categories', 'LEFT')
            ->on("{$table}.category_id", '=', 'categories.id')
            ->where("{$table}.user_id", $user_id);

        if (!empty($params['keyword'])) {
            $keyword = '%' . $params['keyword'] . '%';

            $query->join('recipe_ingredients', 'LEFT')
                ->on("{$table}.id", '=', 'recipe_ingredients.recipe_id')
                ->and_where_open()
                ->where("{$table}.title", 'LIKE', $keyword)
                ->or_where('recipe_ingredients.name', 'LIKE', $keyword)
                ->and_where_close();
        }

        if (!empty($params['category_id'])) {
            $query->where("{$table}.category_id", '=', $params['category_id']);
        }
        $query->group_by("{$table}.id")
            ->order_by("{$table}.created_at", 'desc');

        return $query->execute()->as_array();
    }
    public static function find_by_id($id, $user_id)
    {
        $table = static::$_table_name;
        return DB::select(
            "{$table}.id",
            "{$table}.title",
            "{$table}.category_id",
            "{$table}.image_path",
            "{$table}.created_at",
            "categories.name"
        )
            ->from($table)
            ->join('categories', 'LEFT')
            ->on("{$table}.category_id", '=', 'categories.id')
            ->where("{$table}.id", $id)
            ->where("{$table}.user_id", $user_id)
            ->execute()
            ->current();
    }
    public static function find_or_fail($id, $user_id)
    {
        $recipe = self::find_by_id($id, $user_id);

        if (! $recipe) {
            throw new \HttpNotFoundException();
        }

        return $recipe;
    }
    //レシピ登録
    public static function create($recipe_data)
    {
        $table = static::$_table_name;

        $result = \DB::insert($table)->set($recipe_data)->execute();
        return $result[0];
    }
    //レシピ削除
    public static function delete($id, $user_id)
    {
        $recipe = self::find_or_fail($id, $user_id);
        $table = static::$_table_name;

        try {
            \DB::start_transaction();

            // DB削除
            \DB::delete($table)
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->execute();

            // 画像削除
            if (!empty($recipe['image_path'])) {
                $path = DOCROOT . $recipe['image_path'];
                if (file_exists($path)) {
                    \File::delete($path);
                }
            }

            \DB::commit_transaction();
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            throw $e;
        }
    }
}
