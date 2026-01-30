<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/assets/css/auth.css">
	<link rel="stylesheet" href="/assets/css/base.css">
	<link rel="stylesheet" href="/assets/css/utilities.css">
	<title>ログイン</title>
</head>

<body class="flex items-center justify-center bg-white">

	<?php if ($error = \Session::get_flash('error')): ?>
		<div id="flash-message" class="flash-message error">
			<?= e($error) ?>
		</div>
	<?php endif; ?>
	<div class="auth-form w-full p-32">
		<h1 class="mb-24 text-center">ログイン</h1>

		<?= Form::open(['action' => 'auth/login']); ?>
		<?= Form::csrf(); ?>

		<div class="auth-form__group flex-col mb-16">
			<div class="auth-form__row flex items-center">
				<?= Form::label('メールアドレス', 'email', ['class' => 'auth-form__label']); ?>
				<?= Form::input('email', Input::post('email'), ['class' => 'auth-form__input p-6']); ?>
			</div>

			<?php if (!empty($errors['email'])): ?>
				<p class="auth-form__error error">
					<?= e($errors['email']); ?>
				</p>
			<?php endif; ?>
		</div>

		<div class="auth-form__group flex-col">
			<div class="auth-form__row flex items-center">
				<?= Form::label('パスワード', 'password', ['class' => 'auth-form__label']); ?>
				<?= Form::password('password', null, ['class' => 'auth-form__input p-6']); ?>
			</div>

			<?php if (!empty($errors['password'])): ?>
				<p class="auth-form__error error">
					<?= e($errors['password']); ?>
				</p>
			<?php endif; ?>
		</div>


		<?= Form::submit('submit', 'ログイン', ['class' => 'btn btn-add w-full mt-24']); ?>
		<?= Form::close(); ?>
		<p class="mt-16 text-center">
			<a href="<?= Uri::create('auth/register'); ?>">アカウントをお持ちでない方はこちら</a>
		</p>
	</div>
	<script src="/assets/js/common.js"></script>
</body>

</html>