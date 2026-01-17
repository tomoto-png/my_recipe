<?php

class Model_Recipe_Step
{
    protected static $_table_name = 'recipe_steps';
    public static function find_by_recipe_id($recipe_id)
    {
        $table = static::$_table_name;

        return DB::select('step_number', 'description')
            ->from($table)
            ->where('recipe_id', $recipe_id)
            ->order_by('step_number', 'asc')
            ->execute()
            ->as_array();
    }

    public static function create($recipe_id, $steps, $now)
    {
        $table = static::$_table_name;

        $query = \DB::insert($table)
            ->columns(['recipe_id', 'step_number', 'description', 'created_at', 'updated_at']);

        foreach ($steps as $i => $step) {
            $query->values([
                $recipe_id,
                $i + 1,
                $step['description'],
                $now,
                $now,
            ]);
        }

        $query->execute();
    }

    public static function update($recipe_id, array $steps, string $now)
    {
        $table = static::$_table_name;

        \DB::delete($table)
            ->where('recipe_id', $recipe_id)
            ->execute();

        static::create($recipe_id, $steps, $now);
    }
}
