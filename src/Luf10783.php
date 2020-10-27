<?php

namespace Models;

class Luf10783 extends MasterReport
{
    public function propertiesMap()
    {
        return [
            ["field"=>"Date", "type"=>"string"],
            ["field"=>"TransactionNumber", "type"=>"string"],
            ["field"=>"Customer", "type"=>"string"],
            ["field"=>"CardNumber", "type"=>"string"],
            ["field"=>"CardCategory", "type"=>"string"],
            ["field"=>"TransactionType", "type"=>"string"],
            ["field"=>"Source", "type"=>"string"],
            ["field"=>"ExtraInfo", "type"=>"string"],
//            ["field"=>"PreviousMeter", "type"=>"string"],
//            ["field"=>"CurrentMeter", "type"=>"string"],
            ["field"=>"Cashier", "type"=>"string"],
            ["field"=>"Amount", "type"=>"float"],

        ];
    }

    public function build($csvArray, $location){
        $this->setProperties($this->propertiesMap(), $csvArray);
        return $this->sheetStructure($location);
    }

    public function sheetStructure($pos){
        $prop = $this->field;
        $amount = $prop["Amount"];
        $type = 'RECARGA';
        $entregado = "0.00";

        if($amount<0){
            $type = "RETIRO";
            $entregado = number_format($amount*(-1), 2);
            $amount = "0.00";
        }
        else{
            $amount = number_format($amount, 2);
        }
        //PALERMO HEADER:"PERMISIONARIA COMERCIALIZADORA DE ENTRETENIMIENTO DE CHIHUAHUA S.A DE C.V AV MANUEL GOMEZ MORIN 1101 PTE. INTL 211 COL. CARRIZALEJO NUEVO LEON CP 66254 RFC CEC050121415 OPERADORA TOP ASESORES DE ENTRETENIMIENTO, S.A. DE C.V. PERMISO SEGOB: DGAJS/SCEVF/P-08/2005 RFC:TAE1507079C4 CALLE VALLE SOL #122, INT 302 COL. LA DIANA CP 66566 SAN PEDRO GARZA GARCIA N.L."
        //DIVERTIMEX, S.A DE C.V. Blvd Rodriguez N. 15 col. Centro C.P. 83000 Hermosillo, Sonora RFC DIV9110156W4 PERMISO SEGOB DGAJS
        //SKAMPA HEADER:PERMISIONARIA COMERCIALIZADORA DE ENTRETENIMIENTO DE CHIHUAHUA S.A DE C.V AV MANUEL GOMEZ MORIN 1101 PTE. INTL 211 COL. CARRIZALEJO NUEVO LEON CP 66254 RFC CEC050121415 OPERADORA TOP ASESORES DE ENTRETENIMIENTO, S.A. DE C.V. PERMISO SEGOB: DGAJS
        //Guamuchil: DIVERTIMEX, S.A DE C.V. Blvd Rodriguez N. 15 col. Centro C.P. 83000 Hermosillo, Sonora RFC DIV9110156W4 PERMISO SEGOB DGAJS/SCEVF/0139/2006
        return [
            "header"=>[
                "text"=>"PERMISIONARIA COMERCIALIZADORA DE ENTRETENIMIENTO DE CHIHUAHUA S.A DE C.V AV MANUEL GOMEZ MORIN 1101 PTE. INTL 211 COL. CARRIZALEJO NUEVO LEON CP 66254 RFC CEC050121415 OPERADORA TOP ASESORES DE ENTRETENIMIENTO, S.A. DE C.V. PERMISO SEGOB: DGAJS/SCEVF/P-08/2005 RFC:TAE1507079C4 CALLE VALLE SOL #122, INT 302 COL. LA DIANA CP 66566 SAN PEDRO GARZA GARCIA N.L."
            ],
            "subheader"=>[
                "text"=>"NOTA DE LA TRANSACCION CON TARJETA"
            ],
            "body"=>[

                $type=>[
                    "Cajero"=> (!($prop["Cashier"])) ?'(no data)'  : $prop["Cashier"],
                    "Terminal"=>"#000{$pos}",
                    "ID. tarjeta"=>"#00". $prop["CardNumber"],
                    "Cliente"=> $prop["Customer"]
                ],
                "DETALLES DE LA TRANSACCION"=>[
                    "Transaccion"=>(!$prop["TransactionNumber"]) ?  '(no data)':'#00'.$prop["TransactionNumber"],
                    "Fecha"=> $prop["Date"],
                    "Deposito"=> $amount ." $",
                    "Monto Retirado"=>$entregado." $",
                    "Balance"=>"0.00 $"
                ],
                "ESTADO DE CUENTA"=>[
                    "Cuenta"=>"1",
                    "Deposito Total"=> $amount ." $",
                    "7.00% Retenciones"=> "0.00 $",
                    "**MONTO ENTREGADO"=> $entregado." $"
                ],
                "Moneda: MXN"=>[
                    "Pago"=>"Dinero"
                ],
            ],
            "footer"=>[
                "text"=>"Sucursal Fiscal: SKAMPA CASINO BLVD. COSTERO Y SANGINES 6-A COL CARLOS PACHECO CP: 22880 ENSENADA BAJA CALIFORNIA"
            ]
        ];
    }
}