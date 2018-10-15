<?php

$yi = dirname(__FILE__).'/../YI/Yi.class.php';
$config = dirname(__FILE__).'/config/config.php';

require_once($yi);
yi::createApplication('App',$config)->run();