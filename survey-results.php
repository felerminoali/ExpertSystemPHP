<form method="post" action="save-to-kb.php">
    <?php
    include '/lib/lib.php';
    

    if (!isset($_POST['submit_survey'])) {
        return;
    }

    $str_varx = $_POST['algorithmsx'];
    $passed_array_alg = unserialize(base64_decode($str_varx));

    $str_var = $_POST['rules'];
    $passed_rule_array = unserialize(base64_decode($str_var));

    $ranked_rules = array();
    foreach ($passed_rule_array as $rule) {
        $rule['rank'] = $_POST[$rule['type'] . '' . $rule['id']];
        $ranked_rules[] = $rule;
    }

    foreach ($ranked_rules as $rule) {
        echo 'type ' . $rule['type'] . ' ID ' . $rule['id'] . ' rank ' . $rule['rank'] . '<br>';
    }

    $algo_types = array();
    foreach ($passed_array_alg as $algorithm) {
        $algo_types[] = $algorithm->type;
    }

    $ranking = array(1, 2, 3, 4, 5);

    $frequences = array();
    for ($j = 0; $j < count($algo_types); $j++) {
        for ($i = 0; $i < count($ranking); $i++) {
            $frequences[$j][$i] = array('algorithm' => $algo_types[$j], 'freq' => computeFreq($algo_types[$j], $ranking[$i], $ranked_rules));
        }
    }

    echo count($frequences) . '<br>';
    echo $frequences[0][0]['algorithm'];
    echo '<table>';
    echo '<tr>';
    echo '<th>' . '' . '</th>';
    for ($i = 0; $i < count($ranking); $i++) {
        echo '<th>' . $ranking[$i] . '</th>';
    }

    for ($i = 0; $i < count($frequences); $i++) {
        echo '</tr>';
        echo '<tr>';
        echo '<td>' . 'rule ' . $frequences[$i][0]['algorithm'] . '</td>';
        for ($j = 0; $j < count($ranking); $j++) {
            echo '<td>' . $frequences[$i][$j]['freq'] . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';


    $comprehesibility = array();

    for ($i = 0; $i < count($algo_types); $i++) {
        $belowNormal = 0.0;
        $upNormal = 0.0;

        for ($k = 0; $k < count($ranking); $k++) {

            if ($k < round((count($ranking) / 2))) {
                $belowNormal -= $frequences[$i][$k]['freq'];
            }

            if ($k > round((count($ranking) / 2))) {
                $upNormal += $frequences[$i][$k]['freq'];
            }
        }
        $comprehesibility[] = array('algorithm' => $frequences[$i][0]['algorithm'], 'comprehesibility' => ($upNormal + $belowNormal));
    }

    echo count($comprehesibility);

    var_dump($comprehesibility);

    $highestRanking = reset($comprehesibility);


    foreach ($comprehesibility as $compr) {
        if ($highestRanking['comprehesibility'] < $compr['comprehesibility']) {
            $highestRanking = $compr;
        }
    }


    if (count($highestRanking) > 0) {


        echo $highestRanking['algorithm'] . '<br>';

        echo 'rules: ' . '<br>';

        echo "<TABLE BORDER>";
        echo "<TR ALIGN=CENTER>";
        echo "<TD WIDTH=25><B>Algorithm</B></TD>";
        echo "<TD WIDTH=25><B>Rules</B></TD>";
        echo "</TR>";

        echo '<TR ALIGN=CENTER>';
        echo '<TD  ALIGN=LEFT>' . $highestRanking['algorithm'] . '</TD>';
        $rules_display = '';
        foreach ($passed_rule_array as $rule) {

            if ($rule['type'] == $highestRanking['algorithm']) {
                $rules_display .= $rule['rule'] . '<br>';
            }
        }
        echo '<TD  ALIGN=LEFT>' . $rules_display . '</TD>';
        echo '</TR>';
        echo '</TABLE>';
    }
    
    $selected_algo = null;
    foreach($passed_array_alg as $algo){
        if($algo->type == $highestRanking['algorithm']){
            $selected_algo = $algo;
            break;
        }
    }
    
    echo '<input type="hidden" name="selected_algo" value="' . base64_encode(serialize($selected_algo)) . '"/>';
    ?>
    <br>
    
    <input type="text" name="ruleset">
    <input type="submit" name="save_to_kb" value="Save to KB">
</form>


    <?php

    function computeFreq($algorithm, $rank, $rules) {
        $count = 0;

        foreach ($rules as $rule) {
            if ($rule['type'] == $algorithm && $rule['rank'] == $rank) {
                $count++;
            }
        }

        return $count;
    }
    