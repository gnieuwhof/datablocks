<?php

/**
 * @file
 * @brief This file contains the the class Database.
 */

use nl\gn\Sygnis as S;

/**
 * @brief This class contains all database functions
 *   (all database access should go through this class).
 */
class Database
{
    
    /**
     * @var IDbmsAdapter $dbmsAdapter
     */
    private $dbmsAdapter = null;
    
    
    /**
     * Gets the QueryBuilder instance.
     * 
     * @retval IQueryBuilder
     */
    public function GetQueryBuilder()
    {
        return $this->dbmsAdapter->GetQueryBuilder();
    }
    
    
    public function __construct( S\IDbmsAdapter $dbmsAdapter )
    {
        $this->dbmsAdapter = $dbmsAdapter;
    }
    
    
    public function Merge(
        $tableName,
        $columns,
        $values,
        $columnsToIgnoreOnUpdate
        )
    {
        try
        {
            return S\DB::Merge(
                $this->dbmsAdapter,
                $tableName,
                $columns,
                $values,
                $columnsToIgnoreOnUpdate
                );
        }
        catch( PDOException $exception )
        {
            self::ExceptionHandler( $exception );
        }
    }
    
    public function SelectWhere( $tableName, $columnNames, $where )
    {
        try
        {
            return S\DB::RetrieveAll(
                $this->dbmsAdapter,
                $columnNames,
                $tableName,
                $where
                );
        }
        catch( PDOException $exception )
        {
            self::ExceptionHandler( $exception );
        }
    }
    
    public function SelectRange(
        $tableName,
        $columnNames,
        $where,
        $start,
        $count
        )
    {
        try
        {
            return S\DB::RetrieveRange(
                $this->dbmsAdapter,
                $columnNames,
                $tableName,
                $where,
                $start,
                $count
                );
        }
        catch( PDOException $exception )
        {
            self::ExceptionHandler( $exception );
        }
    }
    
    public function Delete( $tableName, $where )
    {
        try
        {
            return S\DB::Delete(
                $this->dbmsAdapter,
                $tableName,
                $where
                );
        }
        catch( PDOException $exception )
        {
            self::ExceptionHandler( $exception );
        }
    }
    
    private static function ExceptionHandler( PDOException $exception )
    {
        //SQLSTATE[42S02]: Base table or view not found: 1146 Table 'classicmodels.customers3' doesn't exist.
        throw new SoapException( 'Resource error occurred.' );
    }
    
}
