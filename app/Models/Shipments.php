<?php

namespace App\Models;

use App\Events\ShipmentDelayedEvent;
use App\Events\ShipmentUpdate;
use App\Events\TemperatureExcursionEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Shipments extends Model
{
    /**
     * 
     * */ 
    use HasFactory;

    /**
     * fields of table shipments
     * */ 
    protected $fillable = [
        'shipment_id',
        'latitude',
        'longitude',
        'temperature',
        'timestamp',
        'device_id',
    ];

    /**
     * description : To store data and track logs and calling an event channel
     * @param array $csvData
     * @param array $headers
     * @return array 
     * */ 
    public function storeShipment($csvData,$headers) {
        $insertedRecords = 0;
        $failedRecords = 0;

        foreach ($csvData as $index => $row) {
            
            $shipmentData = array_combine($headers, $row);

            try {
                $sipmentInserted = self::create([
                    'shipment_id' => $shipmentData['shipment_id'],
                    'latitude' => (float) $shipmentData['latitude'],
                    'longitude' => (float) $shipmentData['longitude'],
                    'temperature' => (float) $shipmentData['temperature'],
                    'timestamp' => \Carbon\Carbon::parse($shipmentData['timestamp']),
                    'device_id' => $shipmentData['device_id'],
                ]);
                $this->notifyUser($sipmentInserted->id,$shipmentData);
                $insertedRecords++;
            } catch (\Exception $e) {
                Log::error("Failed to insert shipment data for row {$index}: " . $e->getMessage());
                $failedRecords++;
            }
        }

        return [
            'inserted_records' => $insertedRecords,
            'failed_records' => $failedRecords,
        ];
    }


    /**
     * description : To check status of shipment is get delayed or on time
     * @param array $shipment_id
     * @return array 
     * */ 
    public function checkShipmentDelayedStatus($shipmentData,$allEntries=false) {

        $allowedDelayedDays = config('shipment.allowedDelayedDays');
        if($allEntries){
            $firstHistoryRecord = Shipments::where('shipment_id', $shipmentData['shipment_id'])
                                          ->orderBy('timestamp', 'asc')
                                          ->first();

            $estimatedArrivalTime = Carbon::parse($firstHistoryRecord->timestamp)->addDays($allowedDelayedDays);
            $isDelayed = self::where('shipment_id', $shipmentData['shipment_id'])
            ->where('timestamp', '>', $estimatedArrivalTime)
            ->exists();
        }else{
            $expectedArrivalTime = Carbon::parse($shipmentData['timestamp'])->addDays($allowedDelayedDays);
            $isDelayed = Carbon::now()->isAfter($expectedArrivalTime);
        }
         
        return [
            'shipment_id' => $shipmentData['shipment_id'],
            'status' => $isDelayed ? 'Delayed' : 'On-time',
        ];
    }
    
    /**
     * description : To check temerature deviation of shipment
     * @param array $shipment_id
     * @return array 
     * */ 
    public function checkShipmentTemperationDeviation($shipmentData,$allEntries=false) {

        $expectedTemperature = config('shipment.expectedTemperature');
        $incrementLimit = config('shipment.incrementLimit');
        if($allEntries){
            $firstHistoryRecord = Shipments::where('shipment_id', $shipmentData['shipment_id'])
                                          ->orderBy('timestamp', 'asc')
                                          ->first();
            $initialTemperature = $firstHistoryRecord->temperature;
            $isTemperatureExceeded = Shipments::where('shipment_id', $shipmentData['shipment_id'])
                                                     ->whereRaw('ABS(temperature - ?) > ?', [$initialTemperature, $incrementLimit])
                                                     ->exists();;
        }else{
            $isTemperatureExceeded = abs($shipmentData['temperature'] - $expectedTemperature) > $incrementLimit;         
        }
        return [
            'shipment_id' => $shipmentData['shipment_id'],
            'temperature_exceeded' => $isTemperatureExceeded,
            'expected_temperature' => $expectedTemperature,
        ];
    }


    /**
     * description : To notify user about delayed status and temerature deviation of shipment
     * @param array $shipmentData
     * */
    public function notifyUser($shipmentId,$shipmentData) {
        event(new ShipmentUpdate($shipmentData));
        $shipmentStatusData = $this->checkShipmentDelayedStatus($shipmentData);
        $shipment = self::findorfail($shipmentId);
        if(!empty($shipmentStatusData) && $shipmentStatusData['status']=='Delayed'){
            event(new ShipmentDelayedEvent($shipment));
        }
        $shipmentTemperatureData = $this->checkShipmentTemperationDeviation($shipmentData);
        if(!empty($shipmentTemperatureData) && $shipmentTemperatureData['temperature_exceeded']){
            event(new TemperatureExcursionEvent($shipment));
        }
    }
}
