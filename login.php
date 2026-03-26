<?php
include_once('include/connect_db.php');
include_once('include/auth.php');
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");
$message = null;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        http_response_code(401);
        exit("Invalid CSRF token");
    }
    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user['user_id'];
                header("Location: edit.php");
                exit();
            } else {
                $message = "Incorrect email or password.";
            }
        } catch (PDOException $ex) {
            $message = "An unexpected error has occurred.";
        }
    } else {
        $message = "Please fill in all fields";
    }
}
?>


<?php
$title = "Sign In";
ob_start();
?>
<div class="account-page">
    <form action="login.php" method="post" class="account-container shadow">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <h2>Login</h2>
        <span>Please enter your credentials</span>
        <input type="email" name="email" class="input account-input" required placeholder="Email">
        <input type="password" name="password" class="input account-input" required placeholder="Password">
        <input type="submit" class="input account-submit" value="Login">
        <span>Don't have an account? <a href="register.php"><b>Register here</b></a></span>
        <span style="color:rgb(167, 0, 0)" class="account-message"><?= htmlspecialchars($message) ?></span>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'include/layout.php';
?>