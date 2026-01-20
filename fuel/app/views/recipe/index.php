<link rel="stylesheet" href="/assets/css/recipe.css">

<div>
    <?= Form::open([
        'action' => 'recipe/index',
        'method' => 'get',
        'class'  => 'mb-24'
    ]); ?>

    <div class="recipe-search__fields flex items-cente gap-16">
        <div class="recipe-search__keyword-wrap">
            <img
                src="/assets/img/search_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg"
                class="recipe-search__icon"
                alt="">

            <?= Form::input('keyword', Input::get('keyword'), [
                'placeholder' => 'キーワードで検索（レシピ名・材料名）',
                'class'       => 'recipe-search__input bg-white rounded-4 border w-full',
            ]) ?>
            <button type="button" class="recipe-search__btn">
                <img
                    src="/assets/img/close_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg"
                    class="recipe-search__icon"
                    alt="">
            </button>
        </div>

        <?= Form::select(
            'category_id',
            Input::get('category_id', ''),
            $categories,
            [
                'class'    => 'recipe-search__select bg-white rounded-4 border',
                'onchange' => 'this.form.submit()',
            ]
        ) ?>

        <?= Form::submit('search', '検索', [
            'class' => 'btn-search'
        ]) ?>
    </div>

    <?= Form::close() ?>


    <div class="recipe-grid gap-24">
        <?php if (empty($recipes)): ?>
            <p class="empty-message">まだレシピが登録されていません。</p>
        <?php else: ?>
            <?php foreach ($recipes as $recipe): ?>
                <a href="/recipe/view/<?= (int)$recipe['id'] ?>" class="recipe-card flex">
                    <img src="/<?= e($recipe['image_path']) ?>" alt="<?= e($recipe['title']) ?>" class="recipe-card__image">

                    <div class="recipe-card__info flex-col h-full gap-8">
                        <h3 class="recipe-card__title nowrap font-bold overflow-hidden"><?= e($recipe['title']) ?></h3>
                        <p class="recipe-card__category rounded-8">
                            <?= e($recipe['name']) ?>
                        </p>
                        <?php if (!empty($ingredients[$recipe['id']])): ?>
                            <div class="recipe-card__ingredients overflow-hidden" title="<?= e(implode('・', $ingredients[$recipe['id']])) ?>">
                                <?= e(implode('・', $ingredients[$recipe['id']])) ?>
                            </div>
                        <?php endif; ?>
                        <p class="recipe-card__date mb-16">
                            <?= date('Y/m/d', strtotime($recipe['created_at'])) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script src="/assets/js/recipe.js"></script>