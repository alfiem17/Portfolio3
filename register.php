<?php
include_once('include/auth.php');
include_once('include/connect_db.php');

if ($user) {
    header("Location: index.php");
    exit();
}

$name = null;
$email = null;
$password = null;
$confirm_password = null;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if(!validateCSRFToken($_POST['csrf_token'])) {
        http_response_code(401);
        exit("Invalid CSRF token");
    }
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $password_pattern = '/^[^\s].{5,}[^\s]$/'; // length >= 7 and no space at start or end so no need to trim password.
            if (preg_match($password_pattern, $password)) {
                if ($password === $confirm_password) {
                    try {
                        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        $emailFound = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$emailFound) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
                            $success = $stmt->execute([$name, $email, $hashed_password]);
                            $user_id = $conn->lastInsertId();

                            $stmt = $conn->prepare("INSERT INTO userscv (user_id, professional_summary) VALUES (?, ?)");
                            $stmt->execute([$user_id, ""]);

                            if ($success) {
                                header("Location: login.php");
                                exit();
                            } else {
                                $message = "Account failed to be created.";
                            }
                        } else {
                            $message = "The email entered has already been registered";
                        }
                    } catch (PDOException $ex) {
                        $message = "An unexpected error has occured.";
                    }
                } else {
                    $message = "Passwords don't match";
                }
            } else {
                $message = "Password must be at least 7 characters long and cannot start or end with a space.";
            }
        } else {
            $message = "Please enter a valid email";
        }
    } else {
        $message = "Please fill in all fields";
    }
}
?>

<?php
$title = "Register";
ob_start();
?>

<div class="account-page">
    <form action="register.php" method="post" class="account-container shadow">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <h2>Register</h2>
        <span>Please enter your credentials</span>
        <input type="text" name="name" class="input account-input" required placeholder="Name"
            value="<?= htmlspecialchars($name) ?>">
        <input type="email" name="email" class="input account-input" required placeholder="Email"
            value="<?= htmlspecialchars($email) ?>">
        <input type="password" name="password" class="input account-input" required placeholder="Password">
        <input type="password" name="confirm_password" class="input account-input" required
            placeholder="Confirm Password">
        <input type="submit" class="input account-submit" value="Register">
        <span>Already have an account? <a href="login.php"><b>Login here</b></a></span>
        <span style="color:rgb(167, 0, 0)" class="account-message"><?= htmlspecialchars($message) ?></span>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'include/layout.php';
?>