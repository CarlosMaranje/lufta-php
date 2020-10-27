<?php


namespace Models;


class RemoveValue extends MasterReport
{
    public function propertiesMap()
    {
        return [
            ["field"=>"CashierId", "type"=>"string"],
            ["field"=>"CardNumber", "type"=>"string"],
            ["field"=>"Customer", "type"=>"string"],
            ["field"=>"TransactionNumber", "type"=>"string"],
            ["field"=>"TransactionDateTime", "type"=>"string"],
            ["field"=>"RemoveValue", "type"=>"float"],
            ["field"=>"Refund", "type"=>"float"],
            ["field"=>"Winning", "type"=>"float"],
            ["field"=>"TaxOnWinning", "type"=>"float"],
            ["field"=>"ISE", "type"=>"float"],
        ];
    }
}