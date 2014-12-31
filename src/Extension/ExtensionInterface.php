<?php
namespace ddliu\template\Extension;

/**
 * template
 * @copyright 2014 Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 */

use ddliu\template\Engine;

interface ExtensionInterface {
    public function register(Engine $engine);
}