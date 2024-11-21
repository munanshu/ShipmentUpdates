<?php 


namespace Munanshu\CoreBasePackage\FormDataLoaders\DataLoaders;

use Munanshu\CoreBasePackage\FormDataLoaders\FormDataInterface;

class UserForm implements FormDataInterface
{
    // just a demo function to be called to get form data
    public function getData() {
        return ['somedata'];
    }
}
