 

<form method="post" action="survey-results.php">
    <?php
    include '/lib/lib.php';

    if (!isset($_POST['submit'])) {
        return;
    }

    $str_var = $_POST['algorithms'];
    $passed_array = unserialize(base64_decode($str_var));

//    var_dump($passed_array);
//    echo 'size: '.count($passed_array);

    $rules = array();
    foreach ($passed_array as $algorithm) {
        foreach ($algorithm->rules as $rule) {
            $rules[] = array('type' => $algorithm->type, 'id' => $rule->id, 'rule' => 'IF ' . $rule->conditions . ' THEN ' . $rule->action, 'rank' => 0);
        }
    }


    echo "<TABLE BORDER>";
    echo "<TR ALIGN=CENTER>";
    echo "<TD WIDTH=25><B>ID</B></TD>";
    echo "<TD WIDTH=75><B>Type</B></TD>";
    echo "<TD WIDTH=75><B>Rule</B></TD>";
    echo "<TD WIDTH=75><B>Very difficult to<br>comprehend</B></TD>";
    echo "<TD WIDTH=75><B>Difficult to<br>comprehend</B></TD>";
    echo "<TD WIDTH=75><B>Comprehensible</B></TD>";
    echo "<TD WIDTH=75><B>Easy to<br>comprehend</B></TD>";
    echo "<TD WIDTH=75><B>Very difficult to<br>comprehend</B></TD>";
    echo "</TR>";


    foreach ($rules as $rule) {
        echo '<TR ALIGN=CENTER>';
        echo '<TD  ALIGN=LEFT>' . $rule['id'] . '</TD>';
        echo '<TD  ALIGN=LEFT>' . $rule['type'] . '</TD>';
        echo '<TD  ALIGN=LEFT>' . $rule['rule'] . '</TD>';
        echo '<TD><INPUT TYPE="RADIO" NAME="' . $rule['type'] . '' . $rule['id'] . '" VALUE="1"></TD>';
        echo '<TD><INPUT TYPE="RADIO" NAME="' . $rule['type'] . '' . $rule['id'] . '" VALUE="2"></TD>';
        echo '<TD><INPUT TYPE="RADIO" NAME="' . $rule['type'] . '' . $rule['id'] . '" VALUE="3"></TD>';
        echo '<TD><INPUT TYPE="RADIO" NAME="' . $rule['type'] . '' . $rule['id'] . '" VALUE="4"></TD>';
        echo '<TD><INPUT TYPE="RADIO" NAME="' . $rule['type'] . '' . $rule['id'] . '" VALUE="5"></TD>';
        echo '</TR>';
    }

    echo '</TABLE>';

    echo '<input type="hidden" name="rules" value="' . base64_encode(serialize($rules)) . '"/>';
    echo '<input type="hidden" name="algorithmsx" value="' . base64_encode(serialize($passed_array)) . '"/>';
    ?>

    <br>
    <input type="submit" name="submit_survey" value="submit">

</form>


