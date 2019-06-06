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

function printSeatInformation(){
    $totalSeats = ROW_NUMBER * COL_NUMBER;
    $boughtSeats = 0; // todo query
    $bookedSeats = 0; // todo query
    $freeSeats = $totalSeats - $boughtSeats - $bookedSeats;
    echo "<li class='status_total'>Total seats: ".$totalSeats."</li>";
    echo "<li class='status_free'>Free seats: ".$freeSeats."</li>";
    echo "<li class='status_bought'>Bought seats: ".$boughtSeats."</li>";
    echo "<li class='status_booked'>Booked seats: ".$bookedSeats."</li>";
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

?>
