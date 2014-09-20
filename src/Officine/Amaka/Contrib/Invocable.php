<?php

/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Contrib;

/**
 * Implement this interface to inject custom objects and leverage the
 * Amaka build workflow.
 */
interface Invocable
{
    /** NOTE: Please remember to implement the invoke method
     * the prototype has been commented out to allow its signature
     * to be changed.
     *
     * Otherwise we'd force client code to always access the arguments with func_get_args.
     *
     * Please bear with me as I will soon clean up this mess.
     */
    /** public function invoke(..); */
}
