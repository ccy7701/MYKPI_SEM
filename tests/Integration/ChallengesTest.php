<?php

namespace Tests;

class ChallengesTest extends BaseTest
{
    private const CHLG_SUBMIT_ACTION_FILE = __DIR__ . "/../../action_scripts/challenge_submit_action.php";
    private const CHLG_REMOVE_ACTION_FILE = __DIR__ . "/../../action_scripts/challenge_remove_action.php";

    /**
     * Function to push test 'challenge' data
     */
    private function pushTestChallengeData($accountID, array $data)
    {
        $_POST = [
            'challengeSem' => $data['challengeSem'],
            'challengeYear' => $data['challengeYear'],
            'challengeDetails' => $data['challengeDetails'],
            'challengeFuturePlan' => $data['challengeFuturePlan'],
            'challengeRemark' => $data['challengeRemark'],
            'accountID' => $accountID,
        ];

        $_FILES = [
            'challengeImageToUpload' => [
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
        require self::CHLG_SUBMIT_ACTION_FILE;
        ob_get_clean();
    }

    /**
     * @runInSeparateProcess
     * Test for successful addition of new challenge
     */
    public function testAddChallenge()
    {
        // First create dummy account
        $this->createDummyAccount(
            'BI21110025',
            'add_challenge@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );
        $loginResult = $this->attemptLogin('BI21110025', self::STANDARD_PASSWORD);

        // Assert successful login
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Simulate form submission data
        $this->pushTestChallengeData($sessionAccountID, [
            'challengeSem' => 1,
            'challengeYear' => 1,
            'challengeDetails' => 'Test new challenge',
            'challengeFuturePlan' => 'Test new future plan',
            'challengeRemark' => 'Test new remark',
        ]);

        // Fetch the new challenge data
        $result = $this->fetchChallengeByDetails($sessionAccountID, 'Test new challenge');

        // Assert that a result is returned
        $this->assertEquals(1, $result->num_rows);

        // Assert that the returned data matches what is expected
        $row = $result->fetch_assoc();
        $this->assertEquals('Test new challenge', $row['challengeDetails']);
        $this->assertEquals('Test new future plan', $row['challengeFuturePlan']);
        $this->assertEquals($sessionAccountID, $row['accountID']);

        // Clean up session
        $this->cleanUpSession();
    }

    /**
     * @runInSeparateProcess
     * Test for successful deletion of existing challenge
     */
    public function testDeleteChallenge()
    {
        // Create dummy account and log it in
        $this->createDummyAccount(
            'BI21110026',
            'delete_challenge@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );
        $loginResult = $this->attemptLogin('BI21110026', self::STANDARD_PASSWORD);

        // Assert successful login
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Push a dummy challenge
        $this->pushTestChallengeData($sessionAccountID, [
            'challengeSem' => 2,
            'challengeYear' => 4,
            'challengeDetails' => 'Test internship challenge',
            'challengeFuturePlan' => 'Commit to internship',
            'challengeRemark' => 'Test remark',
        ]);

        // Assert that the data was inserted
        $result = $this->fetchChallengeByDetails($sessionAccountID, 'Test internship challenge');
        $this->assertEquals(1, $result->num_rows);

        // Then, simulate deletion
        $row = $result->fetch_assoc();
        $challengeID = $row['challengeID'];
        $challengeImagePath = $row['challengeImagePath'];

        // Simulate deletion
        $_GET['id'] = $challengeID;
        $_SERVER["REQUEST_METHOD"] = "GET";
        
        global $conn;
        $conn = $this->conn;

        // Include the action script
        ob_start();
        require self::CHLG_REMOVE_ACTION_FILE;
        ob_get_clean();

        // Assert that the challenge was removed
        $result = $this->fetchChallengeByDetails($sessionAccountID, 'Test internship challenge');
        $this->assertEquals(0, $result->num_rows);

        // Clean up session
        $this->cleanUpSession();
    }
}
