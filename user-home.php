<?php
include('functions.php');
checkHTTPS();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Your Homr</title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="functions.js"></script>
    <noscript>
        <div class='noscript'>
            <h1>Attention: you need to enable javascript in order to use the website.</h1>
        </div>
    </noscript>
</head>
<body>
<script>checkCookies();</script>
<header>
    <h1>Sordinho AirLines</h1>
</header>

<aside>
    <h1 class="aside_menu">Menu</h1>
    <h3 class="aside_menu">Welcome back, <br>
        <?php
        if (isset($_SESSION['user']))
            echo $_SESSION['user'];
        else
            echo "ERROR";
        ?>
    </h3>
    <ul>
        <li><a href="https://localhost/DP1-Web/logout.php">Logout</a></li>
    </ul>
    <h1 class="aside_menu">Status</h1>
    <ul>
        <?php
        printSeatInformation();
        ?>
    </ul>
</aside>

<article>
    <section>
        <h1>Seats Table</h1>
        <table id='seatTable'>
            <?php
            printSeatTable(true);
            ?>
        </table>
    </section>
</article>


<footer><p class="copyright">Copyright © Polito-PD1 Web 2019 &nbsp;&nbsp;&nbsp;&nbsp; Designed by: Davide Sordi &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="https://github.com/sordinho">GitHub</a></p></footer>
</body>
</html>
