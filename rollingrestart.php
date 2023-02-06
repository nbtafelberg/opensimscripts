<?php

ob_start();

/**
 * Issue a command to any running screens
 */
ini_set("display_errors", 1);
ini_set("log_errors", 1);

error_reporting(E_ALL);
$command="quit";

include ("commandtoregions.php");

docommand($command);