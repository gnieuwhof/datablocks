<?php

/**
 * @file
 * @brief This file contains the class Operator.
 */

/**
 * @brief Enum used to create Expressions.
 * 
 * @enum
 */
class Operator
{
    
    // Being the first, this will be the default in some envirenments...
    const None = 'None';
    
    const _And = 'And';
    
    const _Or = 'Or';
    
    const _True = 'True';
    
    const Equals = 'Equals';
    
    const NotEquals = 'NotEquals';
    
    const GreaterThan = 'GreaterThan';
    
    const LessThan = 'LessThan';
    
    const GreaterThanOrEqual = 'GreaterThanOrEqual';
    
    const LessThanOrEqual = 'LessThanOrEqual';
    
    const Like = 'Like';
    
    const NotLike = 'NotLike';
    
    const In = 'In';
    
}
