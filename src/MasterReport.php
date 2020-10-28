<?php

namespace Models;

use \GuzzleHttp;
use http\Client;
use \DateTime;

class MasterReport
{
    public $field = [];
    private $location = "";

    public function __constructor()
    {

    }

//    public function build($csvArray){
//        $this->setProperties($this->propertiesMap(), $csvArray);
//    }

    public function toArray(){
        return (array) $this;
    }

    /**
     * @param number $number
     * @return string
     */
    public function formatNumber($number){
        return number_format($number, 2, '.', '');
    }

    /**
     * @param string $location
     */
    public function setLocation($location){
       $this->location = $location;
    }

    public function getLocation(){
        return $this->location;
    }

    public function setProperties($properties, $data)
    {
        if (count($properties) === count($data)) {
            $dataIndex = 0;
            foreach ($properties as $index) {
                $type = $index['type'];

                if (!($data[$dataIndex])) {
                    $val = '';
                } else {
                    $val = $this->parseProperty($data[$dataIndex], $type);
                }

                $this->field[$index['field']] = $val;
                $dataIndex++;
            }
        } else {
            echo "Unable to set property";
        }
    }

    public function parseProperty($val, $type)
    {
        switch ($type) {
            case "int":
                $newVal = $val !=='' ? intval($val) : false;
                break;
            case "float":
                    if($match = preg_match('/\d+,\d+.\d+/', $val)){
                        $newVal = floatval(str_replace(',', '', $val));
                    }
                    else{
                        $newVal = floatval(str_replace(',', '.', $val));
//                        $newVal = $val !=='' ? (float) number_format($val, 2) : false;
                    }
//                if((strpos($val, ',') !==FALSE) && (strpos($val, '.') !==FALSE)){
//
//                }
//                var_dump($val);
                break;
            default:
                $newVal = $val !=='' ? ($val) : false;
                break;
        }

        return $newVal;
    }

    /**
     * @param $config
     * @param string $date
     * @return mixed
     */
    public function config($config, $date="")
    {

        $location = $this->getLocation();
        $start = "CASINO MIDAS\n ENTRETENIMIENTOS NATURALES \n S.A. DE C.V. \n RFC: ENA180908T38 \n SUCURSAL";
        $end = "PERMISO SEGOB \n No. 8.S.7.1/DGG/SN/94 \n Oficio Autorizacion \n No. DGJS/282/2018";

        $default = "PERMISIONARIA COMERCIALIZADORA DE ENTRETENIMIENTO DE CHIHUAHUA S.A DE C.V AV MANUEL GOMEZ MORIN 1101 PTE. INTL 211 COL. CARRIZALEJO \n NUEVO LEON \n CP 66254 \n RFC CEC050121415 \n OPERADORA TOP ASESORES DE ENTRETENIMIENTO, S.A. DE C.V. \n PERMISO SEGOB: \n DGAJS/SCEVF/P-08/2005\n RFC:TAE1507079C4\n CALLE VALLE SOL #122, INT 302 COL. LA DIANA CP 66566 SAN PEDRO GARZA GARCIA N.L.";

        $locations = [
            "10645" => [//Checked Skampa
                'header'=> $default,
                'footer'=>"sucursal fiscal: SKAMPA CASINO \n BLVD. COSTERO Y SANGINES 6-A \n COL CARLOS PACHECO CP.22880 \n ENSENADA BAJA CALIFORNIA",
                'tax'=>0.05
            ],
            "10487" => [//Checked
                'header'=> $default,
                'footer'=>'',
                'tax'=>0
            ],
            "10513" => [//Checked
                'header'=> $default,
                'footer'=>"sucursal fiscal:CASINO PALERMO \n Blvd. Luis Donaldo Colosio 2693 \n Col. Kennedy Nogales Sonora \n CP 84063",
                'tax'=>0
            ],
            "10525" => [//Emine San Luis
                'header'=> $default,
                'footer'=>"Sucursal Fiscal: Casino Emine \n Av. Revolucion #200 col. \n Comercial     CP:83449 \n San Luis Rio Colorado. Sonora",
                'tax'=>0
            ],
            "10497" => [//Midas Mazatlan
                "header"=>"{$start} AV. REFORMA S/N LOCAL T-10 COL. ALAMEDA CP. 82123 MAZATLAN, SIN {$end}",
                'footer'=>"",//SUCURSAL \n AV. REFORMA S/N LOCAL T-10 \n COL. ALAMEDA CP 82123 \n MAZATLAN SIN
                "tax"=>0
            ],
            "10523" => [//Checked
                "header"=>"{$start} Calle 5 No. 2101 Col Burocrata\n AGUAPRIETA, SONORA CP.84270 {$end}",
                'footer'=>'',//NOT ENABLED
                "tax"=>0
            ],
            "10562" => [//Checked
                "header"=>"{$start} BLVD ANTONIO ROSALES 334 COL. MORELOS CP:81460 GUAMUCHIL, SALVADOR ALVARADO, \n SINALOA \n {$end}",
                'footer'=>"",//SUCURSAL \n BLVD ANTONIO ROSALES 334 \n COL. MORELOS CP:81460 \n GML, SALVADOR ALVARADO, SIN.
                "tax"=>0.05
            ],
            "10783" => [
                "header"=>"{$start} BLVD. BENITO JUAREZ 2701 COL. VILLA DEL MAR CP 22703 PLAYAS DE ROSARITO, BC {$end}",
                'footer'=>'',//Not enabled
                "tax"=>0.05
            ],
            "default" => [
                'header'=> $default,
                'footer'=>'',
                'tax'=>0
            ],
        ];


        $return = $locations[$location][$config];
        $sep3 = new DateTime("2020-09-03");

        if(($config==='header') && ($date<$sep3)){
                $return = $locations["default"]["header"];
        }
        return $return;
    }

    //Please do not use this
    public function parseFloat($number){

        $url = "https://stringtonumber.azurewebsites.net/api/stringToDecimal?code=DdZmlUwZh8hwYvbkP58hp8cjhIvSndf1BW0v7AC5F27QkCD5Y5chYw==&number={$number}";

        $response = file_get_contents($url);

        return $response;
    }

}

//PALERMO HEADER:"PERMISIONARIA COMERCIALIZADORA DE ENTRETENIMIENTO DE CHIHUAHUA S.A DE C.V AV MANUEL GOMEZ MORIN 1101 PTE. INTL 211 COL. CARRIZALEJO NUEVO LEON CP 66254 RFC CEC050121415 OPERADORA TOP ASESORES DE ENTRETENIMIENTO, S.A. DE C.V. PERMISO SEGOB: DGAJS/SCEVF/P-08/2005 RFC:TAE1507079C4 CALLE VALLE SOL #122, INT 302 COL. LA DIANA CP 66566 SAN PEDRO GARZA GARCIA N.L."
//DIVERTIMEX, S.A DE C.V. Blvd Rodriguez N. 15 col. Centro C.P. 83000 Hermosillo, Sonora RFC DIV9110156W4 PERMISO SEGOB DGAJS
//SKAMPA HEADER:PERMISIONARIA COMERCIALIZADORA DE ENTRETENIMIENTO DE CHIHUAHUA S.A DE C.V AV MANUEL GOMEZ MORIN 1101 PTE. INTL 211 COL. CARRIZALEJO NUEVO LEON CP 66254 RFC CEC050121415 OPERADORA TOP ASESORES DE ENTRETENIMIENTO, S.A. DE C.V. PERMISO SEGOB: DGAJS
//Guamuchil: DIVERTIMEX, S.A DE C.V. Blvd Rodriguez N. 15 col. Centro C.P. 83000 Hermosillo, Sonora RFC DIV9110156W4 PERMISO SEGOB DGAJS/SCEVF/0139/2006