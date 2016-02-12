<?php
include(dirname(__FILE__).'/../../../config/config.inc.php');

$code       = Tools::getValue("code");
$state      = Tools::getValue("state");
$url = Context::getContext()->link->getModuleLink('idcut', 'authentication', array('code'=>$code, 'state'=>$state));

Tools::redirect($url);