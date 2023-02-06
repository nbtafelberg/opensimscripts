<?php
echo "FOrmat -rregionname\n";
$regionname =  $argv[1];


/**
 * Issue a command to any running screens
 */
ini_set("display_errors", 1);
ini_set("log_errors", 1);

error_reporting(E_ALL);
$command="quit";
echo "Restarting " . $regionname . "\n";
$torun = 'screen -S ' . $regionname . ' -p 0 -X stuff "' . $command . '^M"';
exec($torun);