<link rel="stylesheet" href="/assets/css/shopping.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js"></script>

<div id="shopping-form">

	<div class="shopping__header flex justify-between items-center mb-24">
		<h1 class="shopping__title font-bold">買い物リスト</h1>
		<button class="btn-lg btn-add" data-bind="click: openModal">材料を追加</button>
	</div>

	<div class="shopping__list gap-8">
		<?php if (empty($shopping_items)): ?>
			<p class="shopping__empty">まだ買い物リストがありません。</p>
		<?php else: ?>
			<?php foreach ($shopping_items as $item): ?>
				<div class="shopping-item flex justify-between items-center rounded-4 bg-white">

					<?= Form::open(['action' => 'shopping/update/' . $item['id']]) ?>
					<?= Form::csrf() ?>
					<label class="shopping-item__main flex items-center gap-12">
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

					<div class="shopping-item__actions flex items-center gap-12">
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
	<div class="modal-overlay flex items-center justify-center" data-bind="click: closeModal, visible: isModalOpen">
		<div class="modal rounded-4"
			data-bind="click: function(){}, clickBubble: false">
			<h2 class="modal__title font-bold text-center">材料登録</h2>
			<div class="error text-center mb-16"
				data-bind="visible: errors().length">
				<span data-bind="text: errors()[0]"></span>
			</div>
			<?= Form::open([
				'class' => 'shopping',
				'id' => 'add-item-form',
				'onsubmit' => 'return false;'
			]) ?>
			<?= Form::csrf() ?>

			<div class="modal-field flex items-start gap-8 mb-16 gap-8">
				<?= Form::label('材料名', 'name', ['class' => 'modal-field__label font-semibold']) ?>
				<div class="modal-field__control flex-col gap-6">
					<?= Form::input('name', '', [
						'class' => 'modal-field__input w-full rounded-4 border',
						'data-bind' => 'value: name, valueUpdate: "input"'
					]) ?>
					<div class="error" data-bind="visible: fieldErrors.name().length">
						<span data-bind="text: fieldErrors.name()[0]"></span>
					</div>
				</div>
			</div>

			<div class="modal-field  flex items-start gap-8">
				<?= Form::label('分量', 'quantity', ['class' => 'modal-field__label font-semibold']) ?>
				<div class="modal-field__control flex-col gap-6">
					<?= Form::input('quantity', '', [
						'class' => 'modal-field__input w-full rounded-4 border',
						'data-bind' => 'value: quantity, valueUpdate: "input"'
					]) ?>
					<div class="error" data-bind="visible: fieldErrors.quantity().length">
						<span data-bind="text: fieldErrors.quantity()[0]"></span>
					</div>
				</div>
			</div>

			<div class="modal__actions flex justify-end gap-12 mt-32">
				<button type="button"
					class="btn-lg btn-cancel"
					data-bind="click: closeModal">
					キャンセル
				</button>

				<button type="button"
					class="btn-lg btn-add"
					data-bind="click: submit">
					材料登録
				</button>
			</div>
			<?= Form::close() ?>
		</div>
	</div>
</div>
<script src="/assets/js/shopping_form.js"></script>