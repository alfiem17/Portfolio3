<?php
include_once('include/connect_db.php');
include_once('include/auth.php');
include_once('include/get_cv.php');

if (!$user) {
    header("Location: index.php");
    exit();
}

$user_id = $user['user_id'];
$user_cv = get_cv($user_id);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        http_response_code(401);
        exit("Invalid CSRF token");
    }
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $professional_summary = $_POST["summary"] ?? "";
    $languages = $_POST["languages"] ?? [];
    $key_language = $_POST["key_language"] ?? null;
    $education = $_POST["education"] ?? [];
    $links = $_POST["links"] ?? [];

    $languages = array_filter($languages, function($val) {
        return !empty(trim($val));
    });

    $education = array_filter($education, function($val) {
        return !empty(trim($val));
    });

    $links = array_filter($links, function($val) {
        return !empty(trim($val));
    });


    if (!empty($name) && !empty($email) && !empty($professional_summary) && !empty($languages)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($key_language !== null && count($languages) > 0) {
            try {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
                $stmt->execute([$name, $email, $user_id]);

                $stmt = $conn->prepare("UPDATE userscv SET professional_summary = ? WHERE user_id = ?");
                $stmt->execute([$professional_summary, $user_id]);

                $stmt = $conn->prepare("SELECT cv_id FROM userscv WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $cv_id = $stmt->fetchColumn();

                $stmt = $conn->prepare("DELETE FROM cv_programmingentry	 WHERE cv_id = ?");
                $stmt->execute([$cv_id]);

                $stmt = $conn->prepare("DELETE FROM cv_linkentry WHERE cv_id = ?");
                $stmt->execute([$cv_id]);

                $stmt = $conn->prepare("DELETE FROM cv_educationentry WHERE cv_id = ?");
                $stmt->execute([$cv_id]);


                foreach ($languages as $index => $language_name) {
                    $isKey = ($index == $key_language) ? 1 : 0;
                    $stmt = $conn->prepare("
            INSERT INTO cv_programmingentry (cv_id, language_name, is_key_language)
            VALUES (?, ?, ?)
        ");
                    $stmt->execute([$cv_id, $language_name, $isKey]);
                }

                foreach ($education as $index => $institution_name) {
                    $stmt = $conn->prepare("
                        INSERT INTO cv_educationentry (cv_id, institution_name)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$cv_id, $institution_name]);
                }

                foreach ($links as $index => $url) {
                    $stmt = $conn->prepare("
                        INSERT INTO cv_linkentry (cv_id, url)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$cv_id, $url]);
                }

                header("Location: edit.php");

            } catch (PDOException $ex) {
                $message = "An unexpected error has occurred: " . $ex->getMessage();
            }
        } else {
            $message = "Please select a key language";
        }
        } else {
            $message = "Please enter a valid email";
        }
    } else {
        $message = "Please fill in all required fields";
    }
}

?>


<?php
$title = "Edit CV";
ob_start();
?>
<div class="edit-page">
    <form action="edit.php" method="post" class="edit-container shadow">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <h2>Edit your CV</h2>
        <div class="section">
            <label for="name" class="section-label">Name (Required)</label>
            <input name="name" class="input section-input" required placeholder="Name"
                value="<?= htmlspecialchars($user_cv["name"]) ?>" />
        </div>
        <div class="section">
            <label for="email" class="section-label">Email (Required)</label>
            <input name="email" class="input section-input" required placeholder="Email"
                value="<?= htmlspecialchars($user_cv["email"]) ?>" />
        </div>
        <div class="section">
            <label for="summary" class="section-label">Professional Summary (Required)</label>
            <textarea name="summary" class="input section-input summary" required
                placeholder="Professional Summary"><?= htmlspecialchars($user_cv["professional_summary"]) ?></textarea>
        </div>
        <div class="section">
            <label for="languages[]" class="section-label">Programming Languages (Required)</label>
            <div class="languages" id="languages">
                <?php
                if (!empty($user_cv["languages"])) {
                    foreach ($user_cv["languages"] as $index => $language) {
                        echo '
                        <div class="language-entry">
                            <input name="languages[]" class="input section-input" placeholder="Language Name" value="' . htmlspecialchars($language["language_name"]) . '" required />
                            <div class="options">
                                <div class="option">
                                    <input type="radio" name="key_language" value="' . $index . '" ' . ($language["is_key_language"] ? "checked" : "") . '>
                                    <label>Is Key Language</label>
                                </div>
                                <div class="option">
                                    <label for="delete-language">Delete</label>
                                    <input type="checkbox" id="delete-language" value="${index}" />
                                </div>
                            </div>
                        </div>
                    ';
                    }
                }
                ?>

            </div>
        </div>
        <input type="button" value="Add Language" id="add-language" class="input new-language">
        <div class="section">
            <label for="education[]" class="section-label">Education</label>
            <div class="education" id="education">
                <?php
                if (!empty($user_cv["education"])) {
                    foreach ($user_cv["education"] as $index => $education) {
                        echo '
                        <div class="education-entry">
                            <input name="education[]" class="input section-input" placeholder="Institution Name" value="' . htmlspecialchars($education["institution_name"]) . '" />
                            <div class="options">
                                <div class="option">
                                    <label for="delete-language">Delete</label>
                                    <input type="checkbox" id="delete-education" value="${index}" />
                                </div>
                            </div>
                        </div>
                    ';
                    }
                }
                ?>

            </div>
        </div>
        <input type="button" value="Add Education" id="add-education" class="input new-education">
        <div class="section">
            <label for="link[]" class="section-label">Links</label>
            <div class="links" id="links">
                <?php
                if (!empty($user_cv["links"])) {
                    foreach ($user_cv["links"] as $index => $link) {
                        echo '
                        <div class="education-entry">
                            <input name="links[]" class="input section-input" placeholder="Link" value="' . htmlspecialchars($link["url"]) . '" />
                            <div class="options">
                                <div class="option">
                                    <label for="delete-link">Delete</label>
                                    <input type="checkbox" id="delete-link" value="${index}" />
                                </div>
                            </div>
                        </div>
                    ';
                    }
                }
                ?>

            </div>
        </div>
        <input type="button" value="Add Link" id="add-link" class="input new-link">
        <input type="submit" class="input edit-submit" value="Finished">
        <span class="edit-message"><?= htmlspecialchars($message) ?></span>
    </form>
</div>
<script src="assets/js/edit.js"></script>
<?php
$content = ob_get_clean();
include 'include/layout.php';
?>