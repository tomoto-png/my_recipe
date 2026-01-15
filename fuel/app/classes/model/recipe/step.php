<?php

class Model_Recipe_Step
{
    public static function insertSteps($recipe_id, $steps, $now)
    {
        $query = \DB::insert('recipe_steps')
            ->columns(['recipe_id', 'step_number', 'description', 'created_at', 'updated_at']);

        foreach ($steps as $i => $description) {
            $query->values([
                $recipe_id,
                $i + 1,
                $description,
                $now,
                $now,
            ]);
        }

        $query->execute();
    }
}
