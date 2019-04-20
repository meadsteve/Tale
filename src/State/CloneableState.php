<?php

namespace MeadSteve\Tale\State;

/**
 * Interface CloneableState
 *
 * How state is represented is up to the consumer of this library.
 * If the state is an object and implements this interface then
 * 'cloneState' will be called before passing the state into each state.
 *
 */
interface CloneableState
{
    /**
     * This method is expected to return a copy of the current object. How
     * this is done is up to the implementer.
     *
     * @return mixed
     */
    public function cloneState();
}
