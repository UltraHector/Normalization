<?php

require_once (dirname(__FILE__) . '/../entity/FD.php');
require_once (dirname(__FILE__) . '/../entity/TableObj.php');
require_once (dirname(__FILE__) . '/../entity/Sets.php');
require_once (dirname(__FILE__).'/../util/Array_Util.php');

require_once ('FunctionalID.php');

function from1NFto3NF($F, $R) {
    //actually works from 1NF to 3NF by removing partial and transitive dependencies one by one, does not distinguish the two

    $result = array();
    $result['steps'] = array();
    $NFTables = array();

    if (is3NF($F, $R)['isNormalized']) {
        array_push($result['steps'], "<br> Table already in 3NF");
        $tb = new TableObj($R, $MC);
        array_push($NFTables, $tb);


        $result['normalizedTables'] = array_copy($NFTables);
        return $result;
    }

    $MC = findMiniCover($F)['miniCover'];
    $LHS = array();

    foreach ($MC as $fd) {
        $LHS = setUnion($fd->ls, $LHS);
    }

    $rel = array($R);  //set of tables, initially contains a single table R 
    $FDs = array($MC); // corresponding FD set

    $i = 0;

    $K = 1; // the number of tables in $rel

    array_push($result['steps'], "<br> Initially rel[1] is the original table with the original functional dependencies.");

    $result['steps'][0] = $result['steps'][0] . "<br> In each round we check the FDs one by one to see if there is a violation of 3NF (there is a partial or transitive dependency where the RHS includes non-key attributes). If yes, we decompose the table into two.";

    while ($i < $K) {
        array_push($result['steps'], "Round" . ($i + 1) . ": checking table rel[" . ($i + 1) . "] <br>");

        if (is3NF($FDs[$i], $rel[$i])['isNormalized']) {
            $result['steps'][$i + 1] = $result['steps'][$i + 1] . "<br>***** The table is in 3NF already, send it to output *****";
            $fs = findMergedMC($FDs[$i]);
            $tb = new TableObj($rel[$i], $fs);

            //   echo "3NF table"; $tb->printMe();

            array_push($NFTables, $tb);
        } else {

            $result['steps'][$i + 1] = $result['steps'][$i + 1] . "<br> The table is not in 3NF.";
            $K = $K + 2;

            $tbs = decompose2NFto3NF($FDs[$i], $rel[$i]);

            array_push($rel, $tbs[0]); // this table is actually already in 2NF, but it is OK to check again as efficiency is not a problem. 

            array_push($rel, $tbs[1]); // add one relation at the end of the list rel
            //        echo "<br> table 1 = ["; printSet($tbs[0]);
            //         echo "<br> table 2 = ["; printSet($tbs[1]); 
            // next Find the corresponding FDs for each new relation, initially they are empty sets

            array_push($FDs, array());

            array_push($FDs, array());


            for ($w = $K - 2; $w <= $K - 1; $w++) {
                $B = setIntersect($rel[$w], $LHS);  //B is the intersection of LHS and rel(w)
                $subs = findSubset($B);

                foreach ($subs as $subset) {            //for each subset
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

    $result['normalizedTables'] = array_copy($NFTables);
    return $result;
}

//2NFto3NF1

function decompose2NFto3NF($F, $R) {
    // assuming R is in 2NF

    $tbs = array();  // returns a set of 2 tables, no FDs

    $C = findMiniCover($F)['miniCover'];
    $CK = findAllCK($F, $R)['candidateKeys'];

    /* echo "<br> Find the CKs, which contains {";
      foreach ($CK as $key) {
      echo "(";
      printSet($key);
      echo ") ";
      }
      echo "}"; */


    $keyAttr = array();  // key attrbutes

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $R1 = $R;
    $NKEY = setDiff($keyAttr, $R1);   // non-key attributes

    /* echo "<br> Find non-key attributes, which are {";
      printSet($NKEY);
      echo "}."; */

    $checkMoreFDs = true;

    foreach ($C as $fd) {

        if (!isSuperKey($fd->ls, $C, $R) and isSubset($fd->rs, $NKEY)) {

            /* echo "<br>The FD [";
              $fd->printMe();
              echo "] violates 3NF, the table is split into: <br>"; */
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

