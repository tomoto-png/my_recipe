<?php

class Service_Recipe_Form
{
    public static function validate($is_create)
    {
        $errors = [];
        $val = Validation::forge();

        //基本バリデーション
        $val->add('title', 'レシピ名')
            ->add_rule('required');

        $val->add('category', 'カテゴリー')
            ->add_rule('required')
            ->add_rule('numeric_min', 1);

        //メッセージ設定
        $val->set_message('required', ':label は必須です');
        $val->set_message('numeric_min', '正しい:labelを選択してください');

        //画像バリデーション
        if ($is_create || ($_FILES['image_path']['error'] !== UPLOAD_ERR_NO_FILE)) {
            Upload::process([
                'path' => DOCROOT . 'uploads/recipes',
                'randomize' => true,
                'ext_whitelist' => ['jpg', 'jpeg', 'png', 'gif'],
                'max_size' => 2 * 1024 * 1024,
            ]);

            if (! Upload::is_valid()) {
                $errors['image_path'] =
                    '画像は jpg / jpeg / png / gif（2MB以下）でアップロードしてください';
            }
        }

        if (! $val->run()) {
            foreach ($val->error() as $field => $error) {
                $errors[$field] = $error->get_message();
            }
        }

        //材料
        $clean_ingredients = self::extract_ingredients();
        if (empty($clean_ingredients)) {
            $errors['ingredients'] = '材料を1つ以上入力してください';
        }
        //手順
        $clean_steps = self::extract_steps();
        if (empty($clean_steps)) {
            $errors['steps'] = '手順を1つ以上入力してください';
        }

        return [$errors, $clean_ingredients, $clean_steps];
    }

    private static function extract_ingredients()
    {
        $ingredients = Input::post('ingredients');
        $cleaned_ingredients = [];

        // 材料の空行を除去し、有効な材料のみを抽出
        if (is_array($ingredients) && isset($ingredients['name']) && is_array($ingredients['name'])) {
            foreach ($ingredients['name'] as $i => $name) {
                $name = trim($name);
                // 前後の空白を除去してチェック
                if ($name === '') continue;

                $quantity = trim($ingredients['quantity'][$i] ?? '');

                $cleaned_ingredients[] = [
                    'name' => $name,
                    'quantity' => $quantity
                ];
            }
        }

        return $cleaned_ingredients;
    }

    private static function extract_steps()
    {
        $steps = Input::post('steps');
        $cleaned_steps = [];

        // 手順の空行を除去し、有効な手順のみを抽出
        if (is_array($steps)) {
            foreach ($steps as $step) {
                $step = trim($step);
                // 前後の空白を除去してチェック
                if ($step === '') continue;

                $cleaned_steps[] = [
                    'description' => $step,
                ];
            }
        }

        return $cleaned_steps;
    }
}
