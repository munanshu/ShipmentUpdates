<?php 

namespace App\Services\Validators;

use App\Services\Validators\Validators\ShipmentValidator;

class ValidationService 
{
    
    public function getValidator($validatorClass) {
        switch ($validatorClass) {
            case 'shipments':
                $validator = new ShipmentValidator();
                break;
        }

        return $validator;
    }


}
