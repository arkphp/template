<?php
/**
 * ark.template
 * @copyright 2015 Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 */

namespace Ark\Template\Extension;

use Ark\Template\Engine;

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