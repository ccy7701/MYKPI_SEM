<?php
    session_start();
?>

<!DOCTYPE HTML>

<html lang="en">

<head>
    <title>Home | MyStudyKPI </title>
    <meta charset="utf8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Jost">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="js/sitejavascript.js"></script>
    <script src="js/topnav.js"></script>
</head>

<body>
    <header>
        <img class="header" src="images/indexheader2.png" alt="Index Header">
    </header>
    <nav class="topnav" id="myTopnav">
        <?php
            include("include/login_session_check.php");
        ?>
        <button href="#menu" id="topnav-collapse-btn" class="icon" aria-label="Toggle navigation menu"><i class="fa fa-bars"></i></button>
    </nav>
    <main>
        <div id="center-content">
            <?php
                if (isset($_SESSION["UID"])) {
                    echo "
                        <h1>Welcome back</h1>
                    ";
                }
                else {
                    echo "
                        <h1>Welcome to the UMS FKI MyStudyKPI website</h1>
                    ";
                }
            ?>
            <div class="block">
                <i class="fa fa-user-circle-o"></i>
                <p><b>About Me:</b> Get a concise overview of your information in a portfolio webpage.</p>
            </div>
            <br>
            <div class="block">
                <i class="fa fa-line-chart"></i>
                <p><b>MyKPI Indicator Module:</b> A tool to manage your KPIs, such as CGPA, number of activites joined, and other goals.</p>
            </div>
            <br>
            <div class="block">
                <i class="fa fa-calendar-check-o"></i>
                <p><b>Activities List:</b> View, organize and manage all the activites you joined throughout your study years.</p>
            </div>
            <br>
            <div class="block">
                <i class="fa fa-meh-o"></i>
                <p><b>Challenges and Future Plans:</b> Facing challenges in your studies? Put what's on your mind into words to help you plan accordingly.</p>
            </div>
            <br>
        </div>
    </main>
    <footer>
        <h5>© Chiew Cheng Yi | BI21110236 | KK34703 Individual Project</h5>
    </footer>
</body>

</html>
