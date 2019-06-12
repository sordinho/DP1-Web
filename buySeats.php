<?php
/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 12/06/2019
 */

include('functions.php');
checkHTTPS();
checkSession();



$user = $_SESSION['s267570_user'];
$countBooked = 0;
$seats = array();
foreach ($_POST as $key => $value) {
//    echo " <h1> $key=$value </h1>";
    if ($value == "BS") {
        $countBooked++;
        array_push($seats,$key);
    }
}

print_r($seats);
//if ($countBooked == 0)
//die(header("location:user-home.php?error=true&errors=" . urlencode("You need to book at least one seat"))); // todo uncomment

$dbConn = DBConnect();

$queryGetBookings = "SELECT count(*) FROM bookings WHERE user='' and status='booked' FOR UPDATE ";

try {
    mysqli_autocommit($dbConn, false);



















    if ($newStatus == 'free') {
        // check if booking was mine and set it to free (delete from db)
        if (!$resultCheckBeforeFree = mysqli_query($dbConn, $queryCheckMyBooking))
            throw new Exception("failed query check seat before freeing");

        $seatStatus = mysqli_fetch_assoc($resultCheckBeforeFree);
        if ($seatStatus) {
            if ($seatStatus['user'] == $user) {
                if (!mysqli_query($dbConn, $queryDeleteBooking))
                    throw new Exception("failed to update booking");
                $response = 'freeSeat';
            } else {
                // seat was not related to the user (booked or bought from another user)
                $response = $seatStatus['status'];
            }
        }
    } elseif ($newStatus == 'booked') {
        if (!$resultCheckFree = mysqli_query($dbConn, $queryCheckFree))
            throw new Exception("failed query check seat free before booking");

        $seatStatus = mysqli_fetch_assoc($resultCheckFree);
        if ($seatStatus) {
            // was booked or sold
            // query update or exit
            if ($seatStatus['status'] == "sold") {
                // seat was already sold
                $response = 'soldSeat';
            } else {
                // update Seat
                if (!mysqli_query($dbConn, $queryUpdateBooking))
                    throw new Exception("failed to insert new booking");
                $response = 'myBookedSeat';
            }
        } else {
            // was free
            // query insert booking
            if (!mysqli_query($dbConn, $queryInsertBooking))
                throw new Exception("failed to insert new booking");
            $response = 'myBookedSeat';
        }


    }

    if (!mysqli_commit($dbConn))
        throw new Exception("Commit failed");

    // all was good return
    echo $response;

} catch (Exception $e) {
    mysqli_rollback($dbConn);
    mysqli_autocommit($dbConn, true);
    mysqli_close($dbConn);
}
mysqli_autocommit($dbConn, true);
mysqli_close($dbConn);

?>