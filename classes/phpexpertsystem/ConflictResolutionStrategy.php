<?php

interface ConflictResolutionStrategy {
  public function selectPreferredRule($rules, $workingMemory);
}