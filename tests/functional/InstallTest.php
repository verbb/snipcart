<?php 
class InstallTest extends \Codeception\Test\Unit
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testPhpUnitIsWorking()
    {
        $this->assertEquals(1, 1);
    }

    public function testCraftHasDatabase()
    {
        $this->assertTrue(Craft::$app->getDb()->getIsActive());
    }

    public function testSnipcartIsInstalled()
    {
        $this->assertNotNull(Craft::$app->plugins->getPlugin('snipcart'));
    }
}