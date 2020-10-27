<?php
require_once '../auxiliar/customMethods.php';
require_once '../vendor/autoload.php';

use Models\RemoveValue;
use Models\AddValue;
use Models\IseAndSat;


$delimiter = ';';
$path = '../filesToMerge/';
$files = [
    "addValue" => "{$path}addValue.csv",
    "removeValue" => "{$path}removeValue.csv",
    "iseAndSat" => "{$path}iseAndSatReport.csv"
];

$addValue =[];
$removeValue =[];
$iseAndSat =[];

foreach ($files as $type=>$address){
    $readStart = time();
    $file = fopen($address, "r");



    //Build an array for each report
    while(($load = fgetcsv($file, "", $delimiter)) !== FALSE){
        switch ($type){
            case "addValue":
                $report = new AddValue();
                $report->build($load);
                $arrayReport = ($report->toArray())['field'];
                $addValue[] = $arrayReport;
                break;
            case "removeValue":
                $report = new RemoveValue();
                $report->build($load);
                $arrayReport = ($report->toArray())['field'];
                $removeValue[] = $arrayReport;
                break;
            case "iseAndSat":
                $report = new IseAndSat();
                $report->build($load);
                $arrayReport = ($report->toArray())['field'];
                $iseAndSat[] = $arrayReport;
                break;
        }
    }


    $readEnd = time()-$readStart;
    echo '<br>';
    echo "Execution time : {$readEnd} s";
    echo '<hr>';
}

//    header('Content-Type: text/csv');
//    header('Content-Disposition: attachment; filename="sample.csv"');

//var_dump($addValue);

$v=0;
//    $fp = fopen('php://output', 'wb');
    foreach($iseAndSat as $key=>$field) {
        $id = $field['IdOfEvento'];
        $saldo=$field['SaldoInitial'];
        $premio = $field['MontoDePremioPagado'];

        $key = array_search($id, array_column($addValue, 'TransactionNumber'));
        if($key !== FALSE){
            echo '<br>';
            echo ($id.'-->'.$key.' AddValue transaction - Saldo inicial: '.$saldo.' | Deposito: '.$addValue[$key]['Deposit']);
        }
        else{
            $key = array_search($id, array_column($removeValue, 'TransactionNumber'));
            if($key !== FALSE){
                echo '<br>';
                echo ($id.'-->'.$key.'RemoveValue transaction - MontoDePremioPagado: '.$premio.' | RemoveValue: '.$removeValue[$key]['RemoveValue']);
            }
        }
////        $val = explode(",", $line);
//        fputcsv($fp, $line);
        $v++;
        if($v>50){
            die();
        }
    }
//    fclose($fp);