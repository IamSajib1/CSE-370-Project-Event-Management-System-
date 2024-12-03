<?php

    session_start();


    unset($_SESSION['admin_logged_in']);


    $_SESSION = array();

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }


    session_destroy();


    header('Location: home.php');
    exit;
?>

<?php

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}


session_destroy();

header('Location: managerlog.php');
exit;
?>