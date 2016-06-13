<form method="post">
Fly: <select name="fly">
       <option>true</option>
       <option>false</option>
     </select>
     
Legs:<select name="legs">
       <option>2</option>
       <option>4</option>
     </select>

Size:<select name="size">
       <option>small</option>
       <option>large</option>
     </select>
     <input type="submit" name="submit" value="What's the sound?">
</form>
<?php



if (! isset($_POST['submit'])) {
	return;
}


if ($_POST['fly'] == "true") {
	$fly = true;
} else {
	$fly = false;
}

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


$ie = new InferenceEngine("animal");

$wm = $ie->getWorkingMemory();
$wm->setFact("fly", $fly);
$wm->setFact("size", $_POST['size']);
$wm->setFact("legs", $_POST['legs']);

echo "<pre>";
print_r($wm);
echo "</pre>";


$ie->run();

echo "InferenceEngine executed.";

echo "<pre>";
print_r($wm);
echo "</pre>";


echo "Sound is : ".$wm->getFact("sound");


db_close();
