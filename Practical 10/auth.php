<?php
function requireRole($role) {
    session_start();

    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }

    if (time() - $_SESSION["last_activity"] > 300) {
        $_SESSION = [];
        session_destroy();

        header("Location: login.php?timeout=1");
        exit;
    }

    if ($_SESSION["role"] != $role) {
        http_response_code(403);
        exit("Access Denied: Unauthorized user");
    }

    $_SESSION["last_activity"] = time();
}
?>
