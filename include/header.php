<?php
include_once('get_user.php');
include_once('auth.php');

$user_id = $_SESSION['user_id'] ?? null;
$user = $user_id ? get_user($user_id) : null;
?>
<header class="header">
    <nav class="nav-menu">
        <h1><a href="index.php">AstonCV</a></h1>
    </nav>
    <nav class="nav-menu">
        <form class="search-form" action="index.php" method="GET">
            <input type="text" name="query" placeholder="Search for a CV..." class="input search-input">
            <button type="submit" class="search-submit">
                <img src="assets/icons/search.svg" alt="Search">
            </button>
        </form>
    </nav>
    <nav class="nav-menu">
        <a href="index.php">View CVs</a>

        <?php if ($user): ?>
            <a href="edit.php">Edit My CV</a>
            <form action="logout.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="submit" class="logout" value="Logout">
            </form>
        <?php else: ?>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>