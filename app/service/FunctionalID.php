<?php
require_once (dirname(__FILE__).'/../entity/FD.php');
require_once (dirname(__FILE__).'/../entity/Sets.php');


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
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}


function findMiniCover($F) {
    
    $result = array();
    $result['steps'] = array();

    $result['miniCover'] = array();
    /* split  RHS  */
    foreach ($F as $fd) {
    //  $fd->minimizeRs();  //remove attributes on RHS that is also on LHS
        foreach ($fd->rs as $attr) {
            $f = new FD($fd->ls, array($attr));
            array_push($result['miniCover'], $f);
        }
    }
    $result['steps']['step1'] = $result['miniCover'];

    $result['miniCover'] = array();
    foreach ($F as $fd) {   // do it again just to show the step to remove trivial FDs
        $fd->minimizeRs();  //remove attributes on RHS that is also on LHS
        foreach ($fd->rs as $attr) {
            $f = new FD($fd->ls, array($attr));
            array_push($result['miniCover'], $f);
        }
    }

    $result['steps']['step2'] = $result['miniCover'];

// minimize LHS of each FD, 
    foreach ($result['miniCover'] as $fd) {
        $ls = $fd->ls;
        $rs = $fd->rs;
        $f = new FD($ls, $rs);  //make a copy of the original FD
        // the original code below is oK 
        foreach ($fd->ls as $attr) {
            $f->ls = removeElement($attr, $f->ls);

            if (implify($result['miniCover'], $f)) {
                $fd->ls = $f->ls;
            } else {
                $f->ls = $fd->ls;
            }
        }
    }

    $result['steps']['step3'] = $result['miniCover'];
    
    /* remove redundant FDs */
    foreach ($result['miniCover'] as $fd) {
        $ls = $fd->ls;
        $rs = $fd->rs;
        $f = new FD($ls, $rs);  //make a copy of the original FD

        $fd->rs = array(); // mark as removed by making RHS is empty

        if (!implify($result['miniCover'], $f)) {
            $fd->rs = $rs;
        }
    }
    /*     * ** remove those FDs where the RHS is empty*** */
    $K = count($result['miniCover']) - 1;
    $j = 0;
    while ($j <= $K) {
        if (count($result['miniCover'][$j]->rs) == 0) {
            for ($K1 = $j; $K1 <= $K - 1; $K1++) {
                $result['miniCover'][$K1] = $result['miniCover'][$K1 + 1];
            }
            $K--;
            $j--;
            array_pop($result['miniCover']);
        }
        $j++;
    }


    $result['steps']['step4'] = $result['miniCover'];

    return $result;
}


function findMergedMC($F) {
    $C = findMiniCover($F)['miniCover'];
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
