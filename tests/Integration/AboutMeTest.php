<?php

namespace Tests;

class AboutMeTest extends BaseTest
{
    private const ABOUTME_EDIT_ACTION_FILE = __DIR__ . "/../../action_scripts/aboutme_edit_personal_action.php";
    
    private const STANDARD_PASSWORD = "Test@1234";

    private function seedProfileData($accountID, $data)
    {
        // Build an SQL query to insert user data into the profile table
        $query = "INSERT INTO profile (
            username, program, intakeBatch, phoneNumber,
            mentor, profileState, profileAddress, motto,
            profileImagePath, accountID
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the query
        $stmt = $this->conn->prepare($query);

        // Bind the data dynamically
        $stmt->bind_param('ssissssssi',
            $data['username'],
            $data['program'],
            $data['intakeBatch'],
            $data['phoneNumber'],
            $data['mentor'],
            $data['profileState'],
            $data['profileAddress'],
            $data['motto'],
            $data['profileImagePath'],
            $accountID
        );

        // Execute the query
        $stmt->execute();

        // Clean up
        $stmt->close();
    }

    /**
     * @runInSeparateProcess
     * Test updating the user's About Me information without uploading an image
     */
    public function testUpdateAboutMeWithoutImage()
    {
        // First create a dummy account and log it in
        $this->createDummyAccount(
            'BI21110005',
            'update_without_image@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );
        $loginResult = $this->attemptLogin('BI21110005', self::STANDARD_PASSWORD);

        // Assert that the login is successful
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Simulate a POST request to update data
        $_POST = [
            'username' => 'Updated username',
            'program' => 'hc14',
            'intakeBatch' => 2023,
            'phoneNumber' => '0198765432',
            'mentor' => 'Updated mentor',
            'profileState' => 'Sarawak',
            'profileAddress' => 'Updated address',
            'motto' => 'Never Stop Learning',
        ];

        $_FILES = [
            'pfpToUpload' => [
                'name' => '',
                'tmp_name' => '',
                'size' => 0,
                'error' => UPLOAD_ERR_NO_FILE,
            ]
        ];

        $_SERVER["REQUEST_METHOD"] = "POST";
        global $conn;
        $conn = $this->conn;

        ob_start();
        require self::ABOUTME_EDIT_ACTION_FILE;
        ob_get_clean();

        // Fetch the updated profile data
        $result = $this->fetchProfile($sessionAccountID);
        $profile = $result->fetch_assoc();

        // Assertions
        $this->assertEquals($sessionAccountID, $profile['accountID']);
        $this->assertEquals('uploads/profile_images/default.png', $profile['profileImagePath']);

        // Clean up session
        session_unset();
        session_destroy();
    }

    /**
     * @runInSeparateProcess
     * Test updating the user's About Me information together with an image
     */
    public function testUpdateAboutMeIncludingImage()
    {
        $this->createDummyAccount(
            'BI21110006',
            'update_including_image@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );
        $loginResult = $this->attemptLogin('BI21110006', self::STANDARD_PASSWORD);
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Fetch the existing profile data
        $result = $this->fetchProfile($sessionAccountID);
        $profile = $result->fetch_assoc();

        $_POST = [
            'username' => $profile['username'],
            'program' => $profile['program'],
            'intakeBatch' => $profile['intakeBatch'],
            'phoneNumber' => $profile['phoneNumber'],
            'mentor' => $profile['mentor'],
            'profileState' => $profile['profileState'],
            'profileAddress' => $profile['profileAddress'],
            'motto' => $profile['motto'],
        ];

        $_FILES = [
            'pfpToUpload' => [
                'name' => 'test_profile_image.png',
                'tmp_name' => __DIR__ . '/Fixtures/test_profile_image.png',
                'size' => 102400,
                'error' => UPLOAD_ERR_OK,
            ],
        ];

        $_SERVER["REQUEST_METHOD"] = "POST";
        global $conn;
        $conn = $this->conn;

        ob_start();
        require self::ABOUTME_EDIT_ACTION_FILE;
        ob_get_clean();

        // Fetch the updated profile data
        $result = $this->fetchProfile($sessionAccountID);
        $profile = $result->fetch_assoc();

        $expectedFilePath = "uploads/profile_images/" . $sessionAccountID . "_new_profile_image.png";

        // Assertions
        $this->assertEquals($sessionAccountID, $profile['accountID']);
        $this->assertFileExists("../", $expectedFilePath, "Uploaded file does not exist in the specified location");

        // Clean up session
        session_unset();
        session_destroy();
    }
}