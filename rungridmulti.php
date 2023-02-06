<?php

ob_start();
// ** Find theget next available port on theserver
ini_set("display_errors", 1);
ini_set("log_errors", 1);

error_reporting(E_ALL);

include("config.php");
include("database.php");
#


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

function getUUID() {
    $name_space = "5f6384bfec4ca0b2d4114a13aa2a5435";
    $string = date("hisa");
    $n_hex = str_replace(array('-', '{', '}'), '', $name_space); // Getting hexadecimal components of namespace
    $binary_str = ''; // Binary Value
    //Namespace UUID to bits conversion
    for ($i = 0; $i < strlen($n_hex); $i += 2) {
        $binary_str .= chr(hexdec($n_hex[$i] . $n_hex[$i + 1]));
    }
    //hash value
    $hashing = md5($binary_str . $string);

    return sprintf('%08s-%04s-%04x-%04x-%12s',
            // 32 bits for the time low
            substr($hashing, 0, 8),
            // 16 bits for the time mid
            substr($hashing, 8, 4),
            // 16 bits for the time hi,
            (hexdec(substr($hashing, 12, 4)) & 0x0fff) | 0x3000,
            // 8 bits and 16 bits for the clk_seq_hi_res,
            // 8 bits for the clk_seq_low,
            (hexdec(substr($hashing, 16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for the node
            substr($hashing, 20, 12)
    );
}

function custom_copy($src, $dst) {

    // open the source directory
    $dir = opendir($src);

    // Make the destination directory if not exist
    @mkdir($dst);

    // Loop through the files in source directory
    foreach (scandir($src) as $file) {

        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {

                // Recursively calling custom copy function
                // for sub directory 
                custom_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }

    closedir($dir);
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

dolog("Removing from regions in robust");
dolog("Selecting database for server " . $thisservername);
$con->query("delete from robust.regions where serverIP='" . $thisservername . "'");
$sql = "SELECT * FROM grid.regions where servername='" . $thisservername . "' and databasename='westocean'";
$result = $con->query($sql);
$shortname = "master";
$regionpath="master";
$serverport = "25000";
if ($result->num_rows > 0) {
    // output data of each row
    initMaster();

    while ($row = $result->fetch_assoc()) {
        // got data lets start a server!
        dolog("Starting Region " . $row["regionname"]);
        goRegion($row["xpos"], $row["ypos"], $row["regionname"], $row["estatename"], $row["shortname"], $row["owner"], $row["uuid"], $row["params"], $row["nocopy"]
                ,substr($row["owner"],0,strpos($row["owner"]," "))
                ,substr($row["owner"],strpos($row["owner"]," ")+1)
                
                );
        ++$serverport;
    }

    startServer();
} else {
    echo "0 results";
}
$con->close();

function initMaster() {
    Global  $regionpath,$shortname, $serverport,$defaultregionfolder,$con;
    //   $todo = "cp -r " . $defaultregionfolder . "bin/ " . $regionpath;
    //echo $todo;cp 
    dolog("Creating Master Folder " . $regionpath);
    $todo = 'mkdir /home/wolfgrid1/master';
    shell_exec($todo);
    dolog($todo);
     
    $todo = 'mkdir /home/wolfgrid1/master/bin';]
    shell_exec($todo);
    dolog($todo);
     
    $todo = 'cp -r ' . $defaultregionfolder . '* /home/wolfgrid1/regions/' . $regionpath . "/";
    dolog($todo);
    shell_exec($todo);
    $regionpath="/home/wolfgrid1/regions/master/";
  //  $con->query("update grid.regions set port='" . $serverport . "' where uuid='" . $regionuuid . "'");
        file_put_contents($regionpath . "bin/Regions/Region.ini", $inifile);
    dolog("Update OpenSim.ini - Server Port " . $serverport);
    $opensim = file_get_contents($regionpath . "/bin/OpenSim.ini"); //< opensim.ini loaded here
    $opensim = str_replace("ESTATENAME", "Wolf Grid", $opensim);
    $opensim = str_replace("ESTATEOWNER", "Lone Wolf", $opensim);
    $opensim = str_replace("8888", $serverport, $opensim);
    $opensim = str_replace("INSERTMESHERHERE", $mesher, $opensim);
    $opensim = str_replace("INSERTPHYSICSHERE", $physicsengine, $opensim);

    file_put_contents($regionpath . "bin/OpenSim.ini", $opensim);
    /**
     * 
     * 
     * Edit Grid Commono.ini
     * 
     */
    dolog("Edit GridCommon.ini");
    $gridoptions = file_get_contents($regionpath . "bin/config-include/GridCommon.ini");
    $gridoptions = str_replace("DBSCHEMA", "westocean", $gridoptions);
    $gridoptions = str_replace("DBUSER", "root", $gridoptions);
    $gridoptions = str_replace("DBPASSWD", "uV6LTchS", $gridoptions);
    $gridoptions = str_replace("localhost", "grid.wolfterritories.org", $gridoptions);
    file_put_contents($regionpath . "bin/config-include/GridCommon.ini", $gridoptions);

    dolog("Create Execution Script");
    $execute = '
#!/bin/bash

while true
do
    rm -r ' . $regionpath . 'bin/assetcache
    mkdir ' . $regionpath . 'bin/assetcache
    cd ' . $regionpath . 'bin
    '  . ' 
 #   rsync -av --exclude=\'*.ini\' ' . $defaultregionfolder . 'bin/ ./
    mono --server --optimize=all OpenSim.exe
    echo
    echo
    echo "Restarting 30 seconds. Press [CTRL+C] to stop.."
    sleep 30
    echo "Restarting Region NOW."
    sleep 5
done
';
    dolog("Write " . $regionpath . "bin/execute.sh");
    file_put_contents($regionpath . "bin/execute.sh", $execute);

    dolog("Create Database on main server");
    mysqli_query($con, "create database if not exists " . $shortname);
}

function startServer() {
    
    Global $regionpath, $shortname,$con;
    dolog("screen -dmS " . $shortname . " sh " . $regionpath . "bin/execute.sh");

    exec("screen -dmS " . $shortname . " sh " . $regionpath . "bin/execute.sh");
    // now wait for 10 seconds
}

/** Create REgion * */
function goRegion($xpos, $ypos, $regionname, $estatename, $rname, $estateowner, $regionuuid, $params, $nocopy,$firstname,$lastname) {
    global $databasehost, $serverport, $thisservername, $databaseusername, $databasepassword, $regionsfolder, $con,$shortname, $defaultregionfolder;
    $physicsengine = "ubODE";
    $mesher = "ubODEMeshmerizer";

    /** create folder
     * 
     */
    $rname = str_replace(" ", "", $rname);
    /*
     *  Copy Files
     * Edit OPENSIM .ini
     */
    $regionpath = $regionsfolder . $shortname . "/";

    dolog("Create Region File");
    /*
     * 
     * 
     *  Create REGION INI FILE
     */

    $inifile = "[" . $regionname . "]" . PHP_EOL;
    $inifile .= "RegionUUID = " . $regionuuid . "" . PHP_EOL;
    $inifile .= "Location = " . $xpos . "," . $ypos . "" . PHP_EOL;
    $inifile .= "ExternalHostName = " . $thisservername . "" . PHP_EOL;
    $inifile .= "InternalAddress = 0.0.0.0" . PHP_EOL;
    $inifile .= "InternalPort = " . $serverport . "" . PHP_EOL;
    $inifile .= "AllowAlternatePorts = False" . PHP_EOL;
    $inifile .= "SizeX = 1024" . PHP_EOL;
    $inifile .= "SizeY = 1024" . PHP_EOL;
    $inifile .= "MaxPrims = 80000" . PHP_EOL;
    $inifile .= "MaxAgents = 60" . PHP_EOL;
    $inifile .= "MaxPrimsPerUser = -1" . PHP_EOL;
    $inifile .= "MasterAvatarFirstName = " . $firstname . PHP_EOL;
    $inifile .= "MasterAvatarLastName =" . $lastname .  PHP_EOL;
    $inifile .= "EstateName =" . $estatename .  PHP_EOL;
    
    file_put_contents($regionpath . "bin/Regions/Region.ini", $inifile,FILE_APPEND);

    ob_flush();
}
