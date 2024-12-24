<?php

namespace Tests;

class ActivitiesFetchTest extends BaseTest
{
    /**
     * @runInSeparateProcess
     * Test fetching the user's activities by activity type
     */
    public function testFetchActivities()
    {
        // First create a dummy account and log it in
        $this->createDummyAccount(
            'BI21110010',
            'fetch_activities@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );
        $loginResult = $this->attemptLogin('BI21110010', self::STANDARD_PASSWORD);

        // Assert successful login
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Seed database with test data using a prepared statement
        $activityType = 1;
        $activityLevel = 2;
        $activityDetails = 'Test Activity';
        $activityRemarks = 'Test Remarks';
        $activityImagePath = '';
        $activitySem = 1;
        $activityYear = 1;

        $stmt = $this->conn->prepare(
            "INSERT INTO activity 
             (accountID, activityType, activityLevel, activityDetails, activityRemarks, activityImagePath, activitySem, activityYear) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iissssii",
            $sessionAccountID,
            $activityType,
            $activityLevel,
            $activityDetails,
            $activityRemarks,
            $activityImagePath,
            $activitySem,
            $activityYear
        );
        $stmt->execute();

        // Execute query to fetch activity by accountID and type
        $result = $this->fetchActivityByType($sessionAccountID, $activityType);

        // Assert that a result is returned
        $this->assertEquals(1, $result->num_rows);

        // Assert that the returned data matches the seeded data
        $row = $result->fetch_assoc();
        $this->assertEquals('Test Activity', $row['activityDetails']);
        $this->assertEquals('Test Remarks', $row['activityRemarks']);
        $this->assertEquals('1', $row['activitySem']);
        $this->assertEquals('1', $row['activityYear']);
        $this->assertEquals($sessionAccountID, $row['accountID']);

        $stmt->close();

        // Clean up session
        $this->cleanUpSession();
    }
}
