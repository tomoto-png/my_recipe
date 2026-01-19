<link rel="stylesheet" href="/assets/css/recipe.css">

<?php if ($message = \Session::get_flash('message')): ?>
    <div id="flash-message" class="flash-message">
        <?php echo e($message); ?>
    </div>
<?php endif; ?>

<div class="recipe-detail">
    <div class="recipe-detail__hero">
        <img src="/<?= e($recipe['image_path']) ?>" alt="<?= e($recipe['title']) ?>" class="recipe-detail__image">
        <div class="recipe-detail__info">
            <h1 class="recipe-detail__heading"><?= e($recipe['title']) ?></h1>
            <p class="recipe-detail__category"><?= e($recipe['name']) ?></p>
            <p class="recipe-detail__date"><?= date('Y/m/d', strtotime($recipe['created_at'])) ?></p>
            <div class="recipe-detail__actions">
                <div class="recipe-detail__actions-top">
                    <a href="/recipe/edit/<?= $recipe['id'] ?>" class="btn btn-edit">編集</a>
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
            <div class="recipe-detail__ingredient">
                <span class="recipe-detail__ingredient-name"><?= $ingredient['name'] ?></span>
                <span class="nowrap recipe-detail__ingredient-quantity"><?= $ingredient['quantity'] ?></span>
            </div>
        <?php endforeach ?>
    </section>
    <section class="recipe-detail__steps">
        <h2 class="section-title mb16">作り方</h2>
        <?php foreach ($steps as $step): ?>
            <div class="recipe-detail__step">
                <span class="recipe-detail__step-number"><?= $step['step_number'] ?></span>
                <p class="recipe-detail__step-description"><?= $step['description'] ?></p>
            </div>
        <?php endforeach ?>
    </section>
</div>
<script src="/assets/js/common.js"></script>