<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Home Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Sordinho AirLines</h1>
</header>

<aside>
    <ul>
        <li><a href="http://">Login</a></li>
        <li><a href="http://">Register</a></li>
    </ul>
</aside>

<article>
    <section>
		<h1>Tabella</h1>
        <table id='seatTable'>
            <?php
            include ("functions.php");
            printSeatTable();
            ?>
        </table>
    </section>
</article>


<footer><p>footer</p></footer>
</body>
</html>
