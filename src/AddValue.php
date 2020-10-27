<?php


namespace Models;


class AddValue extends MasterReport
{
    public function propertiesMap()
    {
        return [
            ["field"=>"CashierId", "type"=>"string"],
            ["field"=>"CardNumber", "type"=>"string"],
            ["field"=>"Customer", "type"=>"string"],
            ["field"=>"TransactionNumber", "type"=>"string"],
            ["field"=>"TransactionDateTime", "type"=>"string"],
            ["field"=>"Deposit", "type"=>"float"],
            ["field"=>"IEPS", "type"=>"float"],
        ];
    }


}