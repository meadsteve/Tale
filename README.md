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
    
    public function execute($state)
    {
        $state['paymentId'] = $this->customerApi->debit($state['Amount']);
        return $state;
    }

    public function compensate($state): void
    {
        $this->customerApi->refundAccountForPayment($state['paymentId'])
    }
```

```php
class BookFlightStep implements Step
{
    //.. Some constructor logic for initialising the api etc...
    
    public function execute($state)
    {
        $state['flightsBookingRef'] = $this->flightApi->buildBooking(
            $state['Destination'], 
            $state['Origin'].
            self::RETURN,
            $this->airline
        );
        if ($state['flightsBookingRef'] === null) {
            raise \Exception("Unable to book flights");
        }
        return $state;
    }

    public function compensate($state): void
    {
        $this->customerApi->cancelFlights($state['flightsBookingRef'])
    }
```

and so on for any of the steps needed. Then in whatever is handling the user's 
request a distributed transaction can be built:

```php
       $transaction = (new Transaction())
            ->addStep(new DebitCustomerBalance($user))
            ->addStep(new BookFlightStep($airlineOfChoice))
            ->addStep(new BookHotelStep())
            ->addStep(new EmailCustomerDetailsOfBookingStep())

        $result = $transaction
            ->run($startingData)
            ->throwFailures()
            ->finalState();
```

If any step along the way fails then the compensate method on each step
is called in reverse order until everything is undone.

## Testing / Development
TODO
