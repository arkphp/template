<?php
namespace ddliu\template\Extension;

use ddliu\template\Engine;

/**
 * template
 * @copyright 2014 Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 */

class CoreExtension implements ExtensionInterface {
    protected $engine;
    public function register(Engine $engine) {
        $this->engine = $engine;
        $engine->registerFunction('escape', [$this, 'escape']);
        $engine->registerFunction('e', [$this, 'escape']);
    }

    public function escape($v, $filters = null) {
        if (!$filters) {
            return htmlspecialchars($v);
        } else {
            return $this->engine->filter($v, $filters.'|e');
        }
    }
}