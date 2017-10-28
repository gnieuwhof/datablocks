<?php

/**
 * @file
 * @brief This file contains the class SoapController.
 */

/**
 * @brief Class that handles SOAP calls.
 */
class SoapController extends ControllerBase
{
    
    public function Index()
    {
        if( !Config::SERVE_WSDL && array_key_exists( 'wsdl', $_GET ) )
        {
            $controller = new ErrorController();
            
            return $controller->Forbidden();
        }
        else
        {
            return $this->ProcessSoapRequest();
        }
    }
    
    private function ProcessSoapRequest()
    {
        $options = array(
            'soap_version' => SOAP_1_2
            );
        
        if( !DEVELOPMENT )
        {
            $options[ 'cache_wsdl' ] = WSDL_CACHE_BOTH;
        }
        else
        {
            $options[ 'cache_wsdl' ] = WSDL_CACHE_NONE;
        }
        
        $server = new SoapServer(
            Config::WSDL_FILE,
            $options
            );

        $server->setClass( 'CallWrapper' );
        
        $request = file_get_contents( 'php://input' );
        
        ob_start();
        
        $server->handle( $request );
        
        $response = ob_get_contents();
        
        ob_end_clean();
        
        return $this->Xml( $response );
    }

}
