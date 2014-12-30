<?php
use ddliu\template\Engine;

class CommonTest extends PHPUnit_Framework_TestCase {
    protected function getEngine()
    {
        return new Engine([
            'root' => dirname(__FILE__).'/templates'
        ]);
    }

    public function testSingle()
    {
        $level = ob_get_level();
        $text = $this->getEngine()->render('layout.html.php', null, true);
        $this->assertRegExp('/#layout_header\s*#layout_content\s*#layout_footer/', $text);
        $this->assertEquals(ob_get_level(), $level);
    }

    public function testInherit()
    {
        $level = ob_get_level();
        $text = $this->getEngine()->render('index.html.php', null, true);
        $this->assertRegExp('/#layout_header\s*#index_content\s*#layout_footer/', $text);
        $this->assertEquals(ob_get_level(), $level);
    }

    public function testAssign()
    {
        $view = $this->getEngine();

        // assign global
        $view->assignGlobal('name', 'global');
        $text = $view->render('assign.html.php', null, true);
        $this->assertEquals($text, 'Hello global');

        $text = $view->render('assign.html.php', array('name' => 'World'), true);
        $this->assertEquals($text, 'Hello World');
    }
}