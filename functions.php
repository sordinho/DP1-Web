<?php

/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 05/06/2019
 *
 * Utility functions file
 */

// CONSTANTS
define('ROW_NUMBER', 10);
define('COL_NUMBER', 6);
define('TOTAL_SEATS', COL_NUMBER * ROW_NUMBER);
define('INACTIVITY_TIME', 120); // inactivity time set to 2 minutes

// global variables
$notFreeSeats = array(); // list of non free seats
$seatStatus = array(); // array with general statistics on seats status

/**
 * Function used to retrieve seat status from DB
 * @param null $user : if user is not null we need to differentiate booked from user-booked seats
 */
function retrieveSeatStatus($user = null)
{
    // create DB connection
    $dbConn = DBConnect();
    global $seatStatus;
    global $notFreeSeats;

    // initialization
    $seatStatus['free'] = TOTAL_SEATS; //we assume all free at start
    $seatStatus['booked'] = 0;
    $seatStatus['sold'] = 0;
    $seatStatus['myBook'] = 0;

    // query to get bookings from DB
    $retrieveQuery = "SELECT * FROM bookings";
    $resultQuery = mysqli_query($dbConn, $retrieveQuery);

    // if there is some booked or sold seat
    if (mysqli_num_rows($resultQuery) > 0) {

        // iterate on query result
        while ($row = mysqli_fetch_assoc($resultQuery)) {
            $seatStatus['free']--; //decrement number of free seat

            // sold seat
            if ($row['status'] == 'sold') {
                $seatStatus['sold']++;
                $notFreeSeats[$row['seat']] = 'soldSeat';
            }

            // booked seat
            if ($row['status'] == 'booked') {

                // current user booked
                if ($row['user'] == $user) {
                    $seatStatus['myBook']++;
                    $notFreeSeats[$row['seat']] = 'myBookedSeat';
                } // booked from another user
                else {
                    $seatStatus['booked']++;
                    $notFreeSeats[$row['seat']] = 'bookedSeat';
                }
            }
        }
    }
}

/**
 * Function who print the status of the seats on the sidebar
 * @param bool $loggedIn : if user is logged in we need to differentiate booked seats
 */
function printSeatInformation($loggedIn = false)
{
    global $seatStatus; // use global variable

    // get user from session if set and retrieve seat status
    if (isset($_SESSION['s267570_user']))
        retrieveSeatStatus($_SESSION['s267570_user']);
    else
        retrieveSeatStatus();

    // variable set from global associative array set from previous function
    $soldSeats = $seatStatus['sold'];
    $bookedSeats = $seatStatus['booked'];
    $myBooking = $seatStatus['myBook'];
    $freeSeats = $seatStatus['free'];

    // HTML printing
    echo "<ul>";
    echo "<li id='status_total'>Total seats: " . TOTAL_SEATS . "</li>";
    echo "<li id='status_free'>Free seats: " . $freeSeats . "</li>";
    echo "<li id='status_sold'>Sold seats: " . $soldSeats . "</li>";
    echo "<li id='status_booked'>Booked seats: " . $bookedSeats . "</li>";

    // if user logged add a row and buttons to update and buy
    if ($loggedIn) {
        echo "<li id='status_my_booking'>My booked seats: " . $myBooking . "</li>";
        echo "</ul><hr>";

        // update button just refresh the page and delete previous communication from url (errors)
        echo "<button type='button' class='utilityButton' name='update' title='Update seat status' onclick='window.location.replace(location.pathname)'>Update</button><br>";
        echo "<input type='submit' class='utilityButton' name='buy' title='Buy booked seats' value='Buy'/>";
    } else
        echo "</ul>";
}

/**
 * Function used to print the table with seats
 * @param $clickable : set seats clickable or not
 */
function printSeatTable($clickable)
{
    global $notFreeSeats; // refer to global variable
    $letters = range('A', 'Z'); // create range of letters, note: estimated it is enough to have 26 rows

    for ($i = 1; $i <= ROW_NUMBER; $i++) {
        echo "<tr>";

        for ($j = 1; $j <= COL_NUMBER; $j++) {
            $buttonName = $letters[$i - 1] . "_" . $j; // generate button name as ROW_COL
            $seatClass = 'freeSeat'; // default set it as free

            // check if this seat is not a free one and set new class
            if (isset($notFreeSeats[$buttonName]))
                $seatClass = $notFreeSeats[$buttonName];

            // if seat is sold or we print for index page we set to not clickable
            if (($seatClass == 'soldSeat') || ($clickable == false))
                $buttonAttribute = "disabled";
            else
                $buttonAttribute = " ";
            echo '<td><button type="button" ' . $buttonAttribute . ' class="' . $seatClass . '" id="' . $buttonName . '" onclick="bookAjax(this.id)">' . $buttonName . '</button>';

            // add hidden input type for not sold seats and set value for my booking
            $hiddenValue = "";
            if ($seatClass == 'myBookedSeat')
                $hiddenValue = 'BS';
            if ($buttonAttribute == " ")
                echo '<input type="hidden" value="' . $hiddenValue . '" id="' . $buttonName . '_HIDDEN" name="' . $buttonName . '"/></td>';
            else
                echo '</td>';
        }
        echo "</tr>";
    }
}

/**
 * Function to check https protocol and redirect in case is not used
 */
function checkHTTPS()
{
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
        $redirectPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirectPage);
        exit();
    }
}

/**
 * Function to check session validity, also check for inactivity
 */
function checkSession()
{
    // get session or create a new one
    session_start();

    // if user or time is not set we redirect to login page.
    if ((!isset($_SESSION['s267570_user'])) || (!isset($_SESSION['s267570_time']))) {
        myRedirect("login.php");
    }

    // check inactivity
    $t = time();
    $diff = $t - $_SESSION['s267570_time'];

    // user inactive
    if ($diff > INACTIVITY_TIME) {
        // Kill session, and set invalid cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 3600 * 24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        myRedirect("login.php");
    } else {
        // update time session
        $_SESSION['s267570_time'] = time();
    }
}

/** Custom redirect function todo check
 * @param $page: destination page
 * @param null $msg: optional message to send in GET
 */
function myRedirect($page, $msg = null)
{
    $redirectPage = 'https://' . $_SERVER['HTTP_HOST'] . '/~s267570/4e3c03/' . $page; // todo modify folder
    if ($msg != null) {
        $msg = urlencode($msg);
        $redirectPage = $redirectPage . "?msg=true&info=" . $msg;
    }
    header('Location: ' . $redirectPage);
    exit();
}

/**
 * Function for register a new user
 */
function registerUser()
{
    // Create DB connection
    $dbConn = DBConnect();

    // String containing errors
    $errors = "";

    // analyze and validate form data
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
                $salt = createSalt(); // create salt for password hash
                $hashedPasswd = hash('sha256', $password . $salt); //hash password and salt using SHA256

                // insert user in DB
                $queryInsertUser = "INSERT INTO users (mail,password,salt)
                    values ('$username','$hashedPasswd','$salt')";

                mysqli_query($dbConn, $queryInsertUser);

                // commit
                if (!mysqli_commit($dbConn))
                    throw new Exception("Commit failed");

                // if registration successfully completed redirect to login
                myRedirect("login.php", "Registered successfully, you can now login");
            }
            // if there is an error we need to close connection and redirect with errors
            else {
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

/**
 * Function to authenticate a user
 */
function loginUser()
{
    // create SB connection
    $dbConn = DBConnect();

    // String containing errors
    $errors = "";

    // check form validity
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

        // if there are no errors we can authenticate user
        if ($errors == "") {

            // query to get info about user
            $queryLogin = "SELECT * FROM users WHERE mail='$username'";
            $resultQuery = mysqli_query($dbConn, $queryLogin);
            $user = mysqli_fetch_assoc($resultQuery);
            mysqli_close($dbConn);

            // user is registered
            if ($user) {

                // get salt from db  and hash password
                $salt = $user['salt'];
                $hashedPasswd = hash('sha256', $password . $salt);

                // check if inserted password is equal to the one on DB
                if ($hashedPasswd == $user['password']) {

                    // user authentication done, create new session
                    session_start();
                    $_SESSION['s267570_user'] = $username;
                    $_SESSION['s267570_time'] = time();
                    myRedirect("user-home.php");
                } else {
                    // Wrong password, but we say it may be wrong username too
                    $errors = "Wrong username or password";
                    die(header("location:login.php?loginFailed=true&errors=" . urlencode($errors)));
                }
            } else {
                // username was wrong in this case
                $errors = "Wrong username or password";
                die(header("location:login.php?loginFailed=true&errors=" . urlencode($errors)));
            }
        // form data was not valid
        } else
            die(header("location:login.php?loginFailed=true&errors=" . urlencode($errors)));
    }
}

/**
 * Function for creating a casual salt based on time
 * @return bool|string: return salt
 */
function createSalt()
{
    $salt = md5(uniqid(rand(), TRUE));
    return substr($salt, 0, 10);
}

/**
 * Function for creating a new connection to the DB
 * @return false|mysqli
 */
function DBConnect()
{
    $connection = mysqli_connect("localhost", "s267570", "owellers", "s267570");
    if (!$connection) {
        die('Error while connecting to db (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . ')');
    }
    return $connection;
}

/**
 * Function to validate email
 * @param $mail
 * @return bool
 */
function validateMail($mail)
{
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    if (preg_match($regex, $mail))
        return true;
    else
        return false;
}

/**
 * Function to validate password, use same regex of the one in JS
 * @param $passwd
 * @return bool
 */
function validatePassword($passwd)
{
    $regex = '/(\.*[a-z]+\.*[0-9]+\.*)|(\.*[a-z]+[A-Z]+\.*)|(\.*[0-9]+[a-z]+\.*)|(\.*[A-Z]+[a-z]+\.*)/';
    if (preg_match($regex, $passwd))
        return true;
    else
        return true;
}

?>
