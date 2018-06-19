<?php

namespace MeadSteve\Tale\Steps;

use MeadSteve\Tale\Steps\Step;

interface NamedStep extends Step
{
    /**
     * @return string the public name of this step
     */
    public function stepName(): string;
}
