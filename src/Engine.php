<?php
/**
 * ark.template
 * @copyright 2015 Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 */

namespace Ark\Template;

use Ark\Template\Extension\CoreExtension;
use Ark\Template\Extension\ExtensionInterface;

class Engine {
    private $functions = array();

    /**
     * One context for one render
     * @var array
     */
    private $contexts = array();

    /**
     * Global variables
     * @var array
     */
    private $variables = array();

    protected $options = array(
        //'locator' => callable, //how to locate view file
        //'extract' => true, //extract variables
    );

    public function __construct($options = null){
        if(null === $options){
            $options = array();
        }
        if (is_string($options)) {
            $options = [
                'root' => $options
            ];
        }
        $this->options = array_merge($options, array(
            'extract' => true,
        ));
        //register buildin extensions
        $this->registerExtension(new CoreExtension());
        $this->startContext();
    }

    private function startContext(){
        $this->contexts[] = new Context();
    }

    private function endContext(){
        array_pop($this->contexts);
        if(!isset($this->contexts[0])){
            $this->startContext();
        }
    }

    private function getCurrentContext(){
        return end($this->contexts);
    }
    
    /**
     * Get path of view file
     * @param string $name view name
     * @return string
     */
    public function getViewFile($name){
        if(isset($this->options['locator'])){
            if($path = call_user_func($this->options['locator'], $name)){
                return $path;
            }
        }
        $path = '';
        if(isset($this->options['root'])){
            $path.=$this->options['root'].'/';
        }
        
        $path.=$name;
        return $path;
    }

    /**
     * Assign view variable
     * @param string|array $key
     * @param mixed $value
     */
    public function assign($key, $value = null){
        $this->getCurrentContext()->assign($key, $value);
    }

    /**
     * Assign global variables(can be used in different template)
     * @param  mixed $key
     * @param  mixed $value
     */
    public function assignGlobal($key, $value = null)
    {
        if(is_array($key)){
            foreach($key as $k => $v){
                $this->variables[$k] = $v;
            }
        }
        else{
            $this->variables[$key] = $value;
        }
    }

    public function getVariables(){
        return $this->getCurrentContext()->getVariables();
    }

    public function getVar($key, $default = null){
        return $this->getCurrentContext()->getVar($key, isset($this->variables[$key])?$this->variables[$key]:$default);
    }

    public function hasVar($key){
        return $this->getCurrentContext()->hasVar($key) || isset($this->variables[$key]);
    }
    
    public function extend($parent){
        $this->getCurrentContext()->addInherit($parent);
        ob_start();
    }

    public function block($blockname){
        $this->begin($blockname);
        $this->end();
    }

    public function begin($blockname){
        $this->getCurrentContext()->addBlock($blockname);
        ob_start();
    }

    public function getBlock($name){
        return $this->getCurrentContext()->getBlock($name);
    }

    public function end(){
        $currentContext = $this->getCurrentContext();
        if(!$currentContext->hasBlocks()){
            throw new LogicException('Block does not match');
        }
        $blockname = $currentContext->popBlock();
        $content = ob_get_contents();
        ob_end_clean();
        if(!$currentContext->hasBlock($blockname)){
            $currentContext->setBlock($blockname, $content);
        }
        //print block if it's top level OR it's a sub block
        if($currentContext->isTopLevel() || $currentContext->hasBlocks()){
            echo $currentContext->getBlock($blockname);
        }
    }

    public function registerExtension(ExtensionInterface $extension) {
        $extension->register($this);
    }

    public function registerFunction($name, $func) {
        $this->functions[$name] = $func;
    }

    public function filter($value, $filters) {
        $filters = explode('|', $filters);
        foreach ($filters as $filter) {
            if (isset($this->functions[$filter])) {
                $filter = $this->functions[$filter];
            }
            $value = call_user_func($filter, $value);
        }

        return $value;
    }

    public function __call($func, $arguments){
        if (isset($this->functions[$func])) {
            return call_user_func_array($this->functions[$func], $arguments);
        } else {
            throw new Exception("Function not found: ".$func);
        }
    }

    public function clear(){
        while(ob_get_level()){
            ob_end_clean();
            //ob_end_flush();
        }
    }

    public function render($_name, $_variables = array(), $_return = false){
        if($this->getCurrentContext()->isRendered()){
            $this->startContext();
        }
        $this->getCurrentContext()->setRendered(true);

        //$this->resetView();
        if($_return){
            ob_start();
        }

        $this->assign($_variables);

        if($this->options['extract']){
            extract($this->getCurrentContext()->getVariables(), EXTR_SKIP);
            if ($this->variables) {
                extract($this->variables, EXTR_SKIP);
            }
        }
        require($this->getViewFile($_name));
        if($this->getCurrentContext()->hasBlocks()){
            throw new \LogicException('Block does not match');
        }
        $this->renderInherits();

        $this->endContext();
        if($_return){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }

    /**
     * Render parents
     */
    protected function renderInherits(){
        $currentContext = $this->getCurrentContext();

        //has parents
        if($currentContext->hasInherits()){
            //not top most parent
            if(!$currentContext->isTopLevel()){
                ob_end_clean();
                //get parent view name
                $_viewName = $currentContext->getCurrentInherit();

                $currentContext->incCurrentInheritLevel();

                if($this->options['extract']){
                    extract($currentContext->getVariables(), EXTR_SKIP);
                    if ($this->variables) {
                        extract($this->variables, EXTR_SKIP);
                    }
                }

                // render the parent view file
                require($this->getViewFile($_viewName));

                //block match check
                if($currentContext->hasBlocks()){
                    throw new \LogicException('Block does not match');
                }
                
                //render parents
                $this->renderInherits();
            }
            else{
                /*
                if(ob_get_level()){
                    ob_end_flush();
                }*/
            }
        }
    }
}