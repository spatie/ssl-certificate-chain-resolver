<?php
namespace Codeception\Module;

use Exception;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class FunctionalHelper extends \Codeception\Module
{
    public function seeExceptionThrown($exceptionMessage, $function)
    {
        try
        {
            $function();
            return false;
        }
        catch (Exception $e)
        {
            if($e->getMessage() == $exceptionMessage)
            {
                return true;
            }
            return false;
        }
    }
}
