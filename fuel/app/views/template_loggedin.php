<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/utilities.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="/assets/css/layout.css">

    <title><?= $title ?? 'My Recipe' ?></title>
</head>

<body>

    <?= $header ?>

    <main>
        <?php if ($message = \Session::get_flash('message')): ?>
            <div id="flash-message" class="flash-message">
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error = \Session::get_flash('error')): ?>
            <div id="flash-message" class="flash-message error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="main-inner">
            <?= $content ?>
        </div>
    </main>

    <script src="/assets/js/common.js"></script>
</body>

</html>