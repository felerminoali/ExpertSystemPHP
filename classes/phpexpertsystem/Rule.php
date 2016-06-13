<?php

class Rule {
  private $condition;

  private $action;

  private $priority;

  private $executed = false;

  private $active = true;
  
  private $wildcards;
  
  private $wildcardExpansions = array();
  private $wildcardExpansionsThatMatchedRuleConditon = array();

  public function __construct($condition, $action, $priority, $active) {
  	$this->condition = $condition;
  	$this->action = $action;
  	$this->priority = $priority;
  	$this->active = $active;

  	$this->searchWildcards(); // Populates $this->wildcards with variable names with wildcard names
  }
  
  // Figure out if the condition has any wildcards and populates $this->wildcards array if any
  private function searchWildcards() {
  	// Figure out all the variables used in the condition
  	// Check if any of them contains WILDCARDx in their names where x is an integer
  	// Add such variable names with wildcard names to $this->wildcards array
  	preg_match_all('/(\$[_\d\w]*WILDCARD\d+[_\d\w]*)/', $this->condition, $matches);
  	
  	$this->wildcards = array_unique($matches[0]);
  }
  
  // Used in wildcard expansion
  // Populates $this-wildcardExpansions
  private function findCombinations($oneCombination, $traits, $i) {
  	if ($i >= count($traits)) {
      $this->wildcardExpansions[] = $oneCombination;
    } else {
      $keys = array_keys($traits);
      foreach($traits[$keys[$i]] as $trait) {
        // Get a copy of $oneCombination and send with added $trait
        $oneCombination[$keys[$i]] = $trait;
        $this->findCombinations($oneCombination, $traits, $i + 1);
      }
    }
  }
  
  private function cleanseWildcardCombinations() {
	  $possibleCombinations = array();
	  foreach($this->wildcardExpansions as $combination) {
	    $allKeys = array_keys($combination);
	    foreach($combination as $key => $value) {
	        // Get the wildcard name
	        preg_match("/(WILDCARD\d+)/", $key, $match);
	        $wildcardName = $match[0];
	        // Go through other keys and see if the same wildcard is used
	        foreach($combination as $otherKey => $otherValue) {
	            if ($key == $otherKey) continue;
	            preg_match("/(WILDCARD\d+)/", $otherKey, $otherMatch);
	            $otherWildcardName = $otherMatch[0];
	
	            if ($wildcardName == $otherWildcardName) {
	                // We have a case where the same wildcard name appears
	                // in multiple wildcared variables
	                // Check if the values to be substituted for wildcards the same.
	                // If not, discard this combination.
	                $tmpReplacementSearch = str_replace($wildcardName, "(\w+)", $key);
	                preg_match("/$tmpReplacementSearch/", $value, $replacementMatches);
	                $replacementValueForWildcard = $replacementMatches[1];
	                // Use the above variable to replace the wildcard in other key
	                $otherReplacedValueShouldBe = str_replace($wildcardName, $replacementValueForWildcard, $otherKey);
	                if ($otherReplacedValueShouldBe != $otherValue) {
	                    // This combination is not acceptable
	                    continue 3;
	                }
	            }
	        }
	    }
	
	    // Get all the keys
	    $possibleCombinations[] = $combination;
	  }
	  
  	$this->wildcardExpansions = $possibleCombinations;
  }
  
  public function checkCondition(WorkingMemory $_wm)  {
  	// Make all the working memory content available as variables here
  	$_facts = $_wm->getAllFacts();
  	extract($_facts);
	//unset($_facts);
	
  	// Should wildcard expansion take place?
  	if (sizeof($this->wildcards) > 0) {
  		// Wildcards used in the condition.
  		// Figure out the fact variable names that matches with the wildcard strings
  		foreach($this->wildcards as $_variableWithWildcard) {
  			// Turn something like $xx_WILDCARD12_xx to $xx_[_\d\w]*_xx which
  			// facilitates using another preg_match to figure out matching fact variables
  			$_searchPattern = preg_replace('/WILDCARD\d+/', '[_\d\w]*', $_variableWithWildcard);
  			$_searchPattern = str_replace('$', '', $_searchPattern);
//  			print_r($_searchPattern);
  			foreach ($_facts as $_key => $_value) {
//  				print_r($_key);
  				if (preg_match('/'.$_searchPattern.'/', $_key)) {
  					// This fact variable is a match
  					$_wildcardMatches[$_variableWithWildcard][] = '$'.$_key;
  				}
  			}
  		}
  		
  		//print_r($_wildcardMatches);
  		
  		// Find all wildcard combinations possible
  		$this->findCombinations(array(), $_wildcardMatches, 0);// print_r($this->wildcardExpansions);
  		// Not all combinations will be possible. For example $_x_WILDCARD1 and $_y_WILDCARD1 cannot have
  		// $_x_1 and $_y_2. Remove such
  		$this->cleanseWildcardCombinations();
  		//print_r($this->wildcardExpansions);
  		 
  		// Now $this->wildcardExpression has all possible combinations of variables in working memory with values
  		foreach ($this->wildcardExpansions as $_combination) { //print_r($_combination);
  			// Rewrite the condition for all the keys
  			$_condition = $this->condition;
  			foreach($_combination as $_wildcardName => $_wildcardValue) {
  				// WILDCARDNAMELIMIT - DON'T REMOVE THIS LINE
  				$_condition = str_replace($_wildcardName, $_wildcardValue, $_condition);
  			}
  			
  			// Evaluate the condition now
  			$_result = eval(
		  		"if (" . $_condition . ") {
		  		     return true;
		         } else { 
		             return false;
		         }" );
  			if ($_result == true) {
  				$this->wildcardExpansionsThatMatchedRuleConditon[] = $_combination;
  			}
  		}
  		
//  		print_r($this->wildcardExpansionsThatMatchedRuleConditon);
  		if (count($this->wildcardExpansionsThatMatchedRuleConditon) > 0) {
  			return true;
  		}
  		
  		return false;
  		
  	} else {
  		// No wildcards used in the condition string. Simple evaluation of condition possible.
	  	
	  	// Evaluate the condition
	  	$_result = eval(
	  		"if (" . $this->condition . ") {
	  		     return true;
	         } else { 
	             return false;
	         }" );
	  	
	  	// Return the results
	  	return $_result;	
  	}  	
  }

  
  public function execute(WorkingMemory $_wm) {
   	// Make all the working memory content available as variables here
  	$_facts = $_wm->getAllFacts();
  	extract($_facts);
  	unset($_facts);
  	
  	// Execute the action now
  	
  	// Determine if wildcard expansion is needed?
  	if (count($this->wildcardExpansionsThatMatchedRuleConditon) > 0) {
  		// $this->action should be searched and replaced with wildcard substitutions and executed multiple times
  		foreach ($this->wildcardExpansionsThatMatchedRuleConditon as $_combination) {
  			// Rewrite the action for all the keys
  			$_action = $this->action;
//			print_r($_action);
  			foreach($_combination as $_wildcardedVariableName => $_wildcardedVariableReplacement) {
  				preg_match("/(WILDCARD\d+)/", $_wildcardedVariableName, $_match);
	        	$_wildcardName = $_match[0];
	        	$_tmpReplacementSearch = str_replace($_wildcardName, "(\w+)", $_wildcardedVariableName);
//	        	print_r($_tmpReplacementSearch);
//	        	print_r($_wildcardedVariableReplacement);
	            preg_match("/\\$_tmpReplacementSearch/", $_wildcardedVariableReplacement, $_replacementMatches);
//	            print_r($_replacementMatches);
	            $_replacementValueForWildcard = $_replacementMatches[1];
	        	
  				// WILDCARDNAMELIMIT - DON'T REMOVE THIS LINE
//  				print_r($_wildcardName);
  				$_action = str_replace($_wildcardName, $_replacementValueForWildcard, $_action);
//  				print_r($_action);
  			}
//  			print_r($_action);
			eval($_action);
  		}
  		
  	} else {
  		// Simple, no wildcard substitutions needed
  		eval($this->action);
  	}
  	
  	unset($_action);
  	unset($_combination);
  	unset($_match);
  	unset($_replacementMatches);
  	unset($_replacementValueForWildcard);
  	unset($_tmpReplacementSearch);
  	unset($_wildcardName);
  	unset($_wildcardedVariableName);
  	unset($_wildcardedVariableReplacement);
  	
  	// Get everything back into working memory
  	$everything = get_defined_vars();
  	unset($everything['_wm']);
  	$_wm->setAllFacts($everything);
  	
  	// Set the executed flag
  	$this->executed = true;
  }
  
  // Access methods
  
  public function getPriority() {
  	return $this->priority;	
  }
  
  public function isExecuted() {
  	return $this->executed;
  }

  public function isActive() {
  	return $this->active;
  }
  
}