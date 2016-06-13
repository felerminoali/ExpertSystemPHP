<?php

class MySQLRuleDAO implements RuleDAO {

    public function findAll($ruleSet, $activeOnly = true) {

//        $conn = ConnectionFactory::getInstance()->getConnection();
//        var_dump($conn);

        $results = null;

        if ($activeOnly) {
            $results = mysql_query("select * from rule where ruleset = '$ruleSet' and active = true order by priority desc");
//            $sql = "select * from rule where ruleset = '$ruleSet' and active = true order by priority desc";
//            $results = mysqli_query($conn, $sql);
        } else {
            $results = mysql_query("select * from rule where ruleset = '$ruleSet' order by priority desc");
//            $sql = "select * from rule where ruleset = '$ruleSet' order by priority desc";
//            $results = mysqli_query($conn, $sql);
        }

        $rules = array();

        if (mysql_num_rows($results) > 0) {
            while ($row = mysql_fetch_assoc($results)) {
                $rules[] = new Rule($row['conditionString'], $row['actionString'], $row['priority'], $row['active']);
            }
        }

        return $rules;
    }

    public function insert_rule($ruleset, $conditionString, $actionString, $deleteAll = true) {
        $result = false;

        if ($this->isRuleSetExist($ruleset) && $deleteAll) {
            $this->delete($ruleset);
        }

        $sql = "insert into rule (ruleset, conditionString, actionString) values ('$ruleset', '$conditionString', '$actionString')";
        $result = mysql_query($sql);

        return $result;
    }

    public function insert_rules($algorithm, $ruleSet) {
        $result = true; 
       
        $this->delete($ruleSet);
        
        foreach($algorithm->rules as $rule){
            
            $status = $this->insert_rule($ruleSet, $rule->conditions, $rule->action, false);
            
            if(!$status){
                $this->delete($ruleSet);
                $result = false;
                break;
            }
        }
        
        return $result;
    }

    public function delete($ruleset) {
        $sql = "DELETE from rule where ruleset = '$ruleset'";

        $result = mysql_query($sql);
        return $result;
    }
    
    public function findRuleSets(){
         $sql = "SELECT ruleset from rule GROUP BY ruleset";

        $results = mysql_query($sql);
        
        $ruleSets = array();

        if (mysql_num_rows($results) > 0) {
            while ($row = mysql_fetch_assoc($results)) {
                $ruleSets[] = $row;
            }
        }
        
        return $ruleSets;
    }

    private function isRuleSetExist($ruleSet) {

        $results = mysql_query("select * from rule where ruleset = '$ruleSet'");

        if (mysql_num_rows($results) > 0) {
            return true;
        }

        return false;
    }

}
