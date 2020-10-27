<?php


function loadFile($file, $delimiter=';', $type='csv'){

    switch ($type){
        default:
            $array = fgetcsv($file, "", $delimiter);
            break;
    }

    return $array;
}