<link rel="stylesheet" href="/assets/css/recipe.css">

<div class="recipe-detail">
    <div class="recipe-detail__hero flex gap-24 mb-24">
        <img src="/<?= e($recipe['image_path']) ?>" alt="<?= e($recipe['title']) ?>" class="recipe-detail__image">
        <div class="recipe-detail__info flex-col gap-8">
            <h1 class="recipe-detail__heading font-bold"><?= e($recipe['title']) ?></h1>
            <p class="recipe-detail__category rounded-8"><?= e($recipe['name']) ?></p>
            <p class="recipe-detail__date"><?= date('Y/m/d', strtotime($recipe['created_at'])) ?></p>
            <div class="recipe-detail__actions flex-col gap-12">
                <div class="recipe-detail__actions-top flex gap-12">
                    <a href="/recipe/edit/<?= (int)$recipe['id'] ?>" class="btn btn-edit">編集</a>
                    <?= Form::open([
                        'action' => 'recipe/delete/' . $recipe['id'],
                        'onsubmit' => "return confirm('本当に削除しますか？')"
                    ]) ?>
                    <?= Form::csrf(); ?>
                    <?= Form::submit('delete', '削除', ['class' => 'btn btn-remove']) ?>
                    <?= Form::close() ?>
                </div>

                <div class="recipe-detail__actions-bottom">
                    <?= Form::open(['action' => 'shopping/add']) ?>
                    <?= Form::csrf() ?>
                    <?= Form::hidden('recipe_id', $recipe['id']) ?>
                    <?= Form::submit('add', '買い物リストに入れる', ['class' => 'btn btn-add']) ?>
                    <?= Form::close() ?>
                </div>
            </div>
        </div>
    </div>
    <section class="recipe-detail__ingredients">
        <h2 class="section-title mb16">材料</h2>
        <?php foreach ($ingredients as $ingredient): ?>
            <div class="recipe-detail__ingredient flex justify-between items-center">
                <span class="recipe-detail__ingredient-name"><?= e($ingredient['name']) ?></span>
                <span class="nowrap recipe-detail__ingredient-quantity"><?= e($ingredient['quantity']) ?></span>
            </div>
        <?php endforeach ?>
    </section>
    <section class="recipe-detail__steps">
        <h2 class="section-title mb16">作り方</h2>
        <?php foreach ($steps as $step): ?>
            <div class="recipe-detail__step flex gap-12">
                <span class="recipe-detail__step-number flex items-center justify-center font-bold"><?= e($step['step_number']) ?></span>
                <p class="recipe-detail__step-description"><?= e($step['description']) ?></p>
            </div>
        <?php endforeach ?>
    </section>
</div>