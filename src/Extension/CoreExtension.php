<?php
namespace ddliu\template\Extension;

use ddliu\template\Engine;

class CoreExtension implements ExtensionInterface {
    public function register(Engine $engine) {
        $engine->registerFunction('escape', [$this, 'escape']);
        $engine->registerFunction('e', [$this, 'escape']);
    }

    public function escape($v) {
        return htmlspecialchars($v);
    }
}