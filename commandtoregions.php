<?php


function dolog($message) {
    $message = date("H:i:s") . " - $message" . PHP_EOL;
    print($message);
    flush();
    ob_flush();
}

function fakeget($param) {
    global $arguments;
    for ($i = 0; $i < count($arguments); ++$i) {
        $key = explode("=", $arguments[$i])[0];
        $value = explode("=", $arguments[$i])[1];
        if ($key === $param) {
            return $value;
        }
    }
    return "";
}

function getter($param) {
    $test = fakeget($param) . "";
    if ($test !== "") {
        dolog($param . "=" . $test);
        return $test;
    }
    return readline($param . ":");
    // return filter_input(INPUT_POST, $param);
}

function docommand($cmd) {
$screens = shell_exec("screen -r");
$screenlist = explode(PHP_EOL, $screens);
foreach ($screenlist as $regionname) {
    $regionname=$regionname . "\t";
    dolog(explode("\t",$regionname)[1]);
        $shortname = explode("\t", $regionname)[1];
        if($shortname!=="" && strpos($shortname,"robust")===false) {
        dolog($cmd . " .. " . $regionname);
        $torun = 'screen -S ' . $shortname . ' -p 0 -X stuff "' . $cmd . '^M"';
        dolog($torun);
        exec($torun);
	sleep(50);
        } else{
            echo "Cannot restart robust";
        }
    
}

}
