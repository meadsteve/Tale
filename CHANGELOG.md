# Changelog

## Unreleased changes

### Enhancements

### Bug fixes

### Backwards incompatible changes

## v0.4.0 (2019-04-19)

### Enhancements
* Adds a CloneableState class which triggers automatic copying
of the state between steps.

### Bug fixes
None

### Backwards incompatible changes
None


## v0.3.0 (2019-04-18)

### Enhancements
* Failures from applying compensations are caught allowing the process
to continue and are thrown at the end instead. 

### Bug fixes
None

### Backwards incompatible changes
None

## v0.2.0 (2018-06-28)

### Enhancements
* Add finalising Step interface - useful for committing transactions etc.
* Catch all Errors not just exceptions.
* Add builder to help with constructing steps via calls to ::add(...$args)

### Bug fixes

### Backwards incompatible changes
* ::addStep() made private


## v0.1.0 (2018-06-19)

The first release