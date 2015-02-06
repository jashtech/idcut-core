<?php

namespace Kickass\Jash\Template;

interface TemplateInterface
{

    public function render();

    public function setTemplateDir($templateDir);

    public function setTemplateFile($templateFile);
}
