<link rel="stylesheet" href="/assets/css/recipe.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js"></script>

<div id="recipe-form">
    <div class="form-container rounded-4 p-32 border">
        <h1 class="text-center">レシピ編集</h1>

        <?= Form::open(['action' => 'recipe/edit/' . $recipe['id'], 'enctype' => 'multipart/form-data']); ?>
        <?= Form::csrf(); ?>

        <div class="recipe-form__group flex items-start gap-12 mb-16">
            <?= Form::label('レシピ名', 'title', ['class' => 'recipe-form__label font-semibold']); ?>

            <div class="recipe-form__field">
                <?= Form::input('title', Input::post('title', $recipe['title']), ['class' => 'recipe-form__control rounded-4 w-full border']); ?>

                <?php if (!empty($errors['title'])): ?>
                    <p class="error"><?= e($errors['title']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="recipe-form__group flex items-start gap-12 mb-16">
            <?= Form::label('カテゴリー', 'category', ['class' => 'recipe-form__label font-semibold']); ?>

            <div class="recipe-form__field">
                <?= Form::select('category', Input::post('category', $recipe['category_id']), $category_options, ['class' => 'recipe-form__control rounded-4 w-full border']); ?>

                <?php if (!empty($errors['category'])): ?>
                    <div class="error"><?= e($errors['category']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="recipe-form__group flex items-start gap-12 mb-16">
            <?= Form::label('レシピ画像', 'image_path', ['class' => 'recipe-form__label font-semibold']); ?>
            <div class="recipe-form__field">
                <?= Form::file('image_path', [
                    'id' => 'image_path',
                    'accept' => 'image/*',
                    'style' => 'display:none',
                    'data-bind' => 'event: { change: onImageChange }'
                ]); ?>

                <div class="image-uploader flex items-center justify-center rounded-8 overflow-hidden mb-16" data-bind="click: triggerFileInput">

                    <!-- アイコン表示 -->
                    <img
                        src="/assets/img/upload_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg"
                        alt="画像をアップロード"
                        class="upload-icon"
                        data-bind="visible: imagePreview() === null">

                    <!-- プレビュー表示 -->
                    <img data-bind="
						visible: imagePreview,
						attr: { src: imagePreview }"
                        alt="画像プレビュー" class="image-preview w-full h-full">
                </div>

                <?php if (!empty($errors['image_path'])): ?>
                    <div class="error"><?= e($errors['image_path']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <h3 class="section-title mb-16">材料</h3>

        <div class="ingredients" data-bind="foreach: ingredients">

            <div class="ingredients__item flex items-end gap-16 mb-16">
                <div class="ingredients__fields flex gap-16">
                    <div class="ingredients__field flex items-center gap-8">
                        <?= Form::label('材料名', null, ['class' => 'ingredients__label nowrap font-semibold']); ?>
                        <?= Form::input(
                            'ingredients[name][]',
                            '',
                            ['data-bind' => 'value: name', 'class' => 'recipe-form__control rounded-4 w-full border']
                        ); ?>
                    </div>

                    <div class="ingredients__field flex items-center gap-8">
                        <?= Form::label('分量', null, ['class' => 'ingredients__label nowrap font-semibold']); ?>
                        <?= Form::input(
                            'ingredients[quantity][]',
                            '',
                            ['data-bind' => 'value: quantity', 'class' => 'recipe-form__control rounded-4 w-full border']
                        ); ?>
                    </div>
                </div>

                <div class="ingredients__actions">
                    <button type="button" class="btn btn-remove" data-bind="click: $parent.removeIngredient">
                        削除
                    </button>
                </div>
            </div>

        </div>

        <?php if (!empty($errors['ingredients'])): ?>
            <div class="error mb-16"><?= e($errors['ingredients']) ?></div>
        <?php endif; ?>

        <button type="button" class="btn btn-add" data-bind="click: addIngredient">
            ＋ 材料を追加
        </button>

        <h3 class="section-title mb-16">作り方</h3>

        <div class="steps" data-bind="foreach: steps">

            <div class="steps__item flex-col gap-12 mb-16">
                <div class="steps__field flex items-start gap-16">
                    <?= Form::label(
                        '',
                        '',
                        ['data-bind' => 'text: "手順 " + ($index() + 1)', 'class' => 'steps__label font-semibold']
                    ); ?>
                    <?= Form::textarea(
                        'steps[]',
                        '',
                        ['data-bind' => 'value: description', 'class' => 'steps__textarea rounded-4 border']
                    ); ?>
                </div>

                <div class="steps__actions flex justify-end">
                    <button type="button" class="btn btn-remove" data-bind="click: $parent.removeStep">
                        削除
                    </button>
                </div>
            </div>

        </div>

        <?php if (!empty($errors['steps'])): ?>
            <div class="error mb-16"><?= e($errors['steps']) ?></div>
        <?php endif; ?>

        <button type="button" class="btn btn-add" data-bind="click: addStep">
            ＋ 手順を追加
        </button>
        <div class="recipe-form__actions flex justify-end gap-12 mt-24">
            <a href="/recipe/index" class="btn-lg btn-cancel">キャンセル</a>
            <?= Form::submit('submit', 'レシピ更新', ['class' => 'btn-lg btn-add']); ?>
        </div>
        <?= Form::close(); ?>
    </div>
</div>

<script>
    window.initialIngredients = <?= json_encode(
                                    $ingredients ?? [],
                                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                                ) ?>;

    window.initialSteps = <?= json_encode(
                                $steps ?? [],
                                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                            ) ?>;

    window.initialImagePath = <?= json_encode(
                                    !empty($recipe['image_path']) ? '/' . ltrim($recipe['image_path'], '/') : null,
                                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                                ) ?>;
</script>
<script src="/assets/js/recipe_form.js"></script>