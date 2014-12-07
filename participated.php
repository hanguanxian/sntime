<?php

require 'utils.php';
initRedbean();

$rank = R::findOne('rank', ' name = ? ', [ 'rank' ]);
if (!$rank) {
    $rank = R::dispense('rank');
    $rank->name = 'rank';
    $rank->rank = 1;
}

// 检查是不是摁手印过来的
if ($_SERVER['HTTP_REFERER'] != 'http://neone.com.cn/sntime/participate.php') {
    $rankNum = $rank->rank;
} else {
    $rankNum = $rank->rank + 1;
    $rank->rank = $rankNum;
    R::store($rank);
}

render('participated.html.twig', [
    'rank' => $rankNum
]);