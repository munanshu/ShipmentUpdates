<?php 

namespace App\Services\Validators;



class AbstractValidator
{
    protected $errors;

    public function getErrors() {
        return $this->errors;     
    }
    
    public function setErrors($errors) {
        return $this->errors = $errors;     
    }


}
