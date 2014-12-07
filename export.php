<?php

if ($_GET['secret'] != 'suning') {
    die('illegal access');
}

require 'utils.php';
initRedbean();
require_once 'lib/PHPExcel.php';

$users = R::findAll('user');
$data = [];
$data[] = ['姓名', '电话', '地址'];
$prizes = range(3, 7);
foreach ($users as $user) {
    $row = [$user->name, $user->mobile, $user->addr];
    foreach ($prizes as $prize) {
        $row[] = $user["prize$prize"];
    }
    $data[] = $row;
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Kail")
            ->setTitle('苏宁时光用户数据')
            ->setSubject('苏宁时光用户数据');
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->setTitle('数据')->fromArray($data, null, 'A1');
$index = 2;
foreach ($users as $user) {
    $sheet->setCellValueExplicit(
        'B' . $index, $user->mobile, \PHPExcel_Cell_DataType::TYPE_STRING
    );
    ++$index;
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$fileName = 'sntime.xls';
$objWriter->save("cache/$fileName");

header("Content-disposition: attachment; filename=$fileName");
header("Content-type: text/vnd.ms-excel");
readfile("cache/$fileName");