<?php

require_once (dirname(__FILE__) . '/../entity/FD.php');
require_once (dirname(__FILE__) . '/../entity/TableObj.php');
require_once (dirname(__FILE__) . '/../entity/Sets.php');

require_once ('FunctionalID.php');

function isBCNF($F, $R) {
    $result = array();
    $result['steps'] = "";
    $val = true;

// if ($showsteps){ 
    $result['steps'] = $result['steps'] . "<br> A table is in BCNF if and only if for every non-trivial FD, the LHS is a superkey. <br>";
// }

    foreach ($F as $fd) {
        if (!$fd->isTrivial() and ! isSuperKey($fd->ls, $F, $R)) {

            //     if ($showsteps) {	
            $result['steps'] = $result['steps'] . "<br> The FD ";
            $result['steps'] = $result['steps'] . $fd->printMe();
            $result['steps'] = $result['steps'] . " is non-trivial and its LHS is not a superkey. It violates BCNF. <br>";
            //    }
            $val = false;
            break;
        }
    };

    $result['isNormalized'] = $val;
    return $result;
}

function is3NF($F, $R) {
    $result = array();
    $result['steps'] = "";
    $val = true;

    $CK = findAllCK($F, $R)['candidateKeys'];

    $keyAttr = array();

    $result['steps'] = $result['steps'] . "<br>find all cadnidate keys. The candiates keys are ";
    foreach ($CK as $set) {
        $result['steps'] = $result['steps'] . "{ ";
        $result['steps'] = $result['steps'] . printSet($set);
        $result['steps'] = $result['steps'] . "}, ";
    }

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $result['steps'] = $result['steps'] . "The set of key attributes are: { ";
    $result['steps'] = $result['steps'] . printSet($keyAttr);
    $result['steps'] = $result['steps'] . " }";

    $result['steps'] = $result['steps'] . "<br> for each FD, check whether the LHS is superkey or the RHS are all key attributes";


    foreach ($F as $fd) {
        $result['steps'] = $result['steps'] . "<br> checking functional dependency ";
        $result['steps'] = $result['steps'] . $fd->printMe();

        if (!$fd->isTrivial() and ! isSuperkey($fd->ls, $F, $R) and ! isSubset($fd->rs, $keyAttr)) {
            $result['steps'] = $result['steps'] . "<br> The above FD violates definition of 3NF: it is non-trivial, LHS is not superkey, RHS contains a non-key attribute.";
            $val = false;
            break;
        }
    }

    $result['isNormalized'] = $val;
    return $result;
}

//end is3NF


/* * ***************************************************************************** */

function is2NF($F, $R) {
    $result = array();
    $result['steps'] = "";
    $val = true;

    $CK = findAllCK($F, $R)['candidateKeys'];

    $keyAttr = array();

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $result['steps'] = $result['steps'] . "<br>find all candidate keys. The candiates keys are ";
    foreach ($CK as $set) {
        $result['steps'] = $result['steps'] . "{ ";
        $result['steps'] = $result['steps'] . printSet($set);
        $result['steps'] = $result['steps'] . "}, ";
    }

    $result['steps'] = $result['steps'] . "The set of key attributes are: { ";
    $result['steps'] = $result['steps'] . printSet($keyAttr);
    $result['steps'] = $result['steps'] . " }";

    $result['steps'] = $result['steps'] . "<br> for each non-trivial FD, check whether the LHS is a proper subset of some candidate key or the RHS are not all key attributes";

    foreach ($F as $fd) {

        $result['steps'] = $result['steps'] . "<br> checking FD: ";
        $result['steps'] = $result['steps'] . $fd->printMe();
        if (!$fd->isTrivial() and ! isSuperkey($fd->ls, $F, $R) and ! isSubset($fd->rs, $keyAttr)) {
            foreach ($CK as $key) {
                if (isSubset($fd->ls, $key)) { // $fd->ls must be a proper subset as it is not a superkey
                    $result['steps'] = $result['steps'] . "<br> The FD: violates definition of 2NF -- LHS is a proper subset of some CK";
                    $val = false;
                    break;
                }
            } //inner foreach

            if (!$val) {
                break;
            }
        }
    }//outer for each

    $result['isNormalized'] = $val;
    return $result;
}

//end is2NF
 
 