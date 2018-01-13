<?php

/**
 * @brief This file contains the class Timestamp.
 * @file
 */

/**
 * @brief Class containing a function to get the server timestamp (used for authentication).
 */
class Timestamp
{
    
    /**
     * @attention all
     * @return long
     */
    public static function Milliseconds()
    {
        return round( microtime( true ) * 1000 );
    }
    
}
