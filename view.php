<?php
include_once('include/get_cv.php');

$query = intval($_GET["cv_id"]);
$cv = get_cv($query);

if ($query < 1 || !$cv) {
    header("Location: index.php");
    exit();
}

$title = htmlspecialchars($cv["name"]) . "'s CV";
ob_start();
?>
<div class="view-page">
    <div class="view-container shadow">
        <h2><?= $title ?></h2>
        <div class="cv-email">
            <b>Email:</b>
            <a href="mailto:<?= htmlspecialchars($cv["email"]) ?>"><?= htmlspecialchars($cv["email"]) ?></a>
        </div>
        <div class="info">
            <div>
                <b>Professional Summary:</b>
                <p class="cv-summary">
                    <?= $cv["professional_summary"] ? htmlspecialchars($cv["professional_summary"]) : 'Not specified' ?>
                </p>
            </div>
            <div>
                <b>Programming Languages:</b>
                <ul class="cv-list">
                    <?php
                    if (count($cv['languages']) > 0) {
                        foreach ($cv['languages'] as $key => $language) {
                            echo '
                    <li style="order: ' . ($language["is_key_language"] ? 0 : $key + 1) . '">' . htmlspecialchars($language["language_name"]) . ($language["is_key_language"] ? ' <b>(Key Language)</b>' : '') . '</li>
                    ';
                        }
                    } else {
                        echo '<li>Not specified</li>';
                    }
                    ?>
                </ul>
            </div>
            <div>
                <b>Education:</b>
                <ul class="cv-list">
                    <?php
                    if (count($cv['education']) > 0) {
                        foreach ($cv['education'] as $key => $education) {
                            echo '<li>' . htmlspecialchars($education["institution_name"]) . '</li>';
                        }
                    } else {
                        echo '<li>Not specified</li>';
                    }
                    ?>
                </ul>
            </div>
            <div>
                <b>Links:</b>
                <ul class="cv-list">
                    <?php
                    if (count($cv['education']) > 0) {
                        foreach ($cv['links'] as $key => $link) {
                            echo '<li><a href="' . htmlspecialchars($link["url"]) . '" target="_blank">' . htmlspecialchars($link["url"]) . '</a></li>';
                        }
                    } else {
                        echo '<li>Not specified</li>';

                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'include/layout.php';
?>