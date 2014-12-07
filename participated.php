<?php

require 'utils.php';
initRedbean();

$rank = 1024;
render('participated.html.twig', [
    'rank' => $rank
]);