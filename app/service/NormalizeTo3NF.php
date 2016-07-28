<?php

require_once (dirname(__FILE__).'/../entity/FD.php');
require_once (dirname(__FILE__).'/../entity/TableObj.php');
require_once (dirname(__FILE__).'/../entity/Sets.php');

require_once ('FunctionalID.php');


/* FD-preserving decomposition to 3NF */
function to3NF($F, $R) {
    
    $result = array();
    $result['steps'] = array();
    
    $NFTables = array();
    $MC = findMergedMC($F);
    
    if (is3NF($F, $R)['isNormalized']) {

        array_push($result['steps'], "Table already in 3NF");

        $tb = new TableObj($R, $MC);
        array_push($NFTables, $tb);
        
        $result['normalizedTables'] = $NFTables;
        return $result;
    }


    //find union of LHSs of FDs
    $LHS = array();
    foreach ($MC as $fd) {
        $LHS = setUnion($fd->ls, $LHS);
    }

    $C = findMiniCover($F)['miniCover'];

    array_push($result['steps'], "Step 1: Find the minimal cover of FDs, which contains ");

    foreach ($C as $fd) {

        $result['steps'][0] = $result['steps'][0]."<br>";
        $result['steps'][0] = $result['steps'][0].$fd->printMe();
    }


    $CK = findAllCK($F, $R)['candidateKeys'];

    $keyAttr = array();

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $NKEY = setDiff($keyAttr, $R);   // non-key attributes  

    array_push($result['steps'], "Step 2. Find all cadnidate keys. The set of candiates keys is { ");
    
    foreach ($CK as $set) {
        $result['steps'][1] =  $result['steps'][1]."(";
        $result['steps'][1] =  $result['steps'][1].printSet($set);
        $result['steps'][1] =  $result['steps'][1]. "), ";
    }
    $result['steps'][1] =  $result['steps'][1]. " }.";

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $result['steps'][1] =  $result['steps'][1]. "<br> The set of key attributes is: { ";
    $result['steps'][1] =  $result['steps'][1].printSet($keyAttr);
    $result['steps'][1] =  $result['steps'][1]. " }.";

    // split tables using FDs   
    // merge FDs with same LHS and whose RHS are non-key attributes
    // merge FDs with same LHS and whose RHS are key attributes

    $N = count($C) - 1;

    //  echo " the numebr of FDs in mini cover is = ".$N;

    $MergedFD1 = array();  //FDs whose RHS are non-key attributes only

    for ($i = 0; $i <= $N; $i++) {

        $fd = $C[$i];

        if ($fd->ls[0] <> "+++" and ! isSubset($fd->rs, $keyAttr)) {
            for ($j = $i + 1; $j <= $N; $j++) {
                $f1 = $C[$j];
                if (isSubset($f1->ls, $fd->ls) and isSubset($fd->ls, $f1->ls) and ! isSubset($f1->rs, $keyAttr)) {
                    $rhs = setUnion($fd->rs, $f1->rs);
                    $fd->rs = $rhs;

                    $f1->ls = array("+++");  //mark FD as deleted;
                }
            }
            array_push($MergedFD1, $fd);
        }
    }


    array_push($result['steps'], "Step 3: Merge FDs with same LHS and whose RHS are non-key attributes, we get the set F1 which contains:<br>");
    foreach ($MergedFD1 as $fd) {
        $result['steps'][2] =  $result['steps'][2].$fd->printMe();
        $result['steps'][2] =  $result['steps'][2]."<br>";
    }

    array_push($result['steps'], "Step 4: Check each FD in the set F1 for violation of 3NF, and split table accordingly. <br><br>");

    foreach ($MergedFD1 as $fd) {

        $result['steps'][3] =  $result['steps'][3]."<br> Checking FD ";
        $result['steps'][3] =  $result['steps'][3].$fd->printMe();

        if (!isSuperkey($fd->ls, $F, $R)) {
            $result['steps'][3] =  $result['steps'][3]. "<br> The FD violates 3NF as its  LHS is not key, its RHS are non-key attributes. ";

            $t = setUnion($fd->rs, $fd->ls);  //first table

            $tableExists = false; //check whether table t already exist   
            foreach ($NFTables as $table) {
                if (isSubset($t, $table->theTable) and isSubset($table->theTable, $t)) {  // check $t is the same as a already found 3NF table 
                    $tableExists = true;
                    break;
                }
            }

            if (!$tableExists) {
                $R = setDiff($fd->rs, $R);   //second table
                //find FDs for table t
                $FDs = array();
                $B = setIntersect($t, $LHS);  //B is the intersection of LHS of FDs and t   
                $subs = findSubset($B);  // find all subsets of $B
                foreach ($subs as $subset) {            //for each subset
                    $A = findClosureSet($subset, $MC);   //closesure of the subset 
                    $B = setDiff($subset, $A);           //closesure minus the subset            
                    $D = setIntersect($t, $B);   // D is the set of attributes in rel(w) that are dependent on the subset
                    if (count($D) > 0) {
                        $f = new FD($subset, $D);
                        array_push($FDs, $f);      // a new FD is added to FDs(w)
                    }
                }
                $FDsforTable_t = findMergedMC($FDs);
                $tb = new TableObj($t, $FDsforTable_t);
                array_push($NFTables, $tb);

                $result['steps'][3] =  $result['steps'][3]. "<br>The following 3NF table is obtained <br>";
                $result['steps'][3] =  $result['steps'][3].$tb->printMe();
                $result['steps'][3] =  $result['steps'][3]. " <br>";
            } else {
                $result['steps'][3] =  $result['steps'][3]. "but the FD is in the splitted table (";
                $result['steps'][3] =  $result['steps'][3].printSet($t);
                $result['steps'][3] =  $result['steps'][3]. "), so we ignore it.";
            }// if (!$tableExists)
        } else { // those FDs where the LHS is superkey in MergedFD1 hold in the last table. so add them to MergedFD2
            $result['steps'][3] =  $result['steps'][3]. "<br> FD does not violate 3NF";
        }
    }

    // add final table to output
    // Find all FDs for table $R
    $FDs = array();
    $B = setIntersect($R, $LHS);  //B is the intersection of LHS of FDs and t     
    $subs = findSubset($B);  // find all subsets of $B
    foreach ($subs as $subset) {            //for each subset
        $A = findClosureSet($subset, $MC);   //closesure of the subset 
        $B = setDiff($subset, $A);           //closesure minus the subset                        
        $D = setIntersect($R, $B);   // D is the set of attributes in rel(w) that are dependent on the subset
        if (count($D) > 0) {
            $f = new FD($subset, $D);
            array_push($FDs, $f);      // a new FD is added to FDs(w)
        }
    }
    $FDsforTable_R = findMergedMC($FDs);
    $tb = new TableObj($R, $FDsforTable_R);  // add table R to 3NF tables 
    array_push($result['steps'], "Step 5: Finally, add the following table into normalized 3NF table set (obtained by removing RHS attributes of FDs violating 3NF): <br>");
    $result['steps'][4] =  $result['steps'][4].$tb->printMe();
    $result['steps'][4] =  $result['steps'][4];
    array_push($NFTables, $tb);
    
    
    $result['normalizedTables'] = $NFTables;
    return $result;
    
} //to3NF

