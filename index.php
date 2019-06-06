<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Home Page</title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="functions.js"></script>
</head>
<body>
<header>
    <h1>Sordinho AirLines</h1>
</header>

<aside>
    <h1 class="aside_menu">Menu</h1>
    <ul>
        <li><a href="https://localhost/DP1-Web/login.php">Login</a></li>
        <li><a href="https://localhost/DP1-Web/register.php">Register</a></li>
    </ul>

</aside>

<article>
    <section>
        <h1>Tabella</h1>
        <table id='seatTable'>
            <?php
            include("functions.php");
            printSeatTable(false);
            ?>
        </table>
    </section>
</article>


<footer><p class="copyright">Copyright © Polito-PD1 Web 2019 &nbsp;&nbsp;&nbsp;&nbsp; Designed by: Davide Sordi &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="https://github.com/sordinho">GitHub</a></p></footer>
</body>
</html>
