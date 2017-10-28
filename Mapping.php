<?php

/**
 * @file
 * @brief This file contains the class: Mapping.
 */

/**
 * @brief This class handles the custom naming of objects.
 * 
 * By default the names in DefaultMapping are used
 * but, names can be overridden in this class.
 */
class Mapping
{

    private static $tables = array(
//      'table_name_in_database' => 'TableObjectName'
        );
    
//  private static $table_name_in_database = array(
//      'column_name_in_database' => 'PropertyNameInObject'
//      );

    /**
     * This function is called by the DataBlocks generator.
     * 
     * e.g. table name to object:
     * Mapping::GetName( 'tables', 'table_name_in_db' );
     * 
     * e.g. column name to object property:
     * Mapping::GetName( 'table_name', 'column_name' );
     * 
     * If no mapping in this class is found the default mapping is returned, if any.
     * An exception is thrown otherwise.
     * 
     * @param type $object
     * @param type $item
     * @return type
     * @throws Exception
     */
    public static function GetName( $object, $item )
    {
        if( isset( self::${$object} ) )
        {
            if( isset( self::${$object} [ $item ] ) )
            {
                // Default mapping overridden
                return self::${$object}[ $item ];
            }
        }
        
        if( isset( DefaultMapping::${$object} ) )
        {
            if( isset( DefaultMapping::${$object}[ $item ] ) )
            {
                return DefaultMapping::${$object}[ $item ];
            }
        }
        
        throw new Exception(
            "No mapping found for column: {$item} in table: {$object}"
            );
    }

}
