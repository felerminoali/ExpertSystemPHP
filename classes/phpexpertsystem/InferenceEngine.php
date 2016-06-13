<?php

class InferenceEngine {
  private $workingMemory;
  private $conflictResolutionStrategy;
  private $loadedRules;
  private $loadedRuleSetName;
  private $ruleDAO;
  
  public function __construct($ruleSetName) {
  	$this->workingMemory = new WorkingMemory();
  	$this->conflictResolutionStrategy = new RandomConflictResolutionStrategy();
  	$this->ruleDAO = new MySQLRuleDAO();
  	$this->loadedRuleSetName = $ruleSetName;
  }

  public function getWorkingMemory() {
  	return $this->workingMemory;
  }
  
  public function run() {
  	$this->loadAllRules($this->loadedRuleSetName);

  	while(true) {
  		// Get the rules that has matching condtions
  		$matchedRules = array();
  		foreach ($this->loadedRules as $rule) {
  			// If we have selected some rules already and if the new rule is of lower
  			// priority, we need not worry about additional rules.
  			if (sizeof($matchedRules) > 0) {
  				if ($rule->getPriority() < $matchedRules[0]->getPriority()) {
  					break;
  				}
  			}
  			
  			// We neglect the rules that have executed already
  			// Other rules with satisfying 'if' clauses are selected.
  			if (! $rule->isExecuted() && $rule->checkCondition($this->workingMemory)) {
  				$matchedRules[] = $rule;
  			}
  		}
  		
  		$matchedRuleCount = sizeof($matchedRules);
  		
  		$selectedRule = null;
  		
  		if ($matchedRuleCount == 0) { // No more rules?
  			break;
  		} else if ($matchedRuleCount == 1) { // Only one rule selected
  			$selectedRule = $matchedRules[0];	
  		} else { // Multiple rules of same priority? Resolve the conflicts 
  			$selectedRule = $this->conflictResolutionStrategy->selectPreferredRule($matchedRules, $this->workingMemory);
  		}

  		$selectedRule->execute($this->workingMemory);
  	}
  	
  }

  private function loadAllRules($ruleSet) {
  	$this->loadedRules = $this->ruleDAO->findAll($ruleSet, true);
  }
}