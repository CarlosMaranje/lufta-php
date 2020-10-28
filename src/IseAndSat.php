<?php

namespace Models;
use \DateTime;

class IseAndSat extends MasterReport
{
    public function propertiesMap()
    {
        return [
            ["field"=>"IdOfEvento", "type"=>"int"],
            ["field"=>"NoDeComprobante", "type"=>"string"],
            ["field"=>"SaldoInitial", "type"=>"float"],
            ["field"=>"SaldoDePromocion", "type"=>"float"],
            ["field"=>"NoDeRegistroDeCaja", "type"=>"string"],
            ["field"=>"FormaDePago", "type"=>"string"],
            ["field"=>"MontoDePremioPagado", "type"=>"float"],
            ["field"=>"Refund", "type"=>"float"],
            ["field"=>"Winning", "type"=>"float"],
            ["field"=>"TaxOnWinning", "type"=>"float"],
            ["field"=>"Ise", "type"=>"float"],
            ["field"=>"DepRedem", "type"=>"string"],
            ["field"=>"MontoDePremioNoReclamado", "type"=>"float"],
            ["field"=>"FechaHora", "type"=>"string"],
            ["field"=>"CardNumber", "type"=>"string"],//String to keep the zeros at the beginning
            ["field"=>"RFC", "type"=>"string"],
            ["field"=>"CURP", "type"=>"string"],
            ["field"=>"FirstName1", "type"=>"string"],
            ["field"=>"FirstName2", "type"=>"string"],
            ["field"=>"LastName1", "type"=>"string"],
            ["field"=>"LastName2", "type"=>"string"],
            ["field"=>"FechaDeNacimiento", "type"=>"string"],
            ["field"=>"Isr", "type"=>"string"],
            ["field"=>"IDType", "type"=>"string"],
            ["field"=>"IdNumber", "type"=>"string"],
            ["field"=>"AppartmentNumber", "type"=>"string"],
            ["field"=>"StreetNumber", "type"=>"string"],
            ["field"=>"StreetName", "type"=>"string"],
            ["field"=>"Suburb", "type"=>"string"],
            ["field"=>"City", "type"=>"string"],
            ["field"=>"State", "type"=>"string"],
            ["field"=>"PostalCode", "type"=>"string"],

        ];
    }

    public function build($csvArray, $location, $type='Deposit'){
        $this->setProperties($this->propertiesMap(), $csvArray);
        $this->setLocation($location);
        $sheetStructure = $this->sheetStructure($location);
//        $sheetStructure = FALSE;
//
//        if($this->field['DepRedem']===$type){
//        }

        return $sheetStructure;
    }

    public function sheetStructure($location){
        $prop = $this->field;
        $type = $prop['DepRedem'];
        $comprobante = $prop["NoDeComprobante"];
        $explodeFecha = explode('T', $prop["FechaHora"]);
        $fecha = str_replace('-', '/',$explodeFecha[0]);
        $fechaHora = "{$fecha}   $explodeFecha[1]";
        $montoDePremioPagado = $this->parseProperty($prop['MontoDePremioPagado'], 'float');
        $refund = $prop['Refund'];


        $terminal = ((explode('-',$comprobante))[0]).'-'.$location;
        $transaction = ((explode('-',$comprobante))[1]);

        //RemoveValue
        if($type === 'Redemtion'){
            $tipoTrans = "RETIRO";

            $ise = $prop['Ise'];
            $taxOnWinning = $this->parseProperty($prop['TaxOnWinning'], 'float');
            $retencion = "7.00% Retenciones";
            $entregado = $this->parseProperty(($montoDePremioPagado -$taxOnWinning), 'float');
            $winning = $prop['Winning'];

            //Decides if there ISE or not
            if($ise > 0){
                $titleIse = 'ISE';
                $entregado = $entregado-$ise;
                $ise = $this->formatNumber($this->parseProperty($ise, 'float')).' $';
            }
            else{
                $ise = '';
                $titleIse = '';
            }

            $deposit = $refund;
            $titleDeposit = 'Devoluciones';
            $retirado = $this->parseProperty($montoDePremioPagado,'float');
            $addDeposit = '0.00';
            $montoEntregado = "**MONTO ENTREGADO";
            $premioTitle = "Premio";
        }
        else{//AddValue
            $titleDeposit = "Deposito total";
            $tipoTrans = "RECARGA";
            $saldo = $this->parseProperty($prop["SaldoInitial"], 'float');
            $tax = $this->config("tax");
            $deposit = $saldo;
            $entregado = $saldo;
            $retencion = "Impuesto recarga";
            $forTax = $entregado/(1-$tax);
            $taxOnWinning = ($forTax*$tax);
            $winning = "0.00";
            $retirado = $winning;
            $ise = '';
            $titleIse = '';
            $addDeposit = $this->parseProperty($entregado,'float');
            $montoEntregado = "";
            $premioTitle = "Premio";
        }


        $taxOnWinning = $this->parseProperty($taxOnWinning, "float");
        $deposit = $this->parseProperty($deposit, "float");

        $client = trim($prop['FirstName1']).' '.trim($prop['FirstName2'])." ".trim($prop['LastName1']).' '.trim($prop['LastName2']);

        $fecha = new DateTime($fecha);
        $sep3 = new DateTime("2020-09-03");
        $interval = date_diff($sep3, $fecha);

        if($fecha > $sep3){
//            $this->setLocation("default");
        }

//        $num = floatval('3');
//        var_dump($num);

        return [
            "header"=>[
                "text"=>$this->config("header")
            ],
            "subheader"=>[
                "text"=>"NOTA DE LA TRANSACCION CON TARJETA"
            ],
            "body"=>[
                $tipoTrans=>[
                    "Cajero"=> '10272',
                    "Terminal"=> $terminal,
                    "ID. tarjeta"=>$prop["CardNumber"],
                    "Cliente"=> $client
                ],
                "DETALLES DE LA TRANSACCION"=>[
                    "Transaccion"=>$transaction,
                    "Fecha"=> $fechaHora,
                    "Deposito"=> $this->formatNumber($addDeposit) ." $",
                    "Monto Retirado"=>$this->formatNumber($retirado)." $",
                    $titleDeposit=> $this->formatNumber($deposit) ." $",
                    "{$premioTitle}" => $this->formatNumber($winning) .' $',
                ],
                "ESTADO DE CUENTA"=>[
                    "Cuenta"=>"1",
                    "{$premioTitle}"=> $this->formatNumber($winning) .' $',
                    $titleIse => $ise,
                    "{$retencion}" => $this->formatNumber($taxOnWinning) .' $',
                    "{$montoEntregado}" => $this->formatNumber($entregado).' $'
                ],
                "Moneda: MXN"=>[
                    "Pago"=>"Dinero"
                ],
            ],
            "footer"=>[
                "text"=>$this->config("footer")
            ]
        ];
    }
}