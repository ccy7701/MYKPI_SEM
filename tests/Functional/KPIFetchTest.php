<?php

namespace Tests;

class KPIFetchTest extends BaseTest
{
    /**
     * @runInSeparateProcess
     * Test fetching all the user's KPI 'indicator's
     */
    public function testFetchIndicators()
    {
        // First create a dummy account
        // Indicators are created on registration
        $this->createDummyAccount(
            'BI21110035',
            'fetch_indicators@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );

        // Log the dummy account in
        $loginResult = $this->attemptLogin('BI21110035', self::STANDARD_PASSWORD);

        // Assert successful login
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $sessionAccountID = $loginResult['session']["UID"];

        // Execute queries to fetch indicators by all, by year and by type
        $allIndicators = $this->fetchIndicators($sessionAccountID);
        $specificIndicator = $this->fetchIndicators($sessionAccountID, 1, 1);

        // Assert the number of rows returned by type of indicator
        $this->assertEquals(8, $allIndicators->num_rows);   // 8 total indicators
        $this->assertEquals(1, $specificIndicator->num_rows);    // 1 indicator where it belongs to Sem 1 Year 1

        // Clean up session
        $this->cleanUpSession();
    }
}