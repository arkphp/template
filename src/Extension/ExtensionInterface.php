<?php
/**
 * ark.template
 * @copyright 2015 Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 */

namespace Ark\Template\Extension;

use Ark\Template\Engine;

interface ExtensionInterface {
    public function register(Engine $engine);
}