<?php

class TableObj {

    var $theTable;
    var $theFDs;

    function __construct($t, $fds) {
        $this->theTable = $t;
        $this->theFDs = $fds;
    }

    function printMe() {
        $result = "";
        $result = $result.printset($this->theTable);
        $result. "<br>";
        $result. "with FDs <br>";
        foreach ($this->theFDs as $f) {
            $f->printMe();
            $result. "<br>";
        }
        $result. "<br>";
    }

    public function inBCNF() {
        return isBCNF($this->theFDs, $this->theTable);
    }

}