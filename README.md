PHPDAL
======

Just another PHP Database Abstraction Library.

I'll finish this README stuff when I do. Don't worry about it.

NOTE: This library is not finished, and will be very full of bugs. At the
moment, this is not recommended for use in production code.

PHPDAL is a PHP Database Abstraction Library designed to provide a very high
level interface to databases using purely object oriented code. The main goals
of this library over similar libraries are the following:

- Simplicity - A database abstraction library should be easier to use than native
    database libraries or SQL queries.

- Speed - Runtime-translation of SQL queries using expensive regex operations is
    a bad practice. This library handles data using pre-written templates, basic
    string concatenation, otherwise you will have to pre-write a query for each
    language you need. This means better performance.

- Maintainability - This library is meant to be modular, easily configurable, and
    easy to develop. There is are focuses on limiting redundant code and enforcing
    a clearly defined standard. Practices used to ensure these focuses are met
    include using object oriented code, strict MVC separation, use of interfaces,
    and use of model or prototype classes and extending them for specific cases.

INSTRUCTIONS: include/require /src/PHPDAL.php in your project and you're ready
to roll.
