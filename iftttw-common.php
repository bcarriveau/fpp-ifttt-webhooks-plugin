<?php
$skipJSsettings = true;
include_once "/opt/fpp/www/config.php";

$pluginName = basename(dirname(__FILE__));
$pluginPath = $settings['pluginDirectory'] . "/" . $pluginName . "/";

$logFile = $settings['logDirectory'] . "/" . $pluginName . "-execute.log";

// Manual JSON loading
global $pluginSettings;
$pluginConfigFile = $settings['configDirectory'] . "/plugin." . $pluginName . ".json";
$pluginSettings = array();
if (file_exists($pluginConfigFile)) {
    $pluginSettings = json_decode(file_get_contents($pluginConfigFile), true);
}

function getPayloadOptions() {
    $payloadArr = array(
        array(
            "id" => "0",
            "name" => "None",
            "path" => ""
        ),
        array(
            "id" => "1",
            "name" => "FPPD Status",
            "path" => "/api/fppd/status"
        ),
        array(
            "id" => "2",
            "name" => "System Status",
            "path" => "/api/system/status"
        )
    );
    return $payloadArr;
}

function logEntry($data) {
    global $logFile, $myPid;

    $data = $_SERVER['PHP_SELF'] . " : [" . $myPid . "] " . $data;

    $logWrite = fopen($logFile, "a") or die("Unable to open file!");
    fwrite($logWrite, date('Y-m-d h:i:s A', time()) . ": " . $data . "\n");
    fclose($logWrite);
}
?>