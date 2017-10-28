<?php

/**
 * @file
 * @brief This file contains the class ExpressionHelper.
 */

use nl\gn\Sygnis as S;

/**
 * @brief Class used to process Expression instances.
 * 
 * @internal
 */
class ExpressionHelper
{
    
    public static function Validate( $expression, $fieldEnum )
    {
        if( !isset( $expression->Op ) )
            throw new SoapException( 'Op must be set.' );
        
        if( $expression->Op == Operator::_True )
        {
            // No need to check the other fields.
            return;
        }
        
        if( ( $expression->Op == Operator::_And ) ||
            ( $expression->Op == Operator::_Or )
            )
        {
            if( !isset( $expression->Expressions ) )
                throw new SoapException( 'Expressions must be set if Op is a boolean operator (except True).' );
            
            if( !is_array( reset( $expression->Expressions ) ) ||
                ( count( reset( $expression->Expressions ) ) < 2 ) )
                throw new SoapException( 'Expressions must at least contain two elements if Op is a boolean operator (except True).' );
            
            foreach( reset( $expression->Expressions ) as $expressions )
            {
                self::Validate( $expressions, $fieldEnum );
            }
            
            // Done validating boolean operators.
            return;
        }
        
        // Validate compare operators.
        if( !isset( $expression->Field ) )
            throw new SoapException( 'Field must be set if Op is a compare operator.' );

        $reflection = new ReflectionClass( $fieldEnum );
        $fields = $reflection->getConstants();

        if( !in_array( $expression->Field, $fields ) )
            throw new SoapException( 'Field must have one of the following values: ' . implode( ', ', $fields ) . '.' );

        // Field is set and valid, check value(s).
        if( $expression->Op != Operator::In )
        {
            if( !isset( $expression->Value ) )
            {
                if( ( $expression->Op != Operator::Equals ) && ( $expression->Op != Operator::NotEquals ) )
                    throw new SoapException( 'NULL can only be used with Equals and NotEquals operators.' );

                // Avoid undefined property.
                $expression->Value = null;
            }
            else if( is_object( $expression->Value ) )
                throw new SoapException( 'Value must be a scalar type.' );
            
            // Done.
            return;
        }
        
        if( !isset( $expression->Values->object ) &&
            !isset( $expression->SelectExpression )
            )
        {
            throw new SoapException( 'If Op has value In, either Values or SelectExpression must be set.' );
        }

        if( isset( $expression->Values->object ) &&
            is_array( $expression->Values->object )
            )
        {
            if( in_array( '', $expression->Values->object, true ) )
                throw new SoapException( 'The In operator does not handle empty strings, use OR (field = "").' );
            else if( in_array( null, $expression->Values->object, true ) )
                throw new SoapException( 'The In operator does not handle null values, use OR (field = null).' );
        }
    }
    
    public static function Convert( $expression, $columnNames, S\IQueryBuilder $queryBuilder )
    {
        if( ( $expression->Op == Operator::_And ) ||
            ( $expression->Op == Operator::_Or )
            )
        {
            $convertedExpressions = array();

            foreach( reset( $expression->Expressions ) as $expressions )
            {
                $convertedExpressions[] = self::Convert( $expressions, $columnNames, $queryBuilder );
            }

            if( $expression->Op == Operator::_And )
            {
                return new S\BoolAnd( $convertedExpressions );
            }

            return new S\BoolOr( $convertedExpressions );
        }
        else if( $expression->Op == Operator::_True )
        {
            return new S\WhereTrue();   
        }
        
        /**
         * $columnNames is a key => value array containing
         * table column name as key and object property
         * as value. The Field contains the
         * object property but we need the table column
         * name in the query (these could differ because of mapping).
         */
        $columnName = $columnNames[ $expression->Field ];
        
        if( $expression->Op == Operator::In )
        {
            if( isset( $expression->SelectExpression ) )
            {
                $className = $expression->SelectExpression->enc_stype;

                $class = new ReflectionClass( $className );                
                $tableName = $class->getStaticPropertyValue( 'tableName' );
                
                
                $selectExpression = $expression->SelectExpression->enc_value;

                $innerColumnName = $columnNames[ $selectExpression->SelectField ];

                $clause = self::Convert(
                    $selectExpression, $columnNames, $queryBuilder );
                
                return new S\WhereInnerSelect( $tableName, $columnName,
                    Operator::In, $innerColumnName, $clause);
            }
            else
            {
                $values = $expression->Values->object;
            }
            
            if( !is_array( $values ) )
            {
                $values = array( $values );
            }

            return new S\WhereIn( $columnName, $values );
        }
        
        $value = ( isset( $expression->Value ) ? $expression->Value : null );
        
        switch( $expression->Op )
        {                
            case Operator::Equals:
                return new S\WhereEqual( $columnName, $value, $queryBuilder );

            case Operator::NotEquals:
                return new S\WhereNotEqual( $columnName, $value, $queryBuilder );

            case Operator::GreaterThan:
                return new S\WhereGreaterThan( $columnName, $value );

            case Operator::LessThan:
                return new S\WhereLessThan( $columnName, $value );

            case Operator::GreaterThanOrEqual:
                return new S\WhereGreaterThanOrEqual( $columnName, $value );

            case Operator::LessThanOrEqual:
                return new S\WhereLessThanOrEqual( $columnName, $value );

            case Operator::Like:
                return new S\WhereLike( $columnName, $value );

            case Operator::NotLike:
                return new S\WhereNotLike( $columnName, $value );
        }

        throw new SoapException( 'Op field (is None or) has an invalid value.' );
    }
    
}
