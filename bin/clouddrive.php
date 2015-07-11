#!/usr/local/bin/php
<?php
/**
 * Configuration for: Timezone
 */
date_default_timezone_set('America/New_York');

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

define('CLI_ROOT', realpath(dirname(__FILE__) . '/../.cache') . '/');

$app = new \Cilex\Application('CloudDrive', '0.1.0');

$app->command(new \CloudDrive\Commands\MetadataCommand());
$app->command(new \CloudDrive\Commands\InitCommand());
$app->command(new \CloudDrive\Commands\SyncCommand());
$app->command(new \CloudDrive\Commands\ClearCacheCommand());
$app->command(new \CloudDrive\Commands\UploadCommand());
$app->command(new \CloudDrive\Commands\ListCommand());

$app->run();