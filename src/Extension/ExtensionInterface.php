<?php
namespace ddliu\template\Extension;

use ddliu\template\Engine;

interface ExtensionInterface {
    public function register(Engine $engine);
}