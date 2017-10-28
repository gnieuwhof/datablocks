<?php

/**
 * @file
 * @brief This file contains the class Program.
 */

/**
 * @brief First class in the control flow.
 */
class Program
{

    /**
     * @brief OO entry point.
     */
    public static function Main()
    {
        $mvc = new Mvc();

        $mvc->ProcessRequest();
    }

}
