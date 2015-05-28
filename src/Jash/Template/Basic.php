<?php

namespace IDcut\Jash\Template;

use \IDcut\Jash\Template\TemplateInterface as TemplateInterface;

class Basic implements TemplateInterface
{

    private $vars = array();
    private $templateFile;
    private $templateDir = '';

    public function __construct()
    {

    }

    public function __get($name)
    {
        return $this->vars[$name];
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    public function setTemplateDir($templateDir){
        if(!file_exists($templateDir)){
            mkdir($templateDir, 0777, true);
        }
        $this->templateDir = $templateDir;
    }

    private function templatePath(){
        return $this->templateDir.DIRECTORY_SEPARATOR.$this->templateFile;
    }

    public function render()
    {

        if (file_exists($this->templatePath())) {
            extract($this->vars);
            ob_start();
            include($this->templatePath());
            return ob_get_clean();
        } else {
            throw new \Exception("Can't find template file: " . $this->templatePath());
        }
    }

}
