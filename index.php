<?php
include_once('include/connect_db.php');

$query = empty($_GET["query"]) ? null : trim($_GET["query"]);

$title = $query ? 'Searching "' . htmlspecialchars($query) .'"' : 'Search';
ob_start();
?>
<div class="search-container">
    <div class="search-topbar">
        <h1>
            <?php
            if ($query) {
                echo "Search Results for \"" . htmlspecialchars($query) . "\"";
            } else {
                echo "Viewing all CVs";
            }
            ?>
        </h1>
        <a href="index.php">Reset Search</a>
    </div>
    <div class="search-list">
        <?php
        if ($query) {
            $stmt = $conn->prepare("SELECT users.name, users.email, cv_programmingentry.language_name, userscv.cv_id FROM users INNER JOIN userscv ON userscv.user_id = users.user_id LEFT JOIN cv_programmingentry ON cv_programmingentry.cv_id = userscv.cv_id AND cv_programmingentry.is_key_language = 1 WHERE users.name LIKE ? OR cv_programmingentry.language_name LIKE ?");
            $stmt->execute(["%$query%", "%$query%"]);
        } else {
            $stmt = $conn->prepare("SELECT users.name, users.email, cv_programmingentry.language_name, userscv.cv_id FROM users INNER JOIN userscv ON userscv.user_id = users.user_id LEFT JOIN cv_programmingentry ON cv_programmingentry.cv_id = userscv.cv_id AND cv_programmingentry.is_key_language = 1");
            $stmt->execute();
        }
        $cvs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cvs as $key => $cv) {
            echo '
            <a class="cv-container shadow" href="view.php?cv_id=' . htmlspecialchars($cv["cv_id"]) . '">
                <h3 class="cv-name">' . htmlspecialchars($cv["name"]) . '</h3>
                <p class="cv-language">Key Language: ' . ($cv["language_name"] ? htmlspecialchars($cv["language_name"]) : 'Not specified') . '</p>
                <span class="cv-email">Email: ' . htmlspecialchars($cv["email"]) . '</span>
            </a>
            ';
        }
        ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'include/layout.php';
?>