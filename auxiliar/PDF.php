<?php


namespace PDF;


class PDF extends \FPDF
{
    public function Footer(){
        $this->Write(3, 'Axesetwork');
    }
}