<?php

class TableObj {

    var $theTable;
    var $theFDs;

    function __construct($t, $fds) {
        $this->theTable = $t;
        $this->theFDs = $fds;
    }

    function printMe() {
        printset($this->theTable);
        echo "<br>";
        echo "with FDs <br>";
        foreach ($this->theFDs as $f) {
            $f->printMe();
            echo "<br>";
        }
        echo "<br>";
    }

    public function inBCNF() {
        return isBCNF($this->theFDs, $this->theTable);
    }

}