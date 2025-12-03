<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/WP_Backend_Helper.php';

use PHPUnit\Framework\TestCase;

class Backend_UnitTest extends TestCase
{
    private WP_Backend_Helper $helper;

    protected function setUp(): void
    {
        $this->helper = new WP_Backend_Helper();
    }

    public function testSanitizeRemovesHtml()       { $this->assertSame('Hello', $this->helper->sanitizePostTitle('<b>Hello</b>')); }
    public function testSanitizeTruncates()         { $this->assertSame(200, strlen($this->helper->sanitizePostTitle(str_repeat('a', 300)))); }
    public function testValidTitle()                { $this->assertTrue($this->helper->isValidPostTitle('Good Title')); }
    public function testInvalidEmptyTitle()         { $this->assertFalse($this->helper->isValidPostTitle('')); }
    public function testInvalidTooLongTitle()       { $this->assertFalse($this->helper->isValidPostTitle(str_repeat('x', 201))); }
    public function testSlugGeneration()            { $this->assertSame('hello-world', $this->helper->generateSlug(' Hello @ World!!! ')); }
    public function testValidEmail()                { $this->assertTrue($this->helper->isValidEmail('test@wordpress.org')); }
    public function testInvalidEmail()              { $this->assertFalse($this->helper->isValidEmail('bad-email')); }
    public function testStrongPassword()            { $this->assertTrue($this->helper->isStrongPassword('MyPass123')); }
    public function testWeakPassword()              { $this->assertFalse($this->helper->isStrongPassword('123')); }
    public function testExcerptLongContent()        { $this->assertStringEndsWith('...', $this->helper->generateExcerpt(str_repeat('word ', 100))); }
    public function testExcerptShortContent()       { $this->assertSame('Short text', $this->helper->generateExcerpt('Short text')); }
    public function testValidPostStatus()           { $this->assertTrue($this->helper->isValidPostStatus('publish')); }
    public function testInvalidPostStatus()         { $this->assertFalse($this->helper->isValidPostStatus('random')); }
}
