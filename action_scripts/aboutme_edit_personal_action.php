<?php
    session_start();

    require_once "../include/config.php";
?>

<!DOCTYPE HTML>
<html lang="en" xml:lang="en">

<head>
    <title>Edit Confirmation</title>
    <script src="../sitejavascript.js"></script>
</head>

<body>
    <?php
        // commit to the database the data from the editable fields ONLY
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $target = $_SESSION["UID"];
            $newUsername = mysqli_real_escape_string($conn, $_POST["username"]);
            $newProgram = mysqli_real_escape_string($conn, $_POST["program"]);
            $newIntakeBatch = mysqli_real_escape_string($conn, $_POST["intakeBatch"]);
            $newPhoneNumber = mysqli_real_escape_string($conn, $_POST["phoneNumber"]);
            $newMentor = mysqli_real_escape_string($conn, $_POST["mentor"]);
            $newProfileState = mysqli_real_escape_string($conn, $_POST["profileState"]);
            $newProfileAddress = mysqli_real_escape_string($conn, $_POST["profileAddress"]);
            $newMotto = mysqli_real_escape_string($conn, $_POST["motto"]);

            // for image upload
            $pfpUploadFlag = 0;

            // IF THERE IS NO NEW IMAGE
            if (isset($_FILES["pfpToUpload"]) && $_FILES["pfpToUpload"]["name"] == "") {
                // Update the database without a new image
                $pushToDBQuery = "UPDATE profile 
                                  SET username = ?, program = ?, intakeBatch = ?, phoneNumber = ?, 
                                      mentor = ?, profileState = ?, profileAddress = ?, motto = ? 
                                  WHERE accountID = ?";
            
                $stmt = mysqli_prepare($conn, $pushToDBQuery);
                mysqli_stmt_bind_param($stmt, "ssissssss", $newUsername, $newProgram, $newIntakeBatch,
                                                      $newPhoneNumber, $newMentor, $newProfileState,
                                                      $newProfileAddress, $newMotto, $target);
            
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>popup(\"Personal info updated successfully.\", \"../aboutme.php\");</script>";
                } else {
                    echo "<script>popup(\"Oops. Something went wrong: " . mysqli_error($conn) . "\", \"../aboutme_edit_personal.php\");</script>";
                }
                mysqli_stmt_close($stmt);
            } 
            elseif (isset($_FILES["pfpToUpload"]) && $_FILES["pfpToUpload"]["error"] == UPLOAD_ERR_OK) {
                $pfpUploadFlag = 1;
                $targetDirectory = "uploads/profile_images/";
                $pfpFileName = basename($_FILES["pfpToUpload"]["name"]);
                $targetFile = "../" . $targetDirectory . $target . "_" . $pfpFileName;
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
                // Validate file
                if (file_exists($targetFile)) {
                    echo "<script>popup(\"ERROR-1: File already exists.\", \"../aboutme_edit_personal.php\");</script>";
                    $pfpUploadFlag = 0;
                }
                if ($_FILES["pfpToUpload"]["size"] > 2097152) {
                    echo "<script>popup(\"ERROR-2: File size exceeds allowed limit.\", \"../aboutme_edit_personal.php\");</script>";
                    $pfpUploadFlag = 0;
                }
                if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
                    echo "<script>popup(\"ERROR-3: Invalid file type.\", \"../aboutme_edit_personal.php\");</script>";
                    $pfpUploadFlag = 0;
                }
            
                if ($pfpUploadFlag) {
                    // Remove the existing profile image
                    $imgPathSeekQuery = "SELECT profileImagePath FROM profile WHERE accountID = ?";
                    $stmt = mysqli_prepare($conn, $imgPathSeekQuery);
                    mysqli_stmt_bind_param($stmt, "s", $target);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    $imgToDelete = "../" . $row["profileImagePath"];
            
                    if (!empty($imgToDelete) && file_exists($imgToDelete)) {
                        unlink($imgToDelete);
                    }
                    mysqli_stmt_close($stmt);
            
                    // Update the profile image path in the database
                    $imgName = $target . "_" . $pfpFileName;
                    $fullPath = $targetDirectory . $imgName;
                    $updateQuery = "UPDATE profile 
                                    SET username = ?, program = ?, intakeBatch = ?, phoneNumber = ?, 
                                        mentor = ?, profileState = ?, profileAddress = ?, motto = ?, 
                                        profileImagePath = ?
                                    WHERE accountID = ?";
                    $stmt = mysqli_prepare($conn, $updateQuery);
                    mysqli_stmt_bind_param($stmt, "ssisssssss", $newUsername, $newProgram, $newIntakeBatch,
                                                          $newPhoneNumber, $newMentor, $newProfileState,
                                                          $newProfileAddress, $newMotto, $fullPath, $target);
            
                    if (mysqli_stmt_execute($stmt)) {
                        if (move_uploaded_file($_FILES["pfpToUpload"]["tmp_name"], $targetFile)) {
                            echo "<script>popup(\"Personal info updated successfully.\", \"../aboutme.php\");</script>";
                        } else {
                            echo "<script>popup(\"ERROR: Failed to move uploaded file.\", \"../aboutme_edit_personal.php\");</script>";
                        }
                    } else {
                        echo "<script>popup(\"Oops. Something went wrong: " . mysqli_error($conn) . "\", \"../aboutme_edit_personal.php\");</script>";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            
            mysqli_close($conn);
        }
    ?>
</body>

</html>