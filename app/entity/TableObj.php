<?php

class TableObj {

    var $theTable;
    var $theFDs;

    function __construct($t, $fds) {
        $this->theTable = $t;
        $this->theFDs = $fds;
    }

    function printMe() {
        $result = "<br>";
        $result = $result.printset($this->theTable);
        $result = $result. "<br>";
        $result = $result. "with FDs <br>";
        foreach ($this->theFDs as $f) {
            $result = $result. $f->printMe();
            $result = $result. "<br>";
        }
        $result = $result. "<br>";
        return $result;
    }

    public function inBCNF() {
        return isBCNF($this->theFDs, $this->theTable);
    }

}