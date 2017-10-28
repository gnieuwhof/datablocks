<?php

/**
 * @file
 * @brief This file contains the class: Config.
 */

/**
 * @brief Class containing constant fields.
 */
class Config
{

    #REGION MVC
    /**
     * @var string Default MVC controller.
     */
    const MVC_DEFAULT_CONTROLLER = 'soap';
    
    /**
     * @var int MVC output buffer size in bytes.
     */
    const BUFFER_SIZE = 8192;
    #ENDREGION MVC
    

    #REGION SoapGen
    /**
     * @var string Name of the WebService.
     * 
     * NOTE: Any spaces, at least in c#, will be ignored.
     * Just don't use spaces to be sure.
     */
    const SERVICE_NAME = 'DataBlocks';

    /**
     * @var string Name of the WSDL file
     * (can be left as is).
     */
    const WSDL_FILE = 'definitions.wsdl.php';

    /**
     * @var string URL of the WebService.
     */
    const SERVICE_LOCATION = 'http://127.0.0.1/datablocks/example/';

    /**
     * @var string Name of the wrapper class
     * (can be left as is).
     */
    const SOAP_WRAPPER_CLASS_NAME = 'SoapWrapper';

    /**
     * @var string Location of the SOAP classes
     * (can be left as is).
     */
    const SOAP_APPLICATION_DIR = 'application/';
    #ENDREGION SoapGen
    
    
    #REGION MySQL
    /**
     * @var string Database server URI.
     */
    const MYSQL_HOST = 'localhost';

    /**
     * @var string Database name.
     */
    const MYSQL_DATABASE = 'database';

    /**
     * @var string Database username.
     */
    const MYSQL_USERNAME = 'root';

    /**
     * @var string Database password.
     */
    const MYSQL_PASSWORD = '';
    
    /**
     * @var string Default timezone.
     */
    const TIMEZONE = 'Europe/Amsterdam';
    #ENDREGION MySQL
    
    
    /**
     * @var bool Whether the WSDL metadata should be served.
     */
    const SERVE_WSDL = true;
    
}
