<?php 

namespace Munanshu\CoreBasePackage\FormDataLoaders;

use Exception;
use Munanshu\CoreBasePackage\FormDataLoaders\DataLoaders\UserForm;

class FormDataFactory extends FormAbstract
{
    

    public static function createFormLoader($case){
        $loader = '';
        switch ($case) {
            case 'user':
                $loader = new UserForm();
                break;
        }

        if(!empty($loader) && $loader instanceof FormDataInterface){
            return $loader;
        }
        
        throw new Exception("$case should be an instance of FormDataInterface");
    }

}
