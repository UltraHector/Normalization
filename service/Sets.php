<?php
/* my own utility functions */
function isSubset($A, $B) {
    /* A and B are two arrays */
    /* testing whether A is a subset of B */
    foreach ($A as $value) {
        if (!in_array($value, $B)) {

            return false;
        }
    }
    return true;
}

///////////////////////////////////////////////////
function removeElement($x, $B) {
    // remove element $x from array $B
    $k = count($B) - 1;
    $i = 0;
    while ($i <= $k) {

        if ($x == $B[$i]) {
            // echo $B[$i];
            for ($j = $i; $j <= $k - 1; $j++) {
                $B[$j] = $B[$j + 1];
            }
            array_pop($B);
            $k--;
            $i--;
        }
        $i++;
    }
    return $B;
}

/////////////////////////////////////////////
//compute $B-$A 
function setDiff($A, $B) {
    foreach ($A as $x) {
        $B = removeElement($x, $B);
    }
    return $B;
}

/////////////////////////////////////////////

function isElement($x, $B) {
    $b = in_array($x, $B);
    return $b;
}

function addElement($x, $B) {
    if (!in_array($x, $B)) {
        array_push($B, $x);
    }
    return $B;
}

function setUnion($A, $B) {
    foreach ($A as $v) {
        if (!in_array($v, $B)) {

            array_push($B, $v);
        }
    }
    return $B;
}

//////////////////////////////////////////
function setIntersect($A, $B) {
    $C = array_intersect($A, $B);
    $D = array();
    foreach ($C as $value) {
        array_push($D, $value);
    }
    return $D;
}

/* * ************************************************************************************** */

function findUnionSetofSet($A, $B) {
    $U = $A;
    foreach ($B as $set1) {
        $bool = true;

        foreach ($A as $set2) {
            if (isSubset($set1, $set2) and isSubset($set2, $set1)) {
                $bool = false;
            }
        }
        if ($bool) {
            array_push($U, $set1); /* set1 not exist in A */
        }
    }
    return $U;
}

/* * ************************************************************************************** */

/** Find all subsets of  a set AA * */
function findSubset($A) {

    /* G(k-1) is set of k-element sets */

// find 1-element sets first

    $G = array();

    $GG = array();
    foreach ($A as $value) {
        array_push($GG, array($value));
    }

    array_push($G, $GG);      //first element of $G is the 1-itemsets $GG
    // array_push($G, $GG);      // now both G[1] and G[0] store the 1-itemsets

    $k = 0;
    $S = $G[0];
    $N = count($A);

    while ($k <= $N - 1 and count($G[$k]) > 1) {  // $G[$N-1] = $A
        // generate k-itemsets from k-1 itemsets;
        array_push($G, array());
        $k++;
        $M = count($G[$k - 1]);
        // echo "M=".$M;
        for ($i = 0; $i <= $M - 2; $i++) {
            //  echo "i=".$i;

            for ($j = $i + 1; $j <= $M - 1; $j++) {
                //  echo "j=".$j."<br>";

                $X = $G[$k - 1][$i];

                $B = $G[$k - 1][$j];           //for every pair of k-1 element sets X and B 

                array_pop($X);
                array_pop($B);

                if (isSubset($X, $B) and isSubset($B, $X)) {
                    //$A and B are k-1 itemsets

                    if ($G[$k - 1][$i][$k - 1] <= $G[$k - 1][$j][$k - 1]) {   //$G[$k-1][$i] is an k element set, $G[$k-1][$j][$k-1] is the last element// keep A sorted 
                        array_push($X, $G[$k - 1][$i][$k - 1]);
                        array_push($X, $G[$k - 1][$j][$k - 1]);
                    } else {
                        array_push($X, $G[$k - 1][$j][$k - 1]);
                        array_push($X, $G[$k - 1][$i][$k - 1]);
                    }                                       //A is a k-attribute set, and the elements are sorted 

                    array_push($G[$k], $X);
                }
            }
        }
    }
    for ($i = 0; $i <= count($G) - 1; $i++) {  // $S initially was $G[0];
        $S = findUnionSetofSet($S, $G[$i]);
    }
    return $S;
}

function printset($A) {
    $result = "";
    $k = count($A);
    for ($i = 0; $i < $k - 1; $i++) {
        $result += $A[$i] . ",";
    }
    $result += $A[$k - 1];
}
