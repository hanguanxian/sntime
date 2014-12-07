<?php

require 'utils.php';
initRedbean();

/*
$user = R::dispense('user');
$user->name = 'kail';
$user->mobile = '18621586912';
$user->addr = 'asd';
$user->createdAt = R::isoDateTime();
R::store($user);*/

if ($_POST['submit']) {
    $today = date('Y-m-d');
    $result = R::getCell('SELECT COUNT(id) FROM user WHERE DATE(created_at) = ?', [ $today ]);
    if ($result >= 100) {
        // 到上限了
    }
    // redirect
}

render('getbox.html.twig');