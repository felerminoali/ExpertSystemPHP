<?php

class RandomConflictResolutionStrategy implements ConflictResolutionStrategy {
  
  public function selectPreferredRule($rules, $workingMemory) {
  	$random = rand(0, sizeof($rules) - 1); // Calculate a random number
  	return $rules[$random];
  }
}