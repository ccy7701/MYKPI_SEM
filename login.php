<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>Login | MyStudyKPI </title>
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
        <img class="header" src="images/loginheader.png" alt="Login Header">
    </header>
    <nav class="topnav" id="myTopnav">
        <a href="index.php" class="logo"><img src="images/mystudykpi-topnavbtn-2-white.png" alt="MyStudyKPI Logo"></a>
        <a href="login.php" class="tabs">Login</a>
        <a href="#menu" id="topnav-collapse-btn" class="icon" aria-label="Toggle navigation menu"><i class="fa fa-bars"></i></a>
    </nav>
    <main>
        <div id="center-content-login">
            <h4>Login to view all your information</h4>
            <div id="logindiv">
                <form id="loginform" action="action_scripts/login_action.php" method="post">
                    <input class="fieldlogin" name="loginmatric" type="text" placeholder="Matric Number" required><br>
                    <input class="fieldlogin" name="loginpassword" type="password" placeholder="Password" required><br>
                    <input class="btnlogin" name="loginsubmit" type="submit" value="LOGIN">
                    <input class="btnlogin" name="loginreset" type="reset" value="CLEAR"><br>
                </form>
            </div>

            <p>Do not have your own account yet?
            <a href="registration.php">Register here.</a>
            </p>
        </div>
    </main>
    <footer style="position: fixed; bottom: 0;">
        <h5>© Chiew Cheng Yi | BI21110236 | KK34703 Individual Project</h5>
    </footer>
</body>

</html>
