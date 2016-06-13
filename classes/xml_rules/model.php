<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rule
 *
 * @author feler
 */
class xml_algorithm {

    //put your code here

    var $type = NULL;
    var $accuracy = NULL;
    var $rules = array();

    function getType() {
        return $this->type;
    }

    function getRules() {
        return $this->rules;
    }

    function getAccuracy() {
        return $this->accuracy;
    }

    function setAccuracy($accuracy) {
        $this->accuracy = $accuracy;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setRules($rules) {
        $this->rules = $rules;
    }

}

class xml_rule {

    var $id = NULL;
    var $action = NULL;
    var $conditions = NULL;
    var $interpretation = NULL;

    function getId() {
        return $this->id;
    }

    function getInterpretation() {
        return $this->interpretation;
    }

    function setInterpretation($interpretation) {
        $this->interpretation = $interpretation;
    }

    function getAction() {
        return $this->action;
    }

    function getConditions() {
        return $this->conditions;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setAction($action) {
        $this->action = $action;
    }

    function setConditions($conditions) {
        $this->conditions = $conditions;
    }

}
