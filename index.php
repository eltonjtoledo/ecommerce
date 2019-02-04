<?php

session_start();
require_once("vendor/autoload.php");

require "functions.php";

use Slim\Slim;

$app = new Slim;

$app->config('debug', true);

require_once('roots/site.root.php');
require_once('roots/user.root.php');
require_once('roots/category.root.php');
require_once('roots/product.root.php');


$app->run();
?>