<?php

require_once __DIR__ . '/SharedReportTest/Bootstrap.php';

$path = realpath(__DIR__);

if ($path === false) {
    throw new RuntimeException('Unable to determine the real path of the current directory.');
}

chdir(dirname($path));

$bootstrap = SharedReportTest\Bootstrap::getInstance();

$bootstrap->init($path, array('SharedReportTest'));
