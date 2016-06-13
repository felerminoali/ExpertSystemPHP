<!--<html>
    <head>
        <title>Load Rules</title>
    </head>
    <body>-->

<form method="post" action="survey.php">
    <?php
    include '/lib/lib.php';

    $algorithms = get_xml_content('rules.xml');
    
//    var_dump($algorithms);

    if (!empty($algorithms)) {
        echo count($algorithms) . ' Algorithm(s) loaded <br>';
        echo 'Now you  need to take a survey in order to select an accurate and comprehensible rule to be included into the Knowlwdge Base <br>';
        echo '<input type="hidden" name="algorithms" value="' .base64_encode(serialize($algorithms)) . '"/>';
        echo '<input type="submit" name="submit" value="Take survey">';
    } else {
        echo 'No Rule Found';
    }
    ?>


</form>


<!--</body>-->
<!--</html>-->










