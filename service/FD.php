<?php

require_once 'Sets.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class FD {
    var $ls;
    var $rs;

    function __construct($array1, $array2) {
        $this->ls = $array1;
        $this->rs = $array2;
    }

    function printme() {
        $result = "";
        $result += printset($this->ls);
        $result += " --> ";
        $result += printset($this->rs);
        return $result;
    }
    
     function isTrivial() {
        $result = false;
        if (isSubset($this->rs, $this->ls)) {
            $result = true;
        }
        return $result;
    }

    function minimizeRs() {
        $rhs = setDiff($this->ls, $this->rs);
        $this->rs = $rhs;
    }
}
