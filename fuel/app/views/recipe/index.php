<link rel="stylesheet" href="/assets/css/recipe.css">
<div>
    <?= Form::open([
        'action' => 'recipe/index',
        'method' => 'get',
        'class'  => 'mb24'
    ]); ?>

    <div class="recipe-search__row">
        <div class="recipe-search__input-wrap">
            <img
                src="/assets/img/search_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg"
                class="recipe-search__icon"
                alt="">

            <?= Form::input('keyword', Input::get('keyword'), [
                'placeholder' => 'キーワードで検索（レシピ名・材料名）',
                'class'       => 'recipe-search__input',
            ]) ?>
        </div>

        <?= Form::select(
            'category_id',
            Input::get('category_id', ''),
            $categories,
            [
                'class'    => 'recipe-search__select',
                'onchange' => 'this.form.submit()',
            ]
        ) ?>

        <?= Form::submit('search', '検索', [
            'class' => 'btn-search'
        ]) ?>
    </div>

    <?= Form::close() ?>


    <div class="recipe-grid">
        <?php if (empty($recipes)): ?>
            <p>まだレシピが登録されていません。</p>
        <?php else: ?>
            <?php foreach ($recipes as $recipe): ?>
                <a href="/recipe/view/<?= e($recipe['id']) ?>" class="recipe-card">
                    <img src="/<?= e($recipe['image_path']) ?>" alt="<?= e($recipe['title']) ?>" class="recipe-card__image">

                    <div class="recipe-card__info">
                        <h3 class="recipe-card__title nowrap"><?= e($recipe['title']) ?></h3>
                        <p class="recipe-card__category">
                            <?= e($recipe['name']) ?>
                        </p>
                        <?php if (!empty($ingredients[$recipe['id']])): ?>
                            <div class="recipe-card__ingredients" title="<?= e(implode('・', $ingredients[$recipe['id']])) ?>">
                                <?= e(implode('・', $ingredients[$recipe['id']])) ?>
                            </div>
                        <?php endif; ?>
                        <p class="recipe-card__date">
                            <?= date('Y/m/d', strtotime($recipe['created_at'])) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>