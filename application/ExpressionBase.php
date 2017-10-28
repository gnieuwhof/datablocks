<?php

/**
 * @file
 * @brief This file contains the class ExpressionBase.
 */

/**
 * @brief Class used to create ntity where expressions.
 * 
 * DataBlocks generator creates subclasses per type.
 */
class ExpressionBase
{

    /**
     * @var Operator $Op
     */
    public $Op;
    
    /**
     * @var object $Value
     */
    public $Value;
    
    /**
     * @var object[] $Values
     */
    public $Values;
    
    /**
     * @var object $SelectExpression
     */
    public $SelectExpression;

}
