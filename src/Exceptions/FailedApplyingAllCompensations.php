<?php


namespace MeadSteve\Tale\Exceptions;

use Throwable;

class FailedApplyingAllCompensations extends \RuntimeException implements TaleException
{
    /**
     * @var Throwable[]
     */
    public $caughtExceptions;

    /**
     * FailedApplyingAllCompensations constructor.
     * @param \Throwable[] $caughtExceptions
     */
    public function __construct($caughtExceptions)
    {
        $previous = null;
        if (sizeof($caughtExceptions) == 1) {
            $previous = $caughtExceptions[0];
        }
        parent::__construct("Failed applying all compensation steps", 0, $previous);
        $this->caughtExceptions = $caughtExceptions;
    }
}
