<?php

require_once (dirname(__FILE__) . '/../entity/FD.php');
require_once (dirname(__FILE__) . '/../entity/TableObj.php');
require_once (dirname(__FILE__) . '/../entity/Sets.php');

require_once ('FunctionalID.php');

function from1NFto3NF($F, $R) {

    $result = array();
    $result['steps'] = array();

    $NFTables = array();

    $MC = findMiniCover($F)['miniCover'];

    array_push($result['steps'], "Step 1: Find the minimal cover of the FDs, which is <br>");
    foreach ($MC as $fd) {
        $result['steps'][0] = $result['steps'][0] . $fd->printMe();
        $result['steps'][0] = $result['steps'][0] . "<br>";
    }
    // find LHS of FDs-- the set of attributes on LHS of some FD. Will be used to generate FDs for new tables  

    $LHS = array();

    foreach ($MC as $fd) {

        $LHS = setUnion($fd->ls, $LHS);
        // $fd->printMe(); echo "<br>";
    }

    $rel = array($R);  //set of tables, initially contains a single table R 
    $FDs = array($MC); // corresponding FD set

    $i = 0;

    $K = 1; // the number of tables in $rel

    $result['steps'][0] = $result['steps'][0] . "<br> Initially rel[1] is the original table: <br>";
    //foreach($MC as $fd){$fd->printMe(); echo "<br>";}


    while ($i < $K) {

        array_push($result['steps'], "Round" . ($i + 1) . ": Checking table rel[" . ($i + 1) . "] <br>");


        if (is3NF($FDs[$i], $rel[$i], false)) {

            $result['steps'][$i + 1] = $result['steps'][$i + 1] . "<br>***** Table is in 3NF already, send it to output *****";
            $fs = findMergedMC($FDs[$i]);
            $tb = new TableObj($rel[$i], $fs);
            array_push($NFTables, $tb);
        } else {

            $K = $K + 2;

            $result['steps'][$i + 1] = $result['steps'][$i + 1] . "<br> Table is not in 3NF";
            $tbs = decompose2NFto3NF($FDs[$i], $rel[$i]);

            array_push($rel, $tbs[0]); // this table is actually already in 2NF, but it is OK to check again as efficiency is not a problem. 

            array_push($rel, $tbs[1]); // add one relation at the end of the list rel
            // next Find the corresponding FDs for each new relation, initially they are empty sets

            array_push($FDs, array());

            array_push($FDs, array());

            //  echo "hello 3";

            for ($w = $K - 2; $w <= $K - 1; $w++) {


                //   echo "rel(w)="; printset($rel[$w]);

                $B = setIntersect($rel[$w], $LHS);  //B is the intersection of LHS and rel(w)
                //     echo "B="; printset($B);

                $subs = findSubset($B);

                //      echo "hello 5";

                foreach ($subs as $subset) {            //for each subset
                    //       echo "subsets are <br>";
                    //       printset($subset); echo "<br>";
                    $A = findClosureSet($subset, $MC);   //closesure of the subset 

                    $B = setDiff($subset, $A);           //closeset minus the subset

                    $D = setIntersect($rel[$w], $B);   // D is the set of attributes in rel(w) that are dependent on the subset

                    if (count($D) > 0) {
                        $f = new FD($subset, $D);
                        array_push($FDs[$w], $f);      // a new FD is added to FDs(w)
                    }
                }
                $result['steps'][$i + 1] = $result['steps'][$i + 1] . "<br> rel[" . ($w + 1) . "] = (";
                $result['steps'][$i + 1] = $result['steps'][$i + 1] . printSet($rel[$w]);
                $result['steps'][$i + 1] = $result['steps'][$i + 1] . "), with FDs: <br>";
                $FDs[$w] = findMergedMC($FDs[$w]);
                //     echo "With FDs: <br>";
                foreach ($FDs[$w] as $ff) {
                    $result['steps'][$i + 1] = $result['steps'][$i + 1] . $ff->printMe();
                    $result['steps'][$i + 1] = $result['steps'][$i + 1] . "<br>";
                }
            }
        } //else

        $i++;  //next iteration
    } //end of while

    $result['normalizedTables'] = $NFTables;
    return $result;
}

//2NFto3NF1

function decompose2NFto3NF1($F, $R) {
    // assuming R is in 2NF

    $tbs = array();  // returns a set of 2 tables, no FDs

    $C = findMiniCover($F)['miniCover'];
    $CK = findAllCK($F, $R)['candidateKeys'];

    /*echo "<br> Find the CKs, which contains {";
    foreach ($CK as $key) {
        echo "(";
        printSet($key);
        echo ") ";
    }
    echo "}";*/


    $keyAttr = array();  // key attrbutes

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $R1 = $R;
    $NKEY = setDiff($keyAttr, $R1);   // non-key attributes

    /*echo "<br> Find non-key attributes, which are {";
    printSet($NKEY);
    echo "}.";*/

    $checkMoreFDs = true;

    foreach ($C as $fd) {

        if (!isSuperKey($fd->ls, $C, $R) and isSubset($fd->rs, $NKEY)) {

            /*echo "<br>The FD [";
            $fd->printMe();
            echo "] violates 3NF, the table is split into: <br>";*/
            $A = $fd->ls;
            $X = findClosureSet($A, $C);


            $Y = setDiff($A, $X);

            $B = setDiff($keyAttr, $Y);  // B is the set of all non-key attributes dependent on fd->ls and not in fd->ls

            $tb1 = setUnion($B, $A);

            $tb2 = setDiff($B, $R);


            array_push($tbs, $tb1);

            array_push($tbs, $tb2);


            break;  // no longer needs to check more FDs
        } //end if
    }

    return $tbs;
}

//decompose2NFto3NF1

