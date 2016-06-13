<?php

interface RuleDAO {
  public function findAll($ruleSet, $activeOnly = true);
  
// Felermino Ali 
  public function insert_rules($algorithm, $ruleSet);
  public function insert_rule($ruleset, $conditionString, $actionString, $deleteAll);
  public function delete($ruleset);
}