<?php

namespace Tests;

class ActivitiesListTest extends BaseTest
{
    private const ACTV_SUBMIT_ACTION_FILE = __DIR__ . "/../../action_scripts/activitieslist_submit_action.php";

    /**
     * Function to push test 'activity' data
     */
    private function pushTestActivityData($accountID, array $data)
    {
        $_POST = [
            'activitySem' => $data['activitySem'],
            'activityYear' => $data['activityYear'],
            'activityType' => $data['activityType'],
            'activityLevel' => $data['activityLevel'],
            'activityDetails' => $data['activityDetails'],
            'activityRemarks' => $data['activityRemarks'],
            'accountID' => $accountID,
        ];

        $_FILES = [
            'activityImageToUpload' => [
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
        require self::ACTV_SUBMIT_ACTION_FILE;
        ob_get_clean();
    }

    /**
     * @runInSeparateProcess
     * Test for successful addition of new activity
     */
    public function testAddActivity()
    {
        // First create dummy account
        $this->createDummyAccount(
            'BI21110015',
            'add_activity@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );
        $loginResult = $this->attemptLogin('BI21110015', self::STANDARD_PASSWORD);

        // Assert successful login
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Simulate form submission data
        $this->pushTestActivityData($sessionAccountID, [
            'activitySem' => 1,
            'activityYear' => 1,
            'activityType' => 1,
            'activityLevel' => 2,
            'activityDetails' => 'New test activity',
            'activityRemarks' => 'New test remarks',
        ]);

        // Fetch the new activity data
        $result = $this->fetchActivityByDetails($sessionAccountID, 'New test activity');

        // Assert that a result is retrned
        $this->assertEquals(1, $result->num_rows);

        // Assert that the returned data matches what is expected
        $row = $result->fetch_assoc();
        $this->assertEquals('New test activity', $row['activityDetails']);
        $this->assertEquals('New test remarks', $row['activityRemarks']);
        $this->assertEquals($sessionAccountID, $row['accountID']);

        // Clean up session
        $this->cleanUpSession();
    }
}
