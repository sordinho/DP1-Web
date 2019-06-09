<?php
define('ROW_NUMBER', 10);
define('COL_NUMBER', 6);
define('TOTAL_SEATS', COL_NUMBER * ROW_NUMBER);
define('INACTIVITY_TIME', 1200); // TODO set to 120

// global variables for seat status
$notFreeSeats = array();
$seatStatus = array();

function printSeatTable($clickable)
{
    global $notFreeSeats;
    $letters = range('A', 'Z');
    for ($i = 1; $i <= ROW_NUMBER; $i++) {
        echo "<tr>";
        for ($j = 1; $j <= COL_NUMBER; $j++) {
            $buttonName = $letters[$i - 1] . "_" . $j;
            $seatClass = 'freeSeat';
            if (isset($notFreeSeats[$buttonName]))
                $seatClass = $notFreeSeats[$buttonName];
            if (($seatClass == 'soldSeat') || ($seatClass == 'bookedSeat') || ($clickable == false))
                $buttonAttribute = "disabled";
            else
                $buttonAttribute = " ";
            echo '<td><button type="button" ' . $buttonAttribute . ' class="' . $seatClass . '" name="' . $buttonName . '">' . $buttonName . '</button></td>';
        }
        echo "</tr>";
    }
}

function printSeatInformation($loggedIn = false)
{
    global $seatStatus;

    if (isset($_SESSION['s267570_user']))
        retrieveSeatStatus($_SESSION['s267570_user']);
    else
        retrieveSeatStatus();

    $soldSeats = $seatStatus['sold'];
    $bookedSeats = $seatStatus['booked'];
    $myBooking = $seatStatus['myBook'];
    $freeSeats = $seatStatus['free'];
    echo "<ul>";
    echo "<li id='status_total'>Total seats: " . TOTAL_SEATS . "</li>";
    echo "<li id='status_free'>Free seats: " . $freeSeats . "</li>";
    echo "<li id='status_sold'>Sold seats: " . $soldSeats . "</li>";
    echo "<li id='status_booked'>Booked seats: " . $bookedSeats . "</li>";
    if ($loggedIn) {
        echo "<li id='status_my_booking'>My booked seats: " . $myBooking . "</li>";
        echo "</ul><hr>";
        echo "<button type='button' class='utilityButton' name='update' onclick='window.location.href=window.location.href'>Update</button><br>";
        echo "<button type='button' class='utilityButton' name='buy' >Buy</button>";
    } else
        echo "</ul>";
}

function retrieveSeatStatus($user = null)
{
    $dbConn = DBConnect();
    global $seatStatus;
    global $notFreeSeats;

    // initialization
    $seatStatus['free'] = TOTAL_SEATS;
    $seatStatus['booked'] = 0;
    $seatStatus['sold'] = 0;
    $seatStatus['myBook'] = 0;

    $retrieveQuery = "SELECT * FROM bookings";
    $resultQuery = mysqli_query($dbConn, $retrieveQuery);

    if (mysqli_num_rows($resultQuery) > 0) {

        while ($row = mysqli_fetch_assoc($resultQuery)) {
            $seatStatus['free']--;

            if ($row['status'] == 'sold') {
                $seatStatus['sold']++;
                $notFreeSeats[$row['seat']] = 'soldSeat';
            }
            if ($row['status'] == 'booked') {
                if ($row['user'] == $user) {
                    $seatStatus['myBook']++;
                    $notFreeSeats[$row['seat']] = 'myBookedSeat';
                } else {
                    $seatStatus['booked']++;
                    $notFreeSeats[$row['seat']] = 'bookedSeat';
                }
            }
        }
    }
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
    if ($diff > INACTIVITY_TIME) {
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

function myRedirect($page, $msg = null)
{
    $redirectPage = 'https://' . $_SERVER['HTTP_HOST'] . '/DP1-Web/' . $page;
    if ($msg != null) {
        $msg = urlencode($msg);
        $redirectPage = $redirectPage . "?msg=true&info=" . $msg;
    }
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

                myRedirect("login.php", "Registered successfully, you can now login");
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
