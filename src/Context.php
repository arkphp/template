<?php
/**
 * ark.template
 * @copyright 2015 Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 */

namespace Ark\Template;

class Context {
    /**
     * Template variables
     */
    private $variables = array();

    private $blockStack = array();

    private $inherits = array();

    private $currentInheritLevel = 0;

    private $blocks = array();

    private $rendered = false;

    /**
     * Assign view variable
     * @param string|array $key
     * @param mixed $value
     */
    public function assign($key, $value = null){
        if(is_array($key)){
            foreach($key as $k => $v){
                $this->variables[$k] = $v;
            }
        }
        else{
            $this->variables[$key] = $value;
        }
    }

    public function getVar($key, $default = null){
        if(isset($this->variables[$key])){
            return $this->variables[$key];
        }
        $parts = explode('.', $key);
        $parent = $this->variables;
        foreach($parts as $part){
            if(!isset($parent[$part])){
                return $default;
            }
            $parent = $parent[$part];
        }
        return $parent;
    }

    public function hasVar($key){
        return isset($this->variables[$key]);
    }

    public function getVariables(){
        return $this->variables;
    }

    public function setRendered($rendered){
        $this->rendered = $rendered;
    }

    public function isRendered(){
        return $this->rendered;
    }

    public function extend($parent){
        ob_start();
        $this->inherits[] = $parent;
        //$this->currentInheritLevel++;
    }

    public function hasBlocks(){
        return isset($this->blockStack[0]);
    }

    public function hasBlock($blockname){
        return isset($this->blocks[$blockname]);
    }

    public function addBlock($blockname){
        $this->blockStack[] = $blockname;
    }

    public function setBlock($blockname, $value){
        $this->blocks[$blockname] = $value;
    }

    public function popBlock(){
        return array_pop($this->blockStack);
    }

    public function getBlock($name){
        return isset($this->blocks[$name])?$this->blocks[$name]:'';
    }

    public function isTopLevel(){
        return $this->currentInheritLevel >= count($this->inherits);
    }

    public function addInherit($parent){
        $this->inherits[] = $parent;
    }

    public function hasInherits(){
        return isset($this->inherits[0]);
    }

    public function incCurrentInheritLevel(){
        $this->currentInheritLevel++;
    }

    public function getCurrentInheritLevel(){
        return $this->currentInheritLevel;
    }

    public function getCurrentInherit(){
        return $this->inherits[$this->currentInheritLevel];
    }
}