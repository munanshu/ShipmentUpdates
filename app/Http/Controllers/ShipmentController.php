<?php

namespace App\Http\Controllers;

use App\Models\Shipments;
use App\Services\Validators\ValidationService;

class ShipmentController extends Controller
{
    /**
     * description : To ingest data from csv into db and calling an event channel
     * @param Shipments $shipmentModel
     * @return ResponseFactory 
     * */ 
    public function ingestCsv(ValidationService $validationService , Shipments $shipmentModel)
    {
        $records = [];
        $file = config('shipment.csvFilePath');
        $headers = getShipmentHeaders();
        $csvData = readCsvData($file,$headers);
        $shipmentValidator = $validationService->getValidator('shipments');
        if(!$shipmentValidator->validateCsvData(['shipments'=>$csvData])){
             return response()->json(['error' => $shipmentValidator->getErrors()], 400);
        }

        if(!empty($csvData)){
            $records = $shipmentModel->storeShipment($csvData,$headers);
        }

        return response()->json([
            'success' => true,
            'data'    => $records  
        ]);
    }

    /**
     * description : To check whether shipment is delayed or not 
     * and giving update via firing an event channel 'ShipmentDelayedEvent'
     * @param string $shipment_id
     * @param ValidationService $validationService
     * @param Shipments $shipmentModel
     * @return ResponseFactory 
     * */ 
    public function shipmentStatus($shipment_id , ValidationService $validationService , Shipments $shipmentModel)
    {
        $shipmentValidator = $validationService->getValidator('shipments');
        
        if(!$shipmentValidator->validateShipmentParams(['shipment_id'=> $shipment_id])){
            return response()->json(['error' => $shipmentValidator->getErrors()], 400);
        }
        $shipment = $shipmentModel->checkShipmentDelayedStatus(['shipment_id'=>$shipment_id],true);

        return response()->json([
            'success' => true,
            'data'    => $shipment  
        ]);
    }

    /**
     * description : To check whether temperature exceeded and provide 
     * real time update via firing an event channel 'TemperatureExcursionEvent'
     * @param int $shipment_id
     * @param ValidationService $validationService
     * @param Shipments $shipmentModel
     * @return ResponseFactory 
     * */ 
    public function shipmentTemperature($shipment_id , ValidationService $validationService , Shipments $shipmentModel)
    {
        $shipmentValidator = $validationService->getValidator('shipments');
        
        if(!$shipmentValidator->validateShipmentParams(['shipment_id'=> $shipment_id])){
             return response()->json(['error' => $shipmentValidator->getErrors()], 400);
        }
        $shipment = $shipmentModel->checkShipmentTemperationDeviation(['shipment_id'=>$shipment_id],true);

        return response()->json([
            'success' => true,
            'data'    => $shipment  
        ]);
    }

    
}
