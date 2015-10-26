<?php

require_once ('FD.php');
require_once ('TableObj.php');
require_once ('Sets.php');



/* test if $A is a superkey of $R under FDs F   */
function isSuperKey($A, $F, $R) {
    $AA = findClosureSet($A, $F);
    if (isSubset($R, $AA)) {
        $v = true;
    } else {
        $v = false;
    }
    return $v;
}
function findAllCK($F, $R) {
    
    $CK = array();
        
    // Save the results    
    $Cover = findMiniCover($F);
    $RHS = array();
    $LHS = array();

    foreach ($Cover as $fd) {
        $RHS = setUnion($fd->rs, $RHS);
        $LHS = setUnion($fd->ls, $LHS);
    }
    if (!isSubset($RHS, $R) or ! isSubset($LHS, $R)) {
        $CK['status'] = false;
        $CK['errorMessage'] = "Input error-- some attributes in the FDs are not in the table.";
    }

    $x = $R;  // prevent $R from being changed
    $NRHS = setDiff($RHS, $x);  // attributes not on RHS of any FD. Every CK should contain these attributes 
    $x = $RHS;
    $RHSButNotLHS = setDiff($LHS, $x); //attributes on RHS but not on LHS. No CK will have these attributes

    $x = $R;
    $NORHS = setDiff($RHSButNotLHS, $x); // attributes not only on RHS



    if (isSuperKey($NRHS, $F, $R)) {
        array_push($CK, $NRHS);   // $NRHS is the unique candidate key in this case 
        return $CK;
    } else {
        $G = array(array(), array()); // G is an array of array. G[0] is empty array G[i] is used to store 1-attribute sets.  

        foreach ($NORHS as $attr) {
            $X = $NRHS;

            if (!isElement($attr, $X)) {
                $X = addElement($attr, $X);

                if (isSuperKey($X, $Cover, $R)) {
                    array_push($CK, $X);  //X is a (count+1)-element CK, where count= count($NRHS)
                } else {
                    array_push($G[1], array($attr));  //G[1] contains 1-attrubte sets that do not form CKs together with $NRHS
                }
            }
        }
    }

    $val = true;
    $k = 1;
    while (count($G[$k]) > 1) {
        $k++;
        array_push($G, array());  // $G[k] is initially empty

        for ($i = 0; $i <= count($G[$k - 1]) - 2; $i++) {
            $A = $G[$k - 1][$i];

            for ($j = $i + 1; $j <= count($G[$k - 1]) - 1; $j++) {
                $B = $G[$k - 1][$j];
                
                array_pop($A);
                array_pop($B);
                
                if (isSubset($A, $B) and isSubset($B, $A)) { // if the first k-1 elements are the same
                    if ($G[$k - 1][$i][$k - 2] <= $G[$k - 1][$j][$k - 2]) {  // keep CKs sorted
                        $B = addElement($G[$k - 1][$i][$k - 2], $B);
                        $B = addElement($G[$k - 1][$j][$k - 2], $B);
                    } else {
                        $B = addElement($G[$k - 1][$j][$k - 2], $B);
                        $B = addElement($G[$k - 1][$i][$k - 2], $B);
                    }
                    $BB = $B;  // prevent the next line from changing the set $B 
                    $Z = setUnion($NRHS, $BB);
                    if (isSuperKey($Z, $Cover, $R)) {  // if Z is 
                        $asubsetIskey = false;
                        $D = findSubset($A);
                        foreach ($D as $attrSet) {
                            $Z1 = setUnion($NRHS, $AttrSet);
                            if (isSuperKey($Z1, $Cover, $R)) {
                                $asubsetIskey = true;
                            }
                        }
                        if (!$asubsetIskey) {  // Z is a CK 
                            array_push($CK, $Z);
                        }
                    } else {
                        array_push($G[$k], $B);
                    }
                } //if
            } //for
        }  //for
    } // while
    
    return $CK;
}
//end of findAllCk
function isBCNF($F, $R) {
    $val = array();
    $val['isNormalized'] = true;
    $val['steps'] = array();
    $val['violation'] = array();
    
    
    foreach ($F as $fd) {
        if (!$fd->isTrivial() and !isSuperKey($fd->ls, $F, $R)) {
            $val['isNormalized'] = false;
        }
    };
    return $val;
}
/* * ******************************************************************************* */
function is3NF($F, $R, $showsteps) {

    $val = array();
    $val['isNormalized'] = true;
    $val['steps'] = array();
    $val['violation'] = array();
    
    $CK = findAllCK($F, $R);
    $keyAttr = array();
    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    if ($showsteps) {
        $newStep = array();
        $newStep['description']  = "Find the key attributes: The key attributes are ";
        $newStep['attributes'] = printset($keyAttr);
        
        array_push($val['steps'], $newStep);
    }
    
    $C = findMiniCover($F);
    foreach ($C as $fd) {
        if (!isSuperkey($fd->ls, $F, $R) and ! isSubset($fd->rs, $keyAttr)) {
            $val['isNormalized'] = false;
            $f = $fd;
        }
    }
    if ($showsteps) {
        if (!$val['isNormalized']) {
            $val['violation']['desctiption'] = "The FD violates the definition of 3NF.";
            $val['violation']['FD'] = $f->printMe();
        } else {
            $val['violation']['desctiption'] = "No FD violates 3NF: either LHS is superkey or RHS is key attribute";
        }
    }
    return $val;
}
/* * ***************************************************************************** */
function is2NF($F, $R, $showsteps) {
    $val = array();
    $val['isNormalized'] = true;
    $val['steps'] = array();
    $val['violation'] = array();
    
    $CK = findAllCK($F, $R);
    $keyAttr = array();
    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }
    if ($showsteps) {
        $newStep = array();
        $newStep['description']  = "Find the key attributes: The key attributes are ";
        $newStep['attributes'] = printset($keyAttr);
    }
    $C = findMiniCover($F);
    foreach ($C as $fd) {
        if (!isSuperkey($fd->ls, $F, $R) and ! isSubset($fd->rs, $keyAttr)) {
            foreach ($CK as $key) {
                if (isSubset($fd->ls, $key)) {
                    $val = false;
                    $f = $fd;
                }
            }
        }
    }
    if ($showsteps) {
        if (!$val['isNormalized']) {
            $val['violation']['desctiption'] = "The FD violates the definition of 3NF.";
            $val['violation']['FD'] = $f->printMe();
        } else {
            $val['violation']['desctiption'] = "No FD violates 3NF: either LHS is superkey or RHS is key attribute";
        }
    }
    return $val;
}
//end is2NF





/* FD-preserving decomposition to 3NF */
function to3NF($F, $R) {
    $NFTables = array();

    $MC = findMergedMC($F);

    if (is3NF($F, $R, false)['isNormalized']) {
        $tb = new TableObj($R, $MC);
        array_push($NFTables, $tb);
        return $NFTables;
    }
   
    $C = findMiniCover($F);
    $CK = findAllCK($F, $R);
    $keyAttr = array();
    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }
    $NKEY = setDiff($keyAttr, $R);   // non-key attributes
    $N = count($C) - 1;
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
    
    $MergedFD2 = array();  //FDs whose RHS are key attributes only and thos

    for ($i = 0; $i <= $N; $i++) {
        $fd = $C[$i];
        if ($fd->ls[0] <> "+++" and isSubset($fd->rs, $keyAttr)) {
            for ($j = $i + 1; $j <= $N; $j++) {
                $f1 = $C[$j];
                if (isSubset($f1->ls, $fd->ls) and isSubset($fd->ls, $f1->ls) and isSubset($f1->rs, $keyAttr)) {
                    $rhs = setUnion($f1->rs, $fd->rs);
                    $fd->rs = rhs;
                    $f1->ls = array("+++");  //mark FD as deleted;
                }
            }
            array_push($MergedFD2, $fd);
        }
    }
    foreach ($MergedFD1 as $fd) {
        if (!isSuperkey($fd->ls, $F, $R)) {
            //FD violates 3NF, split table now
            $t = setUnion($fd->rs, $fd->ls);  //first table
            $R = setDiff($fd->rs, $R);   //second table

            $tb = new TableObj($t, array($fd));
            array_push($NFTables, $tb);
        } else { // those FDs where the LHS is superkey in MergedFD1 hold in the last table. so add them to MergedFD2
            array_push($MergedFD2, $fd);
        }
    }
    // add final table to output
    $tb = new TableObj($R, $MergedFD2);
    array_push($NFTables, $tb);

    $p = count($NFTables);

    return $NFTables;
}
function ToBCNF($F, $R) {
    $NFTables = array();
    $MC = findMergedMC($F);
    // find LHS of FDs-- the set of attributes on LHS of some FD. Will be used to generate FDs for new tables  
    $LHS = array();
    foreach ($MC as $fd) {
        $LHS = setUnion($fd->ls, $LHS);
    }
    $rel = array($R);  //set of tables, initially contains a single table R 
    $FDs = array($MC); // corresponding FD set
    $i = 0;
    $K = 1; // the number of tables in $rel

    while ($i < $K) {
        //   echo "<br> i=".$i." and K=".$K."<br>"; 
        if (isBCNF($FDs[$i], $rel[$i])['isNormalized']) {
            //       echo "hello BCNF"; printset($rel[$i]); echo "<br>";
            $ff = findMergedMC($FDs[$i]);
            $tb = new TableObj($rel[$i], $ff);
            array_push($NFTables, $tb);
        } else {
            $K = $K + 2;

            $fs = findMergedMC($FDs[$i]);

            $tbs = decomposeBCNF($fs, $rel[$i]);

            array_push($rel, $tbs[0]);          // add two relations at the end of the list rel
            array_push($rel, $tbs[1]);

            array_push($FDs, array());
            array_push($FDs, array());


            for ($w = $K - 2; $w <= $K - 1; $w++) {

                $B = setIntersect($rel[$w], $LHS);  //B is the intersection of LHS and rel(w)
                $subs = findSubset($B);

                foreach ($subs as $subset) { 

                    $A = findClosureSet($subset, $MC);   //closesure of the subset 
                    $B = setDiff($subset, $A);           //closeset minus the subset
                    $D = setIntersect($rel[$w], $B);   // D is the set of attributes in rel(w) that are dependent on the subset

                    if (count($D) > 0) {
                        $f = new FD($subset, $D);
                        array_push($FDs[$w], $f);      // a new FD is added to FDs(w)
                    }
                }
            }
        } //else
        $i++;  //next iteration
    } //end of while
    return $NFTables;
}
function To2NF($F, $R) {
    $NFTables = array();
    $MC = findMiniCover($F);
    // find LHS of FDs-- the set of attributes on LHS of some FD. Will be used to generate FDs for new tables  
    $LHS = array();
    foreach ($MC as $fd) {
        $LHS = setUnion($fd->ls, $LHS);
    }
    $rel = array($R);  //set of tables, initially contains a single table R 
    $FDs = array($MC); // corresponding FD set

    $i = 0;
    $K = 1; // the number of tables in $rel
    while ($i < $K) {
        if (is2NF($FDs[$i], $rel[$i], false)) {
            $fs = findMergedMC($FDs[$i]);
            $tb = new TableObj($rel[$i], $fs);
            array_push($NFTables, $tb);
        } else {
            $K = $K + 2;
            $tbs = decompose2NF($FDs[$i], $rel[$i]);

            array_push($rel, $tbs[0]); // this table is actually already in 2NF, but it is OK to check again as efficiency is not a problem. 
            array_push($rel, $tbs[1]); // add one relation at the end of the list rel
            array_push($FDs, array());
            array_push($FDs, array());

            //  echo "hello 3";
            for ($w = $K - 2; $w <= $K - 1; $w++) {
                $B = setIntersect($rel[$w], $LHS);
                $subs = findSubset($B);
                foreach ($subs as $subset) {
                    $A = findClosureSet($subset, $MC);   //closesure of the subset 
                    $B = setDiff($subset, $A);           //closeset minus the subset
                    $D = setIntersect($rel[$w], $B);   // D is the set of attributes in rel(w) that are dependent on the subset
                    if (count($D) > 0) {
                        $f = new FD($subset, $D);
                        array_push($FDs[$w], $f);      // a new FD is added to FDs(w)
                    }
                }
            }
        } //else
        $i++;  //next iteration
    } //end of while

    return $NFTables;
}



function decomposeBCNF($F, $T) {
    // F should be a minimal cover
    $tbs = array();  // return a set of attribute sets, no FDs
    $Split = true;
    foreach ($F as $fd) {
        if ($Split) {
            if (!isSuperKey($fd->ls, $F, $T)) {

                $A = $fd->ls;
                $B = $fd->rs;
                $tb1 = setUnion($B, $A);
                $tb2 = setDiff($fd->rs, $T);
                array_push($tbs, $tb1);
                array_push($tbs, $tb2);
                $Split = false;
            }
        }
    }
    return $tbs;
}
function decompose2NF($F, $R) {
    $tbs = array();  // return a set of attribute sets, no FDs
    $C = findMiniCover($F);
    $CK = findAllCK($F, $R);
    $keyAttr = array();  // key attrbutes

    foreach ($CK as $set) {
        $keyAttr = setUnion($set, $keyAttr);
    }

    $R1 = $R;
    $NKEY = setDiff($keyAttr, $R1);   // non-key attributes
    //  echo "Non key "; printset($NKEY); 
    $Split = true;
    foreach ($C as $fd) {

        if ($Split) { //echo " within Split ";
            if (!isSuperKey($fd->ls, $C, $R) and isSubset($fd->rs, $NKEY)) { // echo " within if ";
                foreach ($CK as $key) {
                    if (isSubset($fd->ls, $key) and $Split) {  //a proper subset of $key because it is not a superkey // echo " most internal if ";
                        $A = $fd->ls;
                        $X = findClosureSet($A, $C);

                        $Y = setDiff($A, $X);
                        $B = setDiff($keyAttr, $Y);  // B is the set of all non-key attributes dependent on fd->ls and not in fd->ls

                        $tb1 = setUnion($B, $A);
                        $tb2 = setDiff($B, $R);
                        
                        array_push($tbs, $tb1);
                        array_push($tbs, $tb2);

                        $Split = false;
                    }
                }
            }
        }
    }
    return $tbs;
}


function findClosureSet($A, $F) {
    //A is the attrbute set, F is the FD set
    $N = count($F) - 1;
    $CLset = $A;
    $v = true;
    while ($v) {
        $K = count($CLset);

        for ($i = 0; $i <= $N; $i++) {
            if (isSubset($F[$i]->ls, $CLset)) { // echo "inside if";
                $s = setUnion($F[$i]->rs, $CLset);
                $CLset = $s;
            }
        }
        if ($K == count($CLset)) {
            $v = false;
        }
    }
    return $CLset;
}
/* * **** tests whether FDs E implies FD f ********************* */
function implify($E, $f) {
    $Clos = findClosureSet($f->ls, $E);
    if (isSubset($f->rs, $Clos)) {
        $val = true;
    } else {
        $val = false;
    }
    return $val;
}
function findMiniCover($F) {

    $E1 = array();
    /* split  RHS  */
    foreach ($F as $fd) {
        $fd->minimizeRs();
        foreach ($fd->rs as $attr) {
            $f = new FD($fd->ls, array($attr));
            array_push($E1, $f);
        }
    }
    // minimize LHS of each FD, 
    foreach ($E1 as $fd) {
        $ls = $fd->ls;
        $rs = $fd->rs;

        $f = new FD($ls, $rs);
        // the original code below is oK 
        foreach ($fd->ls as $attr) {
            $f->ls = removeElement($attr, $f->ls);

            if (implify($E1, $f)) {
                $fd->ls = $f->ls;
            } else {
                $f->ls = $fd->ls;
            }
        }
    }
    /* remove redundant FDs */
    foreach ($E1 as $fd) {
        $ls = $fd->ls;
        $rs = $fd->rs;
        $f = new FD($ls, $rs);
        $fd->rs = array(); // mark as removed
        if (!implify($E1, $f)) {
            $fd->rs = $rs;
        }
    }
    /*     * ** remove those FDs where the RHS is empty*** */
    $K = count($E1) - 1;
    $j = 0;
    while ($j <= $K) {
        if (count($E1[$j]->rs) == 0) {
            for ($K1 = $j; $K1 <= $K - 1; $K1++) {
                $E1[$K1] = $E1[$K1 + 1];
            }
            $K--;
            $j--;
            array_pop($E1);
        }
        $j++;
    }
    return $E1;
}
function findMergedMC($F) {
    $C = findMiniCover($F);
    $MC = array();
    $k = count($C);
    for ($i = 0; $i < $k; $i++) {
        if ($C[$i]->rs <> "999.0137319") {
            array_push($MC, $C[$i]);

            $k1 = count($MC) - 1;

            for ($j = $i + 1; $j < $k; $j++) {

                if (isSubset($C[$i]->ls, $C[$j]->ls) and isSubset($C[$j]->ls, $C[$i]->ls)) {
                    $MC[$k1]->rs = setUnion($C[$j]->rs, $MC[$k1]->rs);

                    $C[$j]->rs = "999.0137319";     /* mark as  deleted */
                }
            }
        }
    }
    return $MC;
}
