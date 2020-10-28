<?php
require_once '../vendor/autoload.php';
require '../dependencies/fpdf/fpdf.php';

use Models\MasterReport;
use Models\Luf10783;
use Models\IseAndSat;

//Override runtime execution
set_time_limit(999999);

//To calculate the execution time
$start = time();


/**
 * //Alternative to read all files in a folder and subfolders. No needed for now.
 * //function getDirContents($dir, &$results = array()) {
 * //    $files = scandir($dir);
 * //
 * //    foreach ($files as $key => $value) {
 * //        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
 * //        if (!is_dir($path)) {
 * //            $results[] = $path;
 * //        } else if ($value != "." && $value != "..") {
 * //            getDirContents($path, $results);
 * //            if(is_file($path)) {
 * //                $results[] = $path;
 * //            }
 * //        }
 * //    }
 * //
 * //    return $results;
 * //}
 */

//$locations = [
//    '10645',
//    '10497',
//    '10513',
//    '10523',
//    '10562',
//    '10783',
//];

$names = [
    "10645" => "LUF-10645 Casino Skampa",
//    "10487" => "LUF-10487",
    "10497" => "LUF-10497 Casino Midas Mazatlan",
    "10513" => "LUF-10513 Casino Entretenimiento Palermo Nogales",
    "10523" => "LUF-10523 Casino Midas Agua Prieta",
//    "10525" => "LUF-10525 Casino Emine San Luis",
    "10562" => "LUF-10562 Casino Dardania Guamuchil",
    "10783" => "LUF-10783 Casino Midas Rosarito"
];

foreach ($names as $location=>$name) {

//$basePath = 'C:/wamp64/www/lufta-php/csv/';
    $csvDir = "../filesToMerge/toPrint/{$location}.csv";

//Scan dir and get the file
    $results = ($csvDir);
    /**
     * //For each file...
     * //foreach ($results as $result) {
     *
     * //$result = str_replace('\\', '/', $result);
     *
     * //$all = explode($basePath, $result);
     * //$real = explode('/', $all[1]);
     *
     * //    $month = $real[0];
     * //    $location = $real[1];
     * //    $pos = (explode('.', $real[2]))[0];
     */
//Start processing the file
    $delimiter = ";";
    $month = 'SEPTEMBER 2020';
    $pos = '';
    $file = fopen($csvDir, "r");
    $fileName = $name . '-' . $month;



//Ticket dimensions
    $width = 70;
    $height = 5;

    $pdf = new FPDF();

//Beginning of the tickets. Creating the pdf
    $fontFamily = 'Courier';

//First page
    $pdf->AddPage();

    $pdf->Image("../assets/imgs/axeslogo.jpg", 6, 0, 200, 100);
    $pdf->SetFont('Helvetica', '', 16);
    $pdf->SetY(80);

    $pdf->SetLeftMargin(20);
    $pdf->MultiCell(175, $height, 'POS Tickets report', 0, 'C');
    $pdf->Line(20, 86, 190, 86);
    $pdf->Ln();
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->MultiCell(100, $height, "Location: {$names[$location]}", 0, 'L');
    $pdf->MultiCell($width, $height, "Date: {$month}", 0, 'L');
    $pdf->Ln();
    $pdf->SetFont($fontFamily, '', 10);
    $pdf->SetLeftMargin(80);
    $pdf->SetRightMargin(100);

    $i = 0;
    while (($csv = fgetcsv($file, "", $delimiter)) !== FALSE) {
        if ($i > 0) {

            $report = new IseAndSat();

            if($ticket = $report->build($csv, $location)){

                //Add ticket page
                $pdf->AddPage();

                $header = $ticket['header'];
                $subheader = $ticket['subheader'];

                $pdf->SetFont($fontFamily, '', 12);
                $pdf->MultiCell($width, $height, $header['text'], 0, 'C');
                $pdf->SetFont($fontFamily, 'B', 10);
                $pdf->MultiCell($width, $height, $subheader['text'], 0, 'C');
                $body = $ticket['body'];

                foreach ($body as $key => $section) {
                    $pdf->MultiCell($width, 3, ' ', 0, 'C');
                    $pdf->SetFont($fontFamily, 'B', 10);
                    $pdf->MultiCell($width, 3, $key, 0, 'C');

                    foreach ($section as $title => $content) {
                        $pdf->SetFont($fontFamily, '', 10);
                        $cellWidth = ($width/2);

                        if($title !== ''){
                            $left = $pdf->Cell($cellWidth, 2, $title . '' . ':' . ' ', 0, 0, '');
                            if($title == 'Cliente'){
                                $pdf->MultiCell($width, 3, $left, 0, 'C');
                                $pdf->MultiCell($width, 3, $content, 0, 'R');
                            }
                            else{
                                $right = $pdf->Cell($cellWidth, 2, $content, 0, 0, 'R');
                                $pdf->MultiCell($width, 3, $left . $right, 0, 'C');
                            }

                            $pdf->MultiCell($width, 3, ' ', 0, 'C');
                        }

                    }
                }

                $pdf->MultiCell($width, 3, ' ', 0, 'C');
                $footer = $ticket['footer'];
                $pdf->SetFont($fontFamily, '', 10);
                $pdf->MultiCell($width, 4, $ticket['footer']['text'], 0, 'C');

            }
        }
        $i++;
    }
    $pdf->Output('F', "printed/{$fileName}.pdf", true);
    fclose($file);

}
//}

$total = time() - $start;
echo "Time: " . $total;


