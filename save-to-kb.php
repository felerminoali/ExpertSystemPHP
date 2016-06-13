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

include "lib/lib.php";
include "config.php";
include "util/mysql.php";

if (!isset($_POST['save_to_kb'])) {
    echo 'Post not accomplished';
    return;
}

$str_var = $_POST['selected_algo'];
$passed_alg = unserialize(base64_decode($str_var));

$ruleSet = $_POST['ruleset'];

//echo 'RuleSet: '.$ruleSet;

//var_dump($passed_alg);

if(!count($passed_alg)>0){
    echo 'No selected algorithm';
    return;
}


db_connect();


$dao = new MySQLRuleDAO();

//$status = $dao->insert_rule('student_performance_prediction', '$N_quiz_taken > 3', '$finalgrade = PASS;');

//echo 'Passed algorith'.$passed_alg->type;

$status = $dao->insert_rules($passed_alg, $ruleSet);

if ($status) {
    echo 'Sucessfully insert new rules';
} else {
    echo 'Unsuccessfull';
}

db_close();
