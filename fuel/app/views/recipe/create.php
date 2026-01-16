<link rel="stylesheet" href="/assets/css/recipe.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js"></script>

<!-- RecipeFormViewModel -->
<div id="recipe-form">
	<div class="form-container">
		<h1 class="text-center">レシピ登録</h1>

		<?php if ($msg = Session::get_flash('error')): ?>
			<div class="error mb16"><?= e($msg); ?></div>
		<?php endif; ?>

		<?php echo Form::open(['action' => 'recipe/create', 'enctype' => 'multipart/form-data']); ?>
		<?php echo Form::csrf(); ?>

		<div class="form-group mb16">
			<?= Form::label('レシピ名', 'title', ['class' => 'font-semibold']); ?>

			<div class="form-field">
				<?= Form::input('title', Input::post('title'), ['class' => 'form-control']); ?>

				<?php if (!empty($errors['title'])): ?>
					<p class="error"><?= e($errors['title']) ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="form-group mb16">
			<?php echo Form::label('カテゴリー', 'category', ['class' => 'font-semibold']); ?>

			<div class="form-field">
				<?php echo Form::select('category', Input::post('category'), $category_options, ['class' => 'form-control']); ?>

				<?php if (!empty($errors['category'])): ?>
					<div class="error"><?= e($errors['category']) ?></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="form-group mb16">
			<?= Form::label('レシピ画像', 'image_path', ['class' => 'font-semibold']); ?>
			<div class="form-field">
				<?php echo Form::file('image_path', [
					'id' => 'image_path',
					'accept' => 'image/*',
					'style' => 'display:none',
					'data-bind' => 'event: { change: onImageChange }'
				]); ?>

				<div class="image-uploader" data-bind="click: triggerFileInput">

					<!-- アイコン表示 -->
					<img
						src="/assets/img/upload_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg"
						alt="画像をアップロード"
						class="upload-icon"
						data-bind="visible: !imagePreview()">

					<!-- プレビュー表示 -->
					<img data-bind="
						visible: imagePreview,
						attr: { src: imagePreview }"
						alt="画像プレビュー" class="image-preview">
				</div>

				<?php if (!empty($errors['image_path'])): ?>
					<div class="error"><?= e($errors['image_path']) ?></div>
				<?php endif; ?>
			</div>
		</div>

		<h3 class="section-title mb16">材料</h3>

		<div class="ingredients-list" data-bind="foreach: ingredients">

			<div class="ingredient-item mb16">
				<div class="ingredient-fields">
					<div class="ingredient-field">
						<?= Form::label('材料名', null, ['class' => 'ingredient-field__label nowrap, font-semibold']); ?>
						<?= Form::input(
							'ingredients[name][]',
							'',
							['data-bind' => 'value: name', 'class' => 'form-control']
						); ?>
					</div>

					<div class="ingredient-field">
						<?= Form::label('分量', null, ['class' => 'ingredient-field__label nowrap, font-semibold']); ?>
						<?= Form::input(
							'ingredients[quantity][]',
							'',
							['data-bind' => 'value: quantity', 'class' => 'form-control']
						); ?>
					</div>
				</div>

				<div class="ingredient-actions">
					<button type="button" class="btn btn-remove" data-bind="click: $parent.removeIngredient">
						削除
					</button>
				</div>
			</div>

		</div>

		<?php if (!empty($errors['ingredients'])): ?>
			<div class="error mb16"><?= e($errors['ingredients']) ?></div>
		<?php endif; ?>

		<button type="button" class="btn btn-add" data-bind="click: addIngredient">
			＋ 材料を追加
		</button>

		<h3 class="section-title mb16">作り方</h3>

		<div class="steps-list" data-bind="foreach: steps">

			<div class="step-item mb16">
				<div class="step-field">
					<?= Form::label(
						'',
						'',
						['data-bind' => 'text: "手順 " + ($index() + 1)', 'class' => 'step-field__label font-semibold']
					); ?>
					<?= Form::textarea(
						'steps[]',
						'',
						['data-bind' => 'value: description', 'class' => 'step-field__textarea']
					); ?>
				</div>

				<div class="step-actions">
					<button type="button" class="btn btn-remove" data-bind="click: $parent.removeStep">
						削除
					</button>
				</div>
			</div>

		</div>

		<?php if (!empty($errors['steps'])): ?>
			<div class="error mb16"><?= e($errors['steps']) ?></div>
		<?php endif; ?>

		<button type="button" class="btn btn-add" data-bind="click: addStep">
			＋ 手順を追加
		</button>
		<div class="recipe-form__actions">
			<button type="button" class="btn-lg btn-cancel" onclick="history.back();">
				キャンセル
			</button>
			<?php echo Form::submit('submit', 'レシピ登録', ['class' => 'btn-lg btn-add']); ?>
		</div>
		<?php echo Form::close(); ?>
	</div>
</div>

<script>
	window.initialIngredients = <?= json_encode($clean_ingredients ?? []) ?>;
	window.initialSteps = <?= json_encode($clean_steps ?? []) ?>;
</script>
<script src="/assets/js/recipe_form.js"></script>