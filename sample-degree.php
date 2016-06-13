<form method="post">
Courses followed:<br/>
	<input type="checkbox" name="course[]" value="x1">Course x1<br/>
	<input type="checkbox" name="course[]" value="x2">Course x2<br/>
	<input type="checkbox" name="course[]" value="y1">Course y1<br/>
	<input type="checkbox" name="course[]" value="y2">Course y1<br/>
	
    <input type="submit" name="submit" value="Suggest degree field">
</form>
<?php



if (! isset($_POST['submit'])) {
	return;
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


$ie = new InferenceEngine("degree");

$wm = $ie->getWorkingMemory();

foreach ($_POST['course'] as $course) {
	$wm->setFact("external_course_followed_" . $course, true);
}


echo "<pre>";
print_r($wm);
echo "</pre>";


$ie->run();

echo "InferenceEngine executed.";

echo "<pre>";
print_r($wm);
echo "</pre>";


echo "Follow : ".$wm->getFact("follow");


db_close();
