<?php
// Turn off display errors
//ini_set("display_errors", "Off");
ini_set("error_reporting", "E_ALL & ~E_NOTICE");

// Set the include path
$includepath = ini_get("include_path");
ini_set("include_path", $includepath . PATH_SEPARATOR . "classes/phpexpertsystem");

// Let the class files be located correctly
function __autoload($class_name) {
    include $class_name . '.php';
}


include "config.php";
include "util/mysql.php";

db_connect();


$ie = new InferenceEngine("test");

$wm = $ie->getWorkingMemory();
$wm->setFact("x", 10);

echo "<pre>";
print_r($wm);
echo "</pre>";


$ie->run();

echo "InferenceEngine executed.";

echo "<pre>";
print_r($wm);
echo "</pre>";



db_close();

?>
