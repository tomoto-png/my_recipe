<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <title>新規登録</title>
</head>

<body>
    <div class="auth-form">
        <h1 class="mb-24 text-center">新規登録</h1>

        <?php if ($msg = Session::get_flash('error')): ?>
            <p class="error">
                <?php echo e($msg); ?>
            </p>
        <?php endif; ?>

        <?php echo Form::open(['action' => 'auth/register']); ?>
        <?php echo Form::csrf(); ?>

        <p class="form-row">
            <?php echo Form::label('メールアドレス', 'email'); ?>
            <?php echo Form::input('email', Input::post('email')); ?>
        </p>

        <?php if (!empty($errors['email'])): ?>
            <div class="error mb16"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <p class="form-row">
            <?php echo Form::label('パスワード', 'password'); ?>
            <?php echo Form::password('password'); ?>
        </p>

        <?php if (!empty($errors['password'])): ?>
            <div class="error mb16"><?php echo $errors['password']; ?></div>
        <?php endif; ?>

        <p class="form-row">
            <?php echo Form::label('パスワード(確認)', 'password_confirm'); ?>
            <?php echo Form::password('password_confirm'); ?>
        </p>

        <?php if (!empty($errors['password_confirm'])): ?>
            <div class="error mb16"><?php echo $errors['password_confirm']; ?></div>
        <?php endif; ?>

        <?php echo Form::submit('submit', '登録', ['class' => 'btn btn-primary w100 mt24']); ?>
        <?php echo Form::close(); ?>
        <p class="mt16 text-center">
            <a href="<?php echo Uri::create('auth/login'); ?>">すでにアカウントをお持ちの方はこちら</a>
        </p>
    </div>
</body>

</html>