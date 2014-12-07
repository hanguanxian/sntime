<?php

require 'utils.php';
initRedbean();

// 领过，没领过，到达本日上限
$result = $_GET['r'];
render('getboxresult.html.twig');