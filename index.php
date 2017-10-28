<?php

/**
 * @file
 * @brief This file contains the script entry point.
 * 
 * (Find and) autoload files.
 */

// For production set to false.
define( 'DEVELOPMENT', true );

if( DEVELOPMENT )
{
    error_reporting( E_ALL | E_STRICT );
    
    if( !file_exists( 'filecache.php' ) )
    {
        CreateFileCache();
    }
    
    require 'filecache.php';
}
else
{
    // Require before setting the error level
    // to get feedback if requiring failes.
    require 'filecache.php';

    error_reporting( 0 );
}

/**
 * Make sure Exceptions are caught.
 * Note: Script will terminate on exception.
 */
try
{
    set_error_handler( 'ErrorHandler' );

    spl_autoload_register( 'Autoload', true );
    
    // Call OO entry point.
    Program::Main();
}
catch( Exception $exception )
{
    if( DEVELOPMENT )
    {
        echo $exception;
    }
    else
    {
        $message = 'Internal Server Error';
        
        $code = 500;
        
        header(
            $_SERVER[ 'SERVER_PROTOCOL' ] . " {$code} {$message}",
            true,
            $code
            );
        
        echo $message;
    }
    
    exit();
}

/**
 * Handle error and throw it as exception.
 *
 * @param int $errNo
 * @param string $errStr
 * @param string $errFile
 * @param int $errLine
 * @throws ErrorException
 * @return void
 */
function ErrorHandler( $errNo, $errStr, $errFile, $errLine )
{
    // error_reporting() returns 0 if error is suppressed using @
    if( error_reporting() !== 0 )
    {
        throw new ErrorException( $errStr, 0, $errNo, $errFile, $errLine );
    }
}

/**
 * Find and require the file that holds the class.
 *
 * @param string $class Qualified class name.
 * @return void
 */
function Autoload( $class )
{
    // Make the class/file mappings from filecache.php available here.
    global $files;

    if( DEVELOPMENT && !isset( $files[ $class ] ) )
    {
        // Maybe the file is newly added. Create a new file cache.
        CreateFileCache();
    }
    
    if( !isset( $files[ $class ] ) )
    {
        throw new Exception( "Class: {$class} could not be found." );
    }
    
    $result = $files[ $class ];

    require $result;

    // Check if the IStatic interface is somewhere in the class hierarchy.
    if( isset( class_implements( $class )[ 'IStatic' ] ) )
    {
        $reflection = new ReflectionClass( $class );
        
        if ( !$reflection->isAbstract() )
        {
            $class::Constructor();
        }
    }
}

/**
 * (re-)creates the file cache
 * (if we didn't already for this request).
 *
 * @return void
 */
function CreateFileCache()
{
    require_once 'CreateFileCache.php';
}
