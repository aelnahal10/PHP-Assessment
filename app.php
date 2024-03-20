#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Command\QueryCommand;
use App\Command\SummaryCommand;

$app = new Application();

// Register commands
$app->add(new QueryCommand());
$app->add(new SummaryCommand());

$app->run();
