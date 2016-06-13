<?php
/*
 * WorkingMemory maintains facts as name-value pairs.
 */
class WorkingMemory {
  private $facts = array();
   
  public function setFact($factName, $factValue) {
  	$this->facts[$factName] = $factValue;
  }

  public function getFact($factName) {
  	return $this->facts[$factName];
  }

  public function unsetFact($factName) {
  	unset($this->facts[$factName]);
  }

  public function setAllFacts($facts) {
  	$this->facts = $facts;
  }

  public function getAllFacts() {
  	return $this->facts;
  }
}