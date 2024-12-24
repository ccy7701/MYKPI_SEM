<?php

namespace Tests;

class ChallengesTest extends BaseTest
{
    private const CHLG_SUBMIT_ACTION_FILE = __DIR__ . "/../../action_scripts/challenge_submit_action.php";

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
}
