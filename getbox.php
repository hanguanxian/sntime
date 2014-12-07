<?php

require 'utils.php';
initRedbean();

if ($_POST['submit']) {
    $today = date('Y-m-d');
    $result = R::getCell('SELECT COUNT(id) FROM user WHERE DATE(created_at) = ?', [ $today ]);
    if ($result >= 100) {
        redirect('getboxresult.php?r=2');
    }
    
    $mobile = $_POST['mobile'];
    $user = R::findOne('user', ' mobile = ? ', [ $mobile ]);
    if ($user) {
        // 已经领过了
        redirect('getboxresult.php?r=3');
    }
    
    $user = R::dispense('user');
    $user->name = $_POST['name'];
    $user->mobile = $mobile;
    $user->addr = $_POST['addr'];
    $user->createdAt = R::isoDateTime();
    R::store($user);
    
    redirect('getboxresult.php?r=1');
}

render('getbox.html.twig');