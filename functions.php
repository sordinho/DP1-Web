<?php
define('ROW_NUMBER', 10);
define('COL_NUMBER', 6);

function printSeatTable($clickable)
{
    $letters = range('A', 'Z');
    if ($clickable == false)
        $buttonAttribute = 'disabled';
    else
        $buttonAttribute = " ";
    for ($i = 1; $i <= ROW_NUMBER; $i++) {
        echo "<tr>";
        for ($j = 1; $j <= COL_NUMBER; $j++) {
            $buttonName = $letters[$i - 1] . "_" . $j;
            echo '<td><button type="button" ' . $buttonAttribute . ' class="freeSeat" name="' . $buttonName . '">' . $buttonName . '</button></td>';
        }
        echo "</tr>";
    }
}

function printSeatInformation()
{
    $totalSeats = ROW_NUMBER * COL_NUMBER;
    $boughtSeats = 0; // todo query
    $bookedSeats = 0; // todo query
    $freeSeats = $totalSeats - $boughtSeats - $bookedSeats;
    echo "<li class='status_total'>Total seats: " . $totalSeats . "</li>";
    echo "<li class='status_free'>Free seats: " . $freeSeats . "</li>";
    echo "<li class='status_bought'>Bought seats: " . $boughtSeats . "</li>";
    echo "<li class='status_booked'>Booked seats: " . $bookedSeats . "</li>";
}

function checkHTTPS()
{
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
        $redirectPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirectPage);
        exit();
    }
}

function checkSession()
{
    session_start();
    if (!isset($_SESSION['s267570_user'])) {
        myRedirect("login.php");
    }
    $t = time();
    $diff = $t - $_SESSION['s267570_time'];
    if ($diff > 120) {
        // Session to kill
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 3600 * 24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        myRedirect("login.php");
    } else {
        $_SESSION['s267570_time'] = time();
    }

}

function myRedirect($page)
{
    $redirectPage = 'https://' . $_SERVER['HTTP_HOST'] . '/DP1-Web/' . $page;
    header('Location: ' . $redirectPage);
    exit();
}

function registerUser()
{
    $dbConn = DBConnect();

// String containing errors
    $errors = "";

    if (isset($_POST['regUser'])) {
        // username validation
        $username = mysqli_real_escape_string($dbConn, $_POST['mail']);

        // get password from form
        $password = $_POST['password'];

        // form validation: ensure form is filled
        if (empty($username))
            $errors = $errors . " Email is required ";
        if (!validateMail($username))
            $errors = $errors . " Email is not valid ";
        if (!validatePassword($password))
            $errors = $errors . " Password is not valid ";


        // check user is not registered yet
        $queryCheckUser = "SELECT * from users WHERE mail='$username'";

        try {
            mysqli_autocommit($dbConn, false);
            if (!$resultQueryCheck = mysqli_query($dbConn, $queryCheckUser))
                throw new Exception("failed query check user");

            $user = mysqli_fetch_assoc($resultQueryCheck);
            if ($user)
                // user already registered
                $errors = $errors . " Username already exists ";

            // If there were no errors register a new user
            if ($errors == "") {
                $salt = createSalt();
                $hashedPasswd = hash('sha256', $password . $salt);

                $queryInsertUser = "INSERT INTO users (mail,password,salt)
                    values ('$username','$hashedPasswd','$salt')";

                mysqli_query($dbConn, $queryInsertUser);

                if (!mysqli_commit($dbConn))
                    throw new Exception("Commit failed");

                myRedirect("login.php");
            } else {
                mysqli_close($dbConn);
                die(header("location:register.php?registrationFailed=true&errors=" . urlencode($errors)));
            }


        } catch (Exception $e) {
            mysqli_rollback($dbConn);
            mysqli_autocommit($dbConn, true);
            mysqli_close($dbConn);
        }
        mysqli_autocommit($dbConn, true);
        mysqli_close($dbConn);
    }
}

function loginUser()
{
    $dbConn = DBConnect();

    // String containing errors
    $errors = "";

    if (isset($_POST['logUser'])) {
        // username validation
        $username = mysqli_real_escape_string($dbConn, $_POST['mail']);

        // get password from form
        $password = $_POST['password'];

        // form validation: ensure form is filled
        if (empty($username) || empty($password))
            $errors = " Email and password are required ";
        if (!validateMail($username))
            $errors = " Email is not valid ";

        if ($errors == "") {

            $queryLogin = "SELECT * FROM users WHERE mail='$username'";
            $resultQuery = mysqli_query($dbConn, $queryLogin);
            $user = mysqli_fetch_assoc($resultQuery);
            mysqli_close($dbConn);

            if ($user) {
                $salt = $user['salt'];
                $hashedPasswd = hash('sha256', $password . $salt);
                if ($hashedPasswd == $user['password']) {
                    // user autentication done
                    session_start();
                    $_SESSION['s267570_user'] = $username;
                    $_SESSION['s267570_time'] = time();
                    myRedirect("user-home.php");
                } else {
                    $errors = "Wrong username or password";
                    die(header("location:login.php?loginFailed=true&errors=" . urlencode($errors)));
                }
            } else {
                $errors = "Wrong username or password";
                die(header("location:login.php?loginFailed=true&errors=" . urlencode($errors)));
            }

        } else
            die(header("location:login.php?loginFailed=true&errors=" . urlencode($errors)));
    }
}

function createSalt()
{
    $salt = md5(uniqid(rand(), TRUE));
    return substr($salt, 0, 10);
}

function DBConnect()
{
    $connection = mysqli_connect("localhost", "root", "", "s267570_web");
    if (!$connection) {
        die('Error while connecting to db (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . ')');
    }
    return $connection;
}

function validateMail($mail)
{
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    if (preg_match($regex, $mail))
        return true;
    else
        return false;
}

function validatePassword($passwd)
{
    $regex = '/(\.*[a-z]+\.*[0-9]+\.*)|(\.*[a-z]+[A-Z]+\.*)|(\.*[0-9]+[a-z]+\.*)|(\.*[A-Z]+[a-z]+\.*)/';
    if (preg_match($regex, $passwd))
        return true;
    else
        return true;
}

?>
