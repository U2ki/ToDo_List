<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>

    <header>
            <p>ToDo Chicken</p>
    <nav>
        <ul>
            <li><a href="./signup.php">SignUp</li></a>
            <li><a href="./login.php">LogIn</li></a>
        </ul>
    </nav>
    </header>

    <body>
 <?php
    session_start();
    session_destroy();
 ?>
 <img src="img/logout.png" width="700" height="700" style="margin-top: 3em;">
    </body>
</html>
