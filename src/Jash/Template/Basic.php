<?php

namespace Kickass\Jash\Template;

use \Kickass\Jash\Template\TemplateInterface as TemplateInterface;

class Basic implements TemplateInterface
{

    private $vars = array();
    private $templateFile;
    private $templateDir = '';

    public function __construct($templteFile)
    {
        $this->templateFile = $templteFile;
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

        if (file_exists($this->templateFile)) {
            extract($this->vars);
            ob_start();
            include($this->templateFile);
            return ob_get_clean();
        } else {
            throw new Exception("Can't find template file: ".$this->templateFile);
        }
    }

}
