<?php

use Illuminate\Support\Facades\File;


if(!function_exists('readCsvData')){

    function readCsvData($filename,$headers)
    {
        $filePath = public_path($filename);
        $firstIteration = true;
        if (!File::exists($filePath)) {
            return null;
        }
        $file = fopen($filePath, 'r');
        if (!$file) {
            return null;
        }
        $data = [];
        while (($row = fgetcsv($file)) !== false) {
            if($firstIteration){
                $firstIteration = false;
                continue;
            }
            $shipmentData = array_combine($headers, $row);
            $data[] = $shipmentData;
        }
        fclose($file);
        return $data;
    }

}

if(!function_exists('getShipmentHeaders')){

    function getShipmentHeaders(){
        return [
            'shipment_id', 
            'latitude', 
            'longitude', 
            'temperature', 
            'timestamp', 
            'device_id'
        ];
    }
    
}