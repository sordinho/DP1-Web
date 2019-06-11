<?php

/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 06/06/2019
 */

include ('functions.php');
checkHTTPS();
// Session to kill
session_start();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 3600 * 24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();
myRedirect("login.php");
?>