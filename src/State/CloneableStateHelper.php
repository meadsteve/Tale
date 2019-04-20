<?php


namespace MeadSteve\Tale\State;


trait CloneableStateHelper
{
    public function cloneState() {
        return clone $this;
    }
}