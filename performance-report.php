<form method="post">

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

    $dao = new MySQLRuleDAO();

    $ruleSet = $dao->findRuleSets();

    if (count($ruleSet) > 0) {
        echo 'Ruleset: <select name="ruleset">';
        foreach ($ruleSet as $rule) {
            echo '<option>' . $rule['ruleset'] . '</option>';
        }
        echo '</select>';
    }
    ?>
    <br>
    Number of Assignment taken: <select name="nAssgnTaken">
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
    </select>
    <br>
    Number of Quiz taken: <select name="nQuizTaken">
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
    </select>
    
    <br>
    <input type="submit" name="submit" value="predict">
</form>
<?php
if (!isset($_POST['submit'])) {
    return;
}

$ruleSet = $_POST['ruleset'];
$nAssignTaken = $_POST['nAssgnTaken'];
$nQuizTaken = $_POST['nQuizTaken'];

echo $ruleSet.'<br>';
$ie = new InferenceEngine($ruleSet);


$wm = $ie->getWorkingMemory();
$wm->setFact("N_assign_taken", $nAssignTaken);
$wm->setFact("N_quiz_taken", $nQuizTaken);

echo "<pre>";
print_r($wm);
echo "</pre>";


$ie->run();

echo "InferenceEngine executed.";

echo "<pre>";
print_r($wm);
echo "</pre>";

echo "The student is predicted to : " . $wm->getFact("finalgrade");

db_close();
