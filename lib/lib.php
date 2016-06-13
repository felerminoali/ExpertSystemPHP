<?php

include '/lib/xmlize.php';
include '/classes/xml_rules/model.php';

function get_xml_content($path) {

    if (!$file = file_get_contents($path)) {
//        error('Error while reading xml');
        return;
    }

//echo '<pre>' . str_replace('<', '&lt;', $file) . '</pre>';
//echo $file;

    $xml = xmlize($file, 1, 'UTF-8', true);

//    traverse_xmlize($xml, 'xml_');
//    print '<pre>' . implode("", $traverse_array) . '</pre>';

    if (!empty($xml['RULES']['#']['ALGORITHM'])) {

        $algorithms = $xml['RULES']['#']['ALGORITHM'];

        $algorithms_array = array();

        for ($i = 0; $i < sizeof($algorithms); $i++) {
            $algorithm = $algorithms[$i];

//            echo $algorithm['#']['TYPE'][0]['#'] . '<br>';
            $x_algorithm = new xml_algorithm();
            $x_algorithm->type = $algorithm['#']['TYPE'][0]['#'];
            $x_algorithm->accuracy = $algorithm['#']['ACCURACY'][0]['#'];
                    
            $ruless = $algorithm['#']['RULE'];

            $algorithm_rules_array = array();
            for ($j = 0; $j < sizeof($ruless); $j++) {

//                echo $ruless[$j]['#']['ID'][0]['#'] . '<br>';
//                echo 'ACCURACY ' . $ruless[$j]['#']['ACCURACY'][0]['#'] . '<br>';
                $x_ruless = new xml_rule();

                $x_ruless->id = $ruless[$j]['#']['ID'][0]['#'];
//                $x_ruless->accuracy = $ruless[$j]['#']['ACCURACY'][0]['#'];
                $x_ruless->action = $ruless[$j]['#']['CONCLUSION'][0]['#'];
                $x_ruless->interpretation = $ruless[$j]['#']['INTERPRETATION'][0]['#'];

                $conditions = $ruless[$j]['#']['CONDITIONS'][0]['#']['CONDITION'];

                $algorithm_rules_conditions = '';

                for ($k = 0; $k < sizeof($conditions); $k++) {

                    $condition = $conditions[$k];

                    $attribute = $condition['@']['ATTRIBUTE'];
                    $operator = $condition['@']['OPERATOR'];
                    $val = $condition['@']['VAL'];

//                    echo $attribute . '' . $operator . '' . $val;
                    $algorithm_rules_conditions .= $attribute . ' ' . $operator . ' ' . $val;

                    if ($k != (sizeof($conditions) - 1)) {
                        $algorithm_rules_conditions .=' AND ';
//                        echo ' AND ';
                    }
                }
                $x_ruless->conditions = $algorithm_rules_conditions;
//                echo '<br>';

                $algorithm_rules_array[] = $x_ruless;
            }

            $x_algorithm->rules = $algorithm_rules_array;

            $algorithms_array[] = $x_algorithm;
        }
    }else{
        return;
    }
    
    return $algorithms_array;
}
