<?php

/**
 * @file
 * @brief This file contains the class ErrorController.
 */

/**
 * @brief Class for error response pages.
 */
class ErrorController extends ControllerBase
{
    
    public function Index()
    {
        return $this->NotFound();
    }
    
    public function NotFound()
    {
        $this->StatusCode = HttpStatusCode::$NotFound;
        
        $this->DataCollection[ 'message' ] = 'The object cannot be found.';
        $this->DataCollection[ 'description' ] =
            'The object you are looking for ' .
            '(or one of its dependencies) could have been removed, ' .
            'had its name changed, or is temporarily unavailable. ' .
            'Please review the requested URL ' .
            'and make sure that it is spelled correctly.';
        
        $this->DataCollection[ 'version' ] = Profile::Version;
        
        return $this->View( 'error/index.phtml' );
    }
    
    public function Forbidden()
    {
        $this->StatusCode = HttpStatusCode::$Forbidden;
        
        $this->DataCollection[ 'message' ] = 'Forbidden';
        $this->DataCollection[ 'description' ] =
            'Access to the requested resource has been forbidden.';
        
        $this->DataCollection[ 'version' ] = Profile::Version;
        
        return $this->View( 'error/index.phtml' );
    }

}
