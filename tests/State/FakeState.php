<?php


namespace MeadSteve\Tale\Tests\State;


use MeadSteve\Tale\State\CloneableState;
use MeadSteve\Tale\State\CloneableStateHelper;

class FakeState implements CloneableState
{
    use CloneableStateHelper;
}