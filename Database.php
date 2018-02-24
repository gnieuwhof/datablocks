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
    
    public function SelectWhere( $tableName, $columnNames, $where, $distinct = false )
    {
        try
        {
            return S\DB::RetrieveAll(
                $this->dbmsAdapter,
                $columnNames,
                $tableName,
                $where,
                $distinct
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
    
    public function Count( $tableName, $where )
    {
        try
        {
            return S\DB::CountRows(
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
    
    public function WhereQuery( $where, array &$whereValues )
    {
        $queryBuilder = $this->GetQueryBuilder();
        
        $whereClause = $where->ToString( $queryBuilder, /*ref*/ $whereValues );
        
        return $whereClause;
    }
    
    public function GetToken( $tokenId )
    {
        $where = new S\WhereEqual( 'id', $tokenId );
        
        return S\DB::RetrieveFirst(
            $this->dbmsAdapter,
            array( 'guid', 'level', 'accessed' ),
            'tokens',
            $where
            );
    }
    
    public function SaveTimestamp( $tokenId, $timestamp )
    {
        $where = new S\WhereEqual( 'id', $tokenId );
        
        S\DB::Update(
            $this->dbmsAdapter,
            'tokens',
            array( 'accessed' => $timestamp ),
            $where
            );
    }
    
    private static function ExceptionHandler( PDOException $exception )
    {
        //SQLSTATE[42S02]: Base table or view not found: 1146 Table 'classicmodels.customers3' doesn't exist.
        throw new SoapException( 'Resource error occurred.' );
    }
    
}
