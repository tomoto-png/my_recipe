<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/assets/css/login.css">
	<title>ログイン</title>
</head>

<body>
	<div class="login-form">
		<h1 class="mb24 text-center">ログイン</h1>

		<?php if ($msg = Session::get_flash('error')): ?>
			<p class="error">
				<?php echo e($msg); ?>
			</p>
		<?php endif; ?>

		<?php echo Form::open(['action' => 'auth/login']); ?>
		<?php echo Form::csrf(); ?>

		<p class="form-row flex items-center">
			<?php echo Form::label('メールアドレス', 'email'); ?>
			<?php echo Form::input('email', Input::post('email')); ?>
		</p>

		<?php if (!empty($errors['email'])): ?>
			<div class="error mb16"><?= e($errors['email']); ?></div>
		<?php endif; ?>

		<p class="form-row flex items-center">
			<?php echo Form::label('パスワード', 'password'); ?>
			<?php echo Form::password('password'); ?>
		</p>

		<?php if (!empty($errors['password'])): ?>
			<div class="error mb16"><?= e($errors['password']); ?></div>
		<?php endif; ?>

		<?php echo Form::submit('submit', 'ログイン', ['class' => 'btn btn-primary w100 mt24']); ?>
		<?php echo Form::close(); ?>
	</div>
</body>

</html>