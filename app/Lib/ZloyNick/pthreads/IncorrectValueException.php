<?php

namespace App\Lib\ZloyNick\pthreads;

use Exception;

class IncorrectValueException extends Exception
{

    /**
     * IncorrectValueException constructor.
     *
     * @param $variable
     * <p>
     *  Wrong key's name
     * </p>
     */
    public function __construct(string $variable)
    {
        parent::__construct(
            'Incorrect array\'s key given. Pattern of variable must be is A-Z, a-z, 0-9. Key given: '.$variable
        );
    }

}
