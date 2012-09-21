[![Build Status](https://secure.travis-ci.org/ikr/money-math-php.png)](http://travis-ci.org/ikr/money-math-php)

# What does it do?

Arithmetic operations on currency amounts. Amounts on input and output are arbitrary large and
precise:

    99999999999999999999999999999999999999999999999999999999999999999999999999999999.99
    +
    0.01
    =
    100000000000000000000000000000000000000000000000000000000000000000000000000000000.00

However, in cases when the division is involved — like for percentage calculation — the result is
rounded to the whole cent: 33% of $0.50 is $0.17 instead of $0.165

As a bonus feature, there's a simple formatting function for amounts in CHF, EUR, USD, GBP, and JPY.

# Why does it exist?

Because storing currency amounts in floats [is a really bad idea](http://stackoverflow.com/questions/3730019/why-not-use-double-or-float-to-represent-currency)

# How to use it?

## Installation

Install via [Composer package manager](http://packagist.org):


Just create a `composer.json` file for your project:

    {
        "require": {
            "ikr/money-math": "0.1.*"
        }
    }

And run these two commands to install the Composer dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

Now you can add the Composer's autoloader, and you will have access to the `MoneyMath\*` classes:

    <?php
    require 'vendor/autoload.php';

## API

    use MoneyMath\Decimal2;
    use MoneyMath\Currency;

    $a = new Decimal2("3.50");
    $b = new Decimal2("4.50");

    $a->integerValue()                          // 3
    $a->fractionValue()                         // 50
    $a->centsValue()                            // 350

    strval(Decimal2::plus($a, $b))              // "8.00"
    strval(Decimal2::sum([$a, $a, $b]))         // "11.50"
    strval(Decimal2::avg([$a, $b]))             // "4.00"
    strval(Decimal2::minus($a, $b))             // "-1.00"
    strval(Decimal2::multiply($a, 2))           // "7.00"
    strval(Decimal2::mul($a, $b))               // "15.75"

    strval(Decimal2::div($a, $b))               // "0.78"
    strval(Decimal2::getPercentsOf($a, $b))     // "0.16" b% of a
    strval(Decimal2::cmp($a, $b))               // -1

And last, but not least :)

    $c = new Decimal2("42.02");
    strval(Decimal2::roundUpTo5Cents($c))       // "42.05"

Which we use for bills in CHF that are required by law to be 0 (mod 5).

For formatting please use the `Currency` class

    (new Currency('EUR'))->format(new Decimal2('-100000000000.00')) // -100.000.000.000,00

# License (MIT)

Copyright (c) 2012 Ivan Krechetov

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
