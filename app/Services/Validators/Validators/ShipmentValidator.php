<?php 

namespace App\Services\Validators\Validators;

use App\Services\Validators\AbstractValidator;
use Illuminate\Support\Facades\Validator;

class ShipmentValidator extends AbstractValidator
{
    

    public function validateCsvData($data) {
        $rules = [
            'shipments' => 'required|array', 
            'shipments.*.shipment_id' => 'required|alpha_num|unique:shipments,shipment_id',  
            'shipments.*.device_id' => 'required|alpha_num',  
            'shipments.*.latitude' => 'required|numeric', 
            'shipments.*.longitude' => 'required|numeric', 
            'shipments.*.temperature' => 'required|numeric',  
        ];

        $messages = [
            'shipments.*.id.required' => 'The shipment ID is required.',
            'shipments.*.id.numeric' => 'The shipment ID must be alphanumeric.',
            'shipments.*.device_id.required' => 'The device id is required.',
            'shipments.*.device_id.alpha_num' => 'The device id must be alphanumeric.',
            'shipments.*.temperature.required' => 'The temperature is required.',
            'shipments.*.temperature.numeric' => 'The temperature must be a number.',
            'shipments.*.latitude.required' => 'The latitude is required.',
            'shipments.*.latitude.numeric' => 'The latitude must be numeric.',
            'shipments.*.longitude.required' => 'The longitude is required.',
            'shipments.*.longitude.numeric' => 'The longitude must be numeric.',
        ];
        

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $this->setErrors($validator->errors()->all()); 
            return false;
        }
        
        return true;
    }

    public function validateShipmentParams($data) {
        
        $rules = [
            'shipment_id' => 'alpha_num|exists:shipments,shipment_id',
        ];

        $messages = [
            'shipment_id.alpha_num' => 'The shipment id should contain alphabets and digits.',
            'shipment_id.exists' => 'The shipment id does not exists.',
        ];

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $this->setErrors($validator->errors()->all()); 
            return false;
        }
        
        return true;
    }
}
