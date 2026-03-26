<?php
include_once('connect_db.php');
function get_cv($user_id)
{
    global $conn;

    $user_cv = null;

    $stmt = $conn->prepare("
    SELECT users.name, users.email, userscv.cv_id, userscv.professional_summary
    FROM users
    JOIN userscv ON users.user_id = userscv.user_id
    WHERE users.user_id = ?
");
    $stmt->execute([$user_id]);

    $base_cv = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($base_cv) {

        $user_cv = [
            "name" => $base_cv["name"],
            "email" => $base_cv["email"],
            "professional_summary" => $base_cv["professional_summary"],
            "languages" => [],
            "education" => [],
            "links" => []
        ];

        $cv_id = $base_cv["cv_id"];

        $stmt = $conn->prepare("
        SELECT language_name, is_key_language 
        FROM cv_programmingentry	 
        WHERE cv_id = ?
    ");
        $stmt->execute([$cv_id]);
        $user_cv["languages"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("
        SELECT institution_name 
        FROM cv_educationentry 
        WHERE cv_id = ?
    ");
        $stmt->execute([$cv_id]);
        $user_cv["education"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("
        SELECT url 
        FROM cv_linkentry 
        WHERE cv_id = ?
    ");
        $stmt->execute([$cv_id]);
        $user_cv["links"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $user_cv;
}
?>