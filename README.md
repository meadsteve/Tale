Tale
====
[![Build Status](https://travis-ci.org/meadsteve/Tale.svg?branch=master)](https://travis-ci.org/meadsteve/Tale)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/meadsteve/Tale/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/meadsteve/Tale/?branch=master)

## What?
Tale is a small library to help write a "distributed transaction like" 
object across a number of services. It's loosely based on the saga pattern.
A good intro is available on the couchbase blog: 
https://blog.couchbase.com/saga-pattern-implement-business-transactions-using-microservices-part/

## Installation
```bash
composer require mead-steve/tale
```

## Example Usage
An example use case of this would be some holiday booking software broken
down into a few services.

Assuming we have the following services: Flight booking API, Hotel booking API, 
and a Customer API.

We'd write the following steps:

```php
class DebitCustomerBalanceStep implements Step
{
    //.. Some constructor logic for initialising the api etc...
    
    public function execute(CustomerPurchase $state)
    {
        $paymentId = $this->customerApi->debit($state->Amount);
        return $state->markAsPaid($paymentId);
    }

    public function compensate($state): void
    {
        $this->customerApi->refundAccountForPayment($state->paymentId)
    }
```

```php
class BookFlightStep implements Step
{
    //.. Some constructor logic for initialising the api etc...
    
    public function execute(FlightPurchase $state)
    {
        $flightsBookingRef = $this->flightApi->buildBooking(
            $state->Destination, 
            $state->Origin,
            self::RETURN,
            $this->airline
        );
        if ($flightsBookingRef=== null) {
            raise \Exception("Unable to book flights");
        }
        return $state->flightsBooked($flightsBookingRef);
    }

    public function compensate($state): void
    {
        $this->customerApi->cancelFlights($state->flightsBookingRef)
    }
```

and so on for any of the steps needed. Then in whatever is handling the user's 
request a distributed transaction can be built:

```php
       $transaction = (new Transaction())
            ->add(new DebitCustomerBalance($user))
            ->add(new BookFlightStep($airlineOfChoice))
            ->add(new BookHotelStep())
            ->add(new EmailCustomerDetailsOfBookingStep())

        $result = $transaction
            ->run($startingData)
            ->throwFailures()
            ->finalState();
```

If any step along the way fails then the compensate method on each step
is called in reverse order until everything is undone.

## State immutability
The current state is passed from one step to the next. This same state is also
used to compensate for the transactions in the event of a failure further on
in the transaction. Since this is the case it is important that implementations
consider making the state immutable. 

Tale provides a `CloneableState` interface to help with this. Any state implementing
this interface will have its `cloneState` method called before being passed to a step
ensuring that steps won't share references to the same state.
```php
        class FakeState implements CloneableState
        {
                public function cloneState()
                {
                    return clone $this;
                }
        }
        
        $stepOne = new LambdaStep(
            function (MyStateExample $state) {
                $state->mutateTheState = "step one"
                return $state;
            }
        );
        $stepTwo = new LambdaStep(
            function (MyStateExample $state) {
                $state->mutateTheState = "step two"
                return $state;
            }
        );
        $transaction = (new Transaction())
            ->add($stepOne)
            ->add($stepTwo);

        $startingState = new MyStateExample();
        $finalState = $transaction->run($startingState)->finalState();
```
In the example above `$startingState`, `$finalState` and `$state` given to both function
calls are all clones of each other so changing one won't affect any earlier states.

## Testing / Development
TODO
