<?php

namespace Kickass\Jash\Template;

use \Kickass\Jash\Template\TemplateInterface as TemplateInterface;

class Basic implements TemplateInterface
{

    private $vars = array();
    private $templateFile;

    public function __construct(){
        
    }

    public function __get($name)
    {
        return $this->vars[$name];
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function render()
    {
        extract($this->vars);
        ob_start();
        include($this->templateFile);
        return ob_get_clean();
    }

}
