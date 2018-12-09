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

    public function testPhpUnitIsWorking(): void
    {
        $this->assertEquals(1, 1);
    }

    public function testCraftHasDatabase(): void
    {
        $this->assertTrue(Craft::$app->getDb()->getIsActive());
    }

    public function testSnipcartIsInstalled(): void
    {
        $this->assertNotNull(Craft::$app->plugins->getPlugin('snipcart'));
    }
}