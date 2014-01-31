<?php
namespace gallery;

use wsc\modul\AbstractModul;
/**
 *
 * @author Michi
 *        
 */
class gallery extends AbstractModul
{

    
    public function registerAutoloader()
    {
        require('modules\gallery\autoloader.php');
        autoloader::register();
    }
}

?>