<?php

require_once (dirname(__FILE__) . '/../entity/FD.php');
require_once (dirname(__FILE__) . '/../entity/TableObj.php');
require_once (dirname(__FILE__) . '/../entity/Sets.php');

require_once ('FunctionalID.php');

function ToBCNF($F, $R) {
    $result = array();
    $result['steps'] = array();

    $NFTables = array();

    $MC = findMergedMC($F);
        
    if (isBCNF($F, $R)['isNormalized']) {            // table is already in BCNF
        array_push($result['steps'], "<br> Table already in BCNF, return itself.");
        
        $tb = new TableObj($R, $MC);
        array_push($NFTables, $tb);
        $result['normalizedTables'] = $NFTables;
        return $result;
    }



    array_push($result['steps'], "Step 1. Find merged minimal cover of FDs, which contains: <br>");

    foreach ($MC as $f) {
        $result['steps'][0] = $result['steps'][0].$f->printMe();
        $result['steps'][0] = $result['steps'][0]. "<br>";
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


    $result['steps'][0] = $result['steps'][0]. "<br> Initially  rel[1] contains the original table, with the  FDs above";

    while ($i < $K) {
        array_push($result['steps'], "Round" . ($i + 1) . ": Checking whether table rel[" . ($i + 1) . "] is in BCNF <br>");
        // printSet($rel[$i]);


        if (isBCNF($FDs[$i], $rel[$i])['isNormalized']) {
            $ff = findMergedMC($FDs[$i]);
            $tb = new TableObj($rel[$i], $ff);
            array_push($NFTables, $tb);

            $result['steps'][$i + 1] = $result['steps'][$i + 1]. "<br> *** Table rel[" . ($i + 1) . "] is in BCNF already, send it to output ***";
        } else {
            $K = $K + 2;

            //echo "<br> Table is not in BCNF, must decompose it  <br>";

            $fs = findMergedMC($FDs[$i]);

            //  echo "hello 1";


            $decomposeBCNF_result = decomposeBCNF($FDs[$i], $rel[$i]);
            $tbs = $decomposeBCNF_result['tbs'];
            $result['steps'][$i + 1] = $result['steps'][$i + 1].$decomposeBCNF_result['steps'];

            // echo "hello 2";

            array_push($rel, $tbs[0]);          // add two relations at the end of the list rel
            array_push($rel, $tbs[1]);


            //   echo "rel[".$w."] = "; printset($tbs[0]); echo "<br>";
            //    echo "rel[".($w+1)."] = "; printset($tbs[1]); echo "<br>";
            // printset($tbs[1]);
            // echo "<br> next, find the corresponding FDs for each new relation"; // initially both are empty sets

            array_push($FDs, array());

            array_push($FDs, array());

            for ($w = $K - 2; $w <= $K - 1; $w++) {


                $result['steps'][$i + 1] = $result['steps'][$i + 1]. "<br> rel[" . ($w + 1) . "]= (";
                $result['steps'][$i + 1] = $result['steps'][$i + 1].printset($rel[$w]);
                $result['steps'][$i + 1] = $result['steps'][$i + 1]. " )<br>";

                $B = setIntersect($rel[$w], $LHS);  //B is the intersection of LHS and rel(w)
                //  echo "<br> B="; printset($B);

                $subs = findSubset($B);  // find all subsets of $B
                //      echo "hello 5";

                foreach ($subs as $subset) {            //for each subset
                    //       echo "subsets are <br>";
                    //       printset($subset); echo "<br>";

                    $A = findClosureSet($subset, $MC);   //closesure of the subset 

                    $B = setDiff($subset, $A);           //closesure minus the subset

                    $D = setIntersect($rel[$w], $B);   // D is the set of attributes in rel(w) that are dependent on the subset

                    if (count($D) > 0) {
                        $f = new FD($subset, $D);

//  echo "<br> new FD:  for table "; printset($rel[$w]); echo "::"   $f->printMe();      

                        array_push($FDs[$w], $f);      // a new FD is added to FDs(w)
                    }
                } //for each

                $FDs[$w] = findMergedMC($FDs[$w]);
                $result['steps'][$i + 1] = $result['steps'][$i + 1]. "With FDs: <br>";
                foreach ($FDs[$w] as $ff) {
                    $ff->printMe();
                    $result['steps'][$i + 1] = $result['steps'][$i + 1]. "<br>";
                }
            }
        } //else



        $i++;  //next iteration
    } //end of while
    
    $result['normalizedTables'] = $NFTables;
    return $result;
}

function decomposeBCNF($F, $T) {
    // F should be a minimal cover, decomposes table $T into two with first violating FD
    // $F = findMergedMC($F);  //already done in calling function
    $result = array();
    $result['steps'] = "";
    $tbs = array();  // return a set of attribute sets, no FDs


    $Split = true;


    foreach ($F as $fd) {
        //echo "<br> Checking FD "; $fd->printMe(); 
        //   echo "----table ="; printset($T); echo "<br>";
        //   echo "----FD ="; $fd->printMe();  "<br>";
        // modify the FD so that the RHS contains closure set of LHS, this corrects an error in the earlier version 19/07/2016 7:00pm
        // original has a bug using example real2

        $closure = findClosureSet($fd->ls, $F);
        $RHS = setDiff($fd->ls, $closure);
        $f = new FD($fd->ls, $RHS);

        //  echo "<br> modify FD so that RHS includes all attributes that are dependent on LHS, which is "; $f->printMe();

        if ($Split) {
            if (!isSuperKey($f->ls, $F, $T)) {

                $A = $f->ls;

                $B = $f->rs;

                $tb1 = setUnion($B, $A);

                $tb2 = setDiff($B, $T);


                $result['steps'] = $result['steps']. "<br> The FD [";
                $result['steps'] = $result['steps'].$fd->printMe();
                $result['steps'] = $result['steps']."] violates BCNF as the LHS is not superkey. Table is split into the two below: <br>";

                //  echo "<br> LHS is not superkey, so violates BCNF. Table is split into the following two: <br>";      
                //  echo "<br> **** tab1="; printset($tb1);
                //  echo "<br>**** tab2="; printset($tb2);


                array_push($tbs, $tb1);

                array_push($tbs, $tb2);

                $Split = false;
                break;
            } //if
            //  echo "<br> LHS is superkey....";   
        } //if
    } //for

    $result['tbs'] = $tbs;
    return $result;
}
