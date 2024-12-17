<?php
    session_start();
    include("include/config.php");
?>

<!DOCTYPE HTML>
<html lang="en">

<html>

<head>
    <title>Activity Image</title>
    <meta charset="utf8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Jost">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="sitejavascript.js"></script>
</head>

<body>
    <header>

    </header>
    <nav>

    </nav>
    <main>
        <?php
            if (isset($_GET["id"]) && $_GET["id"] != "") {
                $id = $_GET["id"];
                $fetchImagePathQuery = "SELECT * FROM activity WHERE activityID=".$id;
                $result = mysqli_query($conn, $fetchImagePathQuery);
                $row = mysqli_fetch_assoc($result);

                // fetch only the image path
                $activityImagePath = $row["activityImagePath"];

                mysqli_close($conn);
            }
        ?>
        <img src="<?=$activityImagePath?>" alt="Image">
    </main>
    <footer>

    </footer>
</body>

</html>