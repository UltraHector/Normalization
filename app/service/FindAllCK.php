<?php

require_once (dirname(__FILE__).'/../entity/FD.php');
require_once (dirname(__FILE__).'/../entity/TableObj.php');
require_once (dirname(__FILE__).'/../entity/Sets.php');

require_once ('FunctionalID.php');


function findAllCK($F, $R) {

    $result = array();
    $result['steps'] = array();

    $Cover = findMiniCover($F)['miniCover'];


    $result['steps']['step1'] = "Step 1: Find the minimal cover of FDs, which contains <br>";

    foreach ($Cover as $fd) {
        $result['steps']['step1'] = $result['steps']['step1'] . $fd->printMe() . "<br>";
    }



    // find the set of attributes on RHS and the set of attributes on LHS of some FDs  
    $RHS = array();
    $LHS = array();

    foreach ($Cover as $fd) {
        $RHS = setUnion($fd->rs, $RHS);

        $LHS = setUnion($fd->ls, $LHS);
    }

    if (!isSubset($RHS, $R) or ! isSubset($LHS, $R)) {
        $result['status'] = false;
        $result['errorMessage'] = "Input error-- some attributes in the FDs are not in the table.";
    }

    $x = $R;  // prevent $R from being changed


    $NRHS = setDiff($RHS, $x);  // attributes not on RHS of any FD. Every CK should contain these attributes
//    printset($NRHS);

    $result['steps']['step2'] = "Step 2. Find the set of attributes not on the RHS of any FD, which is NotOnRHS = {" . printset($NRHS) . "}. Every CK must contain these attributes.";



    $CK = array();

    if (isSuperKey($NRHS, $F, $R)) {

        array_push($CK, $NRHS);   // $NRHS is the unique candidate key in this case

        $result['steps']['step3'] = "Step 3: NotOnRHS is a superkey, so it is the only candidate key";
        $result['candidateKeys'] = $CK;
        
        return $result;
    } else {

        $G = array(array(), array()); // G is an array of array. G[0] is empty array G[i] is used to store 1-attribute sets.  
        // find G[1], which consists of attributes not on RHS of any FD

        $x = $RHS;
        $RHSButNotLHS = setDiff($LHS, $x); //attributes on RHS but not on LHS. No CK will have these attributes

        $result['steps']['step2'] = $result['steps']['step2'] . "<br> Find the set of attributes that appeared on the RHS of some FD, but not on the LHS of any FD, which is OnRHSNotOnLHS= {" . printset($RHSButNotLHS) . "}";
        $result['steps']['step2'] = $result['steps']['step2'] . "<br> Attributes in OnRHSNotOnLHS cannot be in any candidate key";

        $x = $R;
        $NORHS = setDiff($RHSButNotLHS, $x); // attributes not only on RHS 
        $Y = setDiff(findClosureSet($NRHS, $Cover), $NORHS); //R-ClosureSet(NotOnRHS)-OnRHSNotOnLHS


        $result['steps']['step3'] = "Step 3: Find the closureset of NotOnRHS which is ClosureSet(NotOnRHS)= {" . printset(findClosureSet($NRHS, $Cover)) . "}";

        $result['steps']['step4'] = "Step 4: We try to add one attribute from R-OnRHSNotOnLHS-ClosureSet(NotOnRHS) to check whether it is a superkey,if yes, we check whether it is a candidate key by checking whether it has a proper subset
                         which is also a superkey; if not we add more attributes... untill we have checked all possilibities. This can be implemented in different ways.";


        foreach ($Y as $attr) {
            $X = $NRHS;

            if (!isElement($attr, $X)) {
                $X = addElement($attr, $X);

                $result['steps']['step4'] = $result['steps']['step4'] . "<br> Checking the set NotOnRHS UNION { " . $attr . " }";

                if (isSuperKey($X, $Cover, $R)) {

                    $result['steps']['step4'] = $result['steps']['step4'] . "<br> The above set is a superkey, and it is also a candidate key (do not need to check its subsets in this case)";
                    array_push($CK, $X);  //X is a (count+1)-element CK, where count= count($NRHS)
                } else {
                    $result['steps']['step4'] = $result['steps']['step4'] . "<br> The above set is not a superkey, so it is not a candidate key.";
                    array_push($G[1], array($attr));  //G[1] contains 1-attrubte sets that do not form CKs together with $NRHS
                }
            }
        }
    }

// echo "<br> <br> Step 5: Let G[k] be the set which contains the k-attribute sets that do not form superkeys together with NotOnLHS;
//      If G[k] has more than 1 set, we must generate G[k+1]. Otherwise we stop.";
// echo "<br> For every pair of sets in G[k], we generate a set in G[k+1] by merging the pair if their first k-1 elements are the same, and check whether it is a superkey, if yes, we further check whether it is a CK, other wise we put it into G[k+1]";
    // next generate  k+1 element set from k elemnet sets           

    $val = true;
    $k = 1;

    /*
      echo "<br><br>  G[1] = { ";
      foreach ($G[1] as $SingletonSet){
      echo "("; printSet($SingletonSet); echo")";
      echo "  ";
      }
      echo " }";
     */
    while (count($G[$k]) > 1) {

// echo "<br> <br> G[".$k."] has more than 1 set";

        $k++;
        array_push($G, array());  // $G[k] is initially empty


        for ($i = 0; $i <= count($G[$k - 1]) - 2; $i++) {
            $A = $G[$k - 1][$i];

            for ($j = $i + 1; $j <= count($G[$k - 1]) - 1; $j++) {
                $B = $G[$k - 1][$j];

                array_pop($A);  //remove last element from $A
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

                    $Z = setUnion($BB, $NRHS);

                    // adding the next checking of $aCKisSubset to avoid the original checking of $asubsetIskey commented out by /* */
                    // within the if (isSuperKey($Z,$Cover,$R) below 

                    $aCKisSubset = false;
                    foreach ($CK as $set) {
                        if (isSubset($set, $Z)) {
                            $aCKisSubset = true;
                            $Ckey = $set;
                            break;
                        }
                    }

                    $result['steps']['step4'] = $result['steps']['step4'] . "<br> Checking the set ( " . printSet($Z) . " )";
                    /*
                      if ($aCKisSubset)
                      {
                      echo "<br> The smaller size CK ("; printSet($Ckey); echo ") as its subset, so it is not a candidate key, and can be ignored.";
                      }
                      else
                      {
                      echo "<br> No small size CK is its subset, ";
                     */
                    if (isSuperKey($Z, $Cover, $R)) {  // if Z is a superkey 

                        $result['steps']['step4'] = $result['steps']['step4'] . "<br> The above set is a superkey, ";

                        //the following checking is necessary, as shown in test case 13.
                        // but we only need to test subsets of $BB that contains both 
                        //$G[$k-1][$i][$k-2] and $G[$k-1][$j][$k-2], i.e, the last two elements of $Z. This is not imlemented here for simplicity
                        // alternatively, we only need to text whether there is an existing CK in $CK that is a subset of $Z,
                        // and we can do this test before checking whether $Z is a superkey.  (This alternative is ) 

                        if (!$aCKisSubset) {
                            $result['steps']['step4'] = $result['steps']['step4'] . " and no proper subset of it is a superkey, hence it is a candidate key.";
                            array_push($CK, $Z);
                        } else {
                            $result['steps']['step4'] = $result['steps']['step4'] . "but it has a subset (" . printSet($Ckey) . ") which is also a superkey, thus it is not a candidate key. ";
                        }

                        /*
                          $asubsetIskey= false;
                          $D = findSubset($BB);  //find subsets of $BB

                          foreach($D as $attrSet)
                          {
                          if (!isSubset($BB, $attrSet))  //only check proper subsets
                          {
                          $Z1=setUnion($NRHS, $attrSet);

                          if (isSuperKey($Z1, $Cover, $R))
                          {
                          $asubsetIskey= true;
                          }
                          }
                          }

                          if (!$asubsetIskey)  // Z is a CK because so proper subset is key
                          {
                          echo "<br> The above set is indeed a candiate key.";
                          array_push($CK, $Z);
                          }
                          else
                          {
                          echo "<br> The above set is not a candiate key because a proper subset of it is a superkey.";
                          }
                         */
                    }//  
                    else { // NRHS union B is not a superkey, add B to G[k]
                        $result['steps']['step4'] = $result['steps']['step4'] . "<br> The above set is not a superkey,  so it is not a candidate key.";
                        array_push($G[$k], $B);
                    } //(isSuperKey($Z,$Cover,$R))
                    //   } //if ($aCKisSubset)
                } // if(isSubset($A,$B) and isSubset($B,$A))
            } //for
        }  //for


        /*
          echo "<br> G[".$k."]= {";
          foreach ($G[$k] as $set)
          {echo "("; printSet($set); echo ")";  echo "  ";}
          echo " }";
         */
        if (count($G[$k]) <= 1) {
            $result['steps']['step4'] = $result['steps']['step4'] . "<br> We can stop now.";
        }
    } // while

    $result['candidateKeys'] = $CK;
    return $result;
} //end of findAllCk


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





