<?php

namespace MeadSteve\Tale;

interface NamedStep extends Step
{
    /**
     * @return string the public name of this step
     */
    public function stepName(): string;
}
