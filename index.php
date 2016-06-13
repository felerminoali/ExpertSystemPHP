

<html>
    <head>
        <title>phpexpertsystem</title>
    </head>
    <body>
        <h3>phpexpertsystem</h3>

        <p>Samples:</p>
        <ol>
            <li><a href="sample-basic.php">sample-basic.php</a></li>
            <li><a href="sample-animal.php">sample-animal.php</a></li>
            <li><a href="sample-degree.php">sample-degree.php</a></li>
            <li><a href="sample-degree2.php">sample-degree2.php</a></li>
        </ol>

    </body>
</html>

<?php
include '/lib/xmlize.php';
include '/classes/xml_rules/model.php';

$path = 'rules.xml';
if (!$file = file_get_contents($path)) {
    error('Error while reading xml');
}

//echo '<pre>' . str_replace('<', '&lt;', $file) . '</pre>';
//echo $file;

$xml = xmlize($file, 1, 'UTF-8', true);

traverse_xmlize($xml, 'xml_');
print '<pre>' . implode("", $traverse_array) . '</pre>';

if (!empty($xml['RULES']['#']['ALGORITHM'])) {

    $algorithms = $xml['RULES']['#']['ALGORITHM'];

    $algorithms_array = array();

    for ($i = 0; $i < sizeof($algorithms); $i++) {
        $algorithm = $algorithms[$i];

        echo $algorithm['#']['TYPE'][0]['#'] . '<br>';
        $x_algorithm = new xml_algorithm();
        $x_algorithm->type = $algorithm['#']['TYPE'][0]['#'];

        $ruless = $algorithm['#']['RULE'];

        $algorithm_rules_array = array();
        for ($j = 0; $j < sizeof($ruless); $j++) {

            echo $ruless[$j]['#']['ID'][0]['#'] . '<br>';
            echo 'ACCURACY '.$ruless[$j]['#']['ACCURACY'][0]['#'] . '<br>';
            $x_ruless = new xml_rule();

            $x_ruless->id = $ruless[$j]['#']['ID'][0]['#'];
            $x_ruless->accuracy = $ruless[$j]['#']['ACCURACY'][0]['#'];
            $x_ruless->action = $ruless[$j]['#']['CONCLUSION'][0]['#'];
            $x_ruless->interpretation = $ruless[$j]['#']['INTERPRETATION'][0]['#'];

            $conditions = $ruless[$j]['#']['CONDITIONS'][0]['#']['CONDITION'];

            $algorithm_rules_conditions = '';

            for ($k = 0; $k < sizeof($conditions); $k++) {

                $condition = $conditions[$k];

                $attribute = $condition['@']['ATTRIBUTE'];
                $operator = $condition['@']['OPERATOR'];
                $val = $condition['@']['VAL'];

                echo $attribute . '' . $operator . '' . $val;
                $algorithm_rules_conditions .= $attribute . '' . $operator . '' . $val;

                if ($k != (sizeof($conditions) - 1)) {
                    $algorithm_rules_conditions .=' AND ';
                    echo ' AND ';
                }
            }
            $x_ruless->conditions = $algorithm_rules_conditions;
//            echo '<br>' . $algorithm_rules_conditions . '<br>';
            echo '<br>';

            $algorithm_rules_array[] = $x_ruless;
        }

        $x_algorithm->rules = $algorithm_rules_array;

        $algorithms_array[] = $x_algorithm;
    }
}


echo 'Numeros de algorithm: ' . count($algorithms_array);

$table = array();


$rules = array();

foreach ($algorithms_array as $algorithm) {

    foreach ($algorithm->rules as $rule) {
        $rules[] = array('type' => $algorithm->type, 'id'=>$rule->id);
    }
}

echo 'rules ID';
foreach ($rules as $rule) {
    echo $rule['id'];
}

$ranking = array(1, 2, 3, 4, 5);
$frequences = array();

$totalRules = 5;

for ($x = 0; $x <= $totalRules; $x++) {
    $table[] = array("rule" => rand(1, 3), "rank" => rand(1, 5));
}

//var_dump($table);

echo '<table>';
echo '<tr>';
echo '<th>' . 'Rule' . '</th>';
echo '<th>' . 'Rank' . '</th>';
foreach ($table as $row) {
    echo '</tr>';
    echo '<tr>';
    echo '<td>' . $row['rule'] . '</td>';
    echo '<td>' . $row['rank'] . '</td>';
    echo '</tr>';
}
echo '</table>';

for ($i = 0; $i < count($rules); $i++) {
    for ($j = 0; $j < count($ranking); $j++) {
        $frequences[$i][$j] = computeFreq($rules[$i], $ranking[$j], $table);
    }
}

//var_dump($freq);

echo '<table>';
echo '<tr>';
echo '<th>' . '' . '</th>';
for ($i = 0; $i < count($ranking); $i++) {
    echo '<th>' . ($i + 1) . '</th>';
}

for ($i = 0; $i < count($frequences); $i++) {
    echo '</tr>';
    echo '<tr>';
    echo '<td>' . 'rule' . ($i + 1) . '</td>';

    for ($j = 0; $j < count($ranking); $j++) {
        echo '<td>' . $frequences[$i][$j] . '</td>';
    }
    echo '</tr>';
}
echo '</table>';

$comprehesibility = array();

for ($i = 0; $i < count($rules); $i++) {
    $belowNormal = 0.0;
    $upNormal = 0.0;

    for ($k = 0; $k < count($ranking); $k++) {


        if ($k < round((count($frequences) / 2))) {
            $belowNormal -= $frequences[$i][$k];
        }

//        if ($k == round((count($freq) / 2))) {
//            $belowNormal -= $freq[$i][$k] / 2.0;
//            $upNormal += $freq[$i][$k] / 2.0;
//        }

        if ($k > round((count($frequences) / 2))) {
            $upNormal += $frequences[$i][$k];
        }
    }
    $comprehesibility[] = array('rule' => ($i + 1), 'comprehesibility' => ($upNormal + $belowNormal));
}

var_dump($comprehesibility);

$highestRanking = reset($comprehesibility);


foreach ($comprehesibility as $compr) {
    if ($highestRanking['comprehesibility'] < $compr['comprehesibility']) {
        $highestRanking = $compr;
    }
}

echo 'the most comprehensible rule is: ' . $highestRanking['rule'] . '<br>';

function computeFreq($rule, $rank, $table) {
    $count = 0;

    for ($i = 0; $i < count($table); $i++) {
        if ($table[$i]['rule'] == $rule && $table[$i]['rank'] == $rank) {
            $count++;
        }
    }
    return $count;
}
?>


