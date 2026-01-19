<link rel="stylesheet" href="/assets/css/shopping.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js"></script>
<?php if ($message = \Session::get_flash('message')): ?>
	<div id="flash-message" class="flash-message">
		<?php echo e($message); ?>
	</div>
<?php endif; ?>

<div class="shopping" id="shopping-form">

	<div class="shopping__header">
		<h1 class="shopping__title">買い物リスト</h1>
		<button class="btn-lg btn-add" data-bind="click: openModal">材料を追加</button>
	</div>

	<div class="shopping__list">
		<?php if (empty($shopping_items)): ?>
			<p class="shopping__empty">まだ買い物リストがありません。</p>
		<?php else: ?>
			<?php foreach ($shopping_items as $item): ?>
				<div class="shopping-item">

					<?= Form::open(['action' => 'shopping/update/' . $item['id']]) ?>
					<?= Form::csrf() ?>
					<label class="shopping-item__main">
						<?= Form::checkbox(
							'checked',
							1,
							$item['is_checked'],
							[
								'class' => 'shopping-item__checkbox',
								'onchange' => 'this.form.submit()'
							]
						) ?>

						<span class="shopping-item__name"><?= e($item['name']) ?></span>

						<?php if (!empty($item['quantity'])): ?>
							<span class="shopping-item__quantity">
								（<?= e($item['quantity']) ?>）
							</span>
						<?php endif; ?>
					</label>
					<?= Form::close() ?>

					<div class="shopping-item__actions">
						<?php if (!empty($item['recipe_id'])): ?>
							<a
								href="/recipe/view/<?= (int)$item['recipe_id'] ?>"
								class="shopping-item__recipe-link">
								レシピ
							</a>
						<?php endif; ?>

						<?= Form::open([
							'action' => 'shopping/delete/' . $item['id'],
							'method' => 'post',
							'onsubmit' => "return confirm('本当に削除しますか？')"
						]) ?>
						<?= Form::csrf() ?>
						<?= Form::submit('delete', '削除', [
							'class' => 'btn btn-remove'
						]) ?>
						<?= Form::close() ?>
					</div>

				</div>
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<div class="modal-overlay" data-bind="click: closeModal, visible: isModalOpen">
		<div class="error error--global"
			data-bind="visible: errors().length">
			<span data-bind="text: errors()[0]"></span>
		</div>
		<div class="modal"
			data-bind="click: function(){}, clickBubble: false">
			<h2 class="modal__title text-center">材料登録</h2>
			<div class="shopping" id="shopping-form">
				<input
					type="hidden"
					name="fuel_csrf_token"
					value="<?= Security::fetch_token(); ?>">

				<div class="modal__field">
					<label class="font-semibold">材料名</label>
					<div class="modal__field__control">
						<input type="text"
							class="modal__field__input"
							data-bind="value: name, valueUpdate: 'input'">
						<div class="error" data-bind="visible: fieldErrors.name().length">
							<span data-bind="text: fieldErrors.name()[0]"></span>
						</div>
					</div>
				</div>

				<div class="modal__field">
					<label class="font-semibold">分量</label>
					<input type="text"
						class="modal__field__input"
						data-bind="value: quantity, valueUpdate: 'input'">
				</div>
				<div class="error" data-bind="visible: fieldErrors.quantity().length">
					<span data-bind="text: fieldErrors.quantity()[0]"></span>
				</div>

				<div class="modal__actions">
					<button type="button"
						class="btn-lg btn-cancel"
						data-bind="click: closeModal">
						キャンセル
					</button>

					<button type="button"
						class="btn-lg btn-add"
						data-bind="click: submitForm">
						材料登録
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/assets/js/shopping_form.js"></script>
<script src="/assets/js/common.js"></script>