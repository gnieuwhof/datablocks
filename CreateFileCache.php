<?php

/**
 * @file
 * @brief Logic to create the file cache.
 * 
 * This file contains the logic to create the filecache.
 * The filecache is a file containing an associative array,
 * where the key is a classname (FQN) and the value is the file
 * containing the class.
 */

$phpFiles = GetFiles( '.' );

$classesAndFiles = '';

$count = count( $phpFiles );

foreach( $phpFiles as $key => $value )
{
    $classesAndFiles .= "    '$key' => '$value'";

    if( --$count > 0 )
    {
        $classesAndFiles .= ',';
    }
    
    $classesAndFiles .= PHP_EOL;
}

$content =
"<?php

/**
 * @file
 * @brief This file holds the class locations.
 *
 * Note: This is an automatically generated file.
 * It should not be modified by hand.
 *
 * This file holds an array containing all classes (qualified name)
 * and the corresponding file that holds the class.
 */

global \$files;

\$files = array(
$classesAndFiles    );
";

file_put_contents( 'filecache.php', $content );

global $files;

$files = $phpFiles;

/**
 * Recursively find all files with php extension
 * in folder (incl. files in subfolders)
 *
 * @param string $dir Path to folder
 * @param string $prefix Used for recursive calls
 * @return string[] Files
 */
function GetFiles( $dir, $prefix = '' )
{
    $dir = rtrim( $dir, '\\/' );
    $result = array();

    if( ( $handle = opendir( $dir ) ) !== false )
    {
        while( ( $entry = readdir( $handle ) ) !== false )
        {
            // Ignore if file/folder starts with a period (.)
            if ( $entry[ 0 ] !== '.' )
            {
                if( is_dir( $dir . '/' . $entry ) )
                {
                    $files = GetFiles(
                        $dir . '/' . $entry,
                        $prefix . $entry . '/'
                        );
                    
                    $result = array_merge( $result, $files );
                }
                else if( strcasecmp( substr( $entry, -4 ), '.php' ) == 0 )
                {
                    $phpFile = $prefix . $entry;

                    if( $prefix != '' )
                    {
                        if( EndsWithIgnoreCase($phpFile, 'index.php' ) ||
                            EndsWithIgnoreCase($phpFile, 'default.php' )
                            )
                        {
                            // Ignore this folder,
                            // it contains an index/default file.
                            closedir( $handle );
                            return array();
                        }
                    }

                    $classes = GetQualifiedNames( $phpFile );

                    foreach( $classes as $class )
                    {
                        foreach( array_keys( $result ) as $classInCache )
                        {
                            if( strcasecmp( $classInCache, $class ) == 0 )
                            {
                                throw new Exception(
                                    'Duplicate class found! ' .
                                    'Class: ' . $class . ' found in ' .
                                    $result[$classInCache] . ' and ' .
                                    $phpFile
                                    );
                            }
                        }

                        $result[ $class ] = $phpFile;
                    }
                }
            }
        }

        closedir( $handle );
    }

    return $result;
}

/**
 * Determines whether the given string end matches
 * the other string, ignoring the case.
 * 
 * @param string $haystack
 * @param strin $needle
 * @return boolean
 */
function EndsWithIgnoreCase( $haystack, $needle )
{
    $length = strlen( $needle );
    if( $length == 0 )
    {
        return true;
    }
    
    $haystackEnd = substr( $haystack, -$length );

    return ( strtolower( $haystackEnd ) == strtolower( $needle ) );
}

/**
 * Get qualified names of all classes defined in file.
 * 
 * @param string $file Path of the file.
 * @return string[] Qualified classnames.
 */
function GetQualifiedNames( $file )
{
    $fileContent = file_get_contents( $file );
    $phpTokens = token_get_all( $fileContent );
    $result = array();
    $namespace = '';
    $i = 0;
    $count = count( $phpTokens );

    while( $i < $count )
    {
        if( is_array( $phpTokens[ $i ] ) && ( $phpTokens[ $i ][ 0 ] === T_NAMESPACE ) )
        {
            $namespace = '';
            // Found namespace declaration
            while( ++$i < $count )
            {
                if( is_array( $phpTokens[ $i ] ) &&
                    ( ( $phpTokens[ $i ][ 0 ] == T_STRING ) ||
                    ( $phpTokens[ $i ][ 0 ] == T_NS_SEPARATOR ) )
                    )
                {
                    $namespace .= $phpTokens[ $i ][ 1 ];
                }
                else if( ( $phpTokens[ $i ] == ';' ) || ( $phpTokens[ $i ] == '{' ) )
                {
                    break;
                }
            }
        }

        if( ( $phpTokens[ $i ][ 0 ] === T_CLASS ) || ( $phpTokens[ $i ][ 0 ] === T_INTERFACE ) )
        {
            // Found class/interface declaration
            $class = '';

            while( ++$i < $count )
            {
                if( is_array( $phpTokens[ $i ] ) && ( $phpTokens[ $i ][ 0 ] == T_STRING ) )
                {
                    $class .= $phpTokens[$i][1];
                }
                else if(
                    ( $phpTokens[ $i ] == '{' ) ||
                        ( is_array( $phpTokens[ $i ] ) && (
                            ( $phpTokens[ $i ][ 0 ] == T_EXTENDS ) ||
                            ( $phpTokens[ $i ][ 0 ] == T_IMPLEMENTS )
                            )
                        )
                    )
                {
                    if( $namespace == '' )
                    {
                        $result[] = $class;
                    }
                    else
                    {
                        $result[] = $namespace . '\\' . $class;
                    }

                    break;
                }
            }
        }

        ++$i;
    }
    
    return $result;
}
