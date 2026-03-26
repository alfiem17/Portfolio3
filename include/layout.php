<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) . " - Aston CV" ?></title>
    <link rel="stylesheet" href="assets/styles/global.css" />
    <link rel="stylesheet" href="assets/styles/header.css" />
    <link rel="stylesheet" href="assets/styles/account.css" />
    <link rel="stylesheet" href="assets/styles/edit.css" />
    <link rel="stylesheet" href="assets/styles/search.css" />
    <link rel="stylesheet" href="assets/styles/view.css" />
</head>
<body>
    <?php include_once('header.php') ?>
    <div class="main-container">
        <?= $content ?>
    </div>
</body>
</html>