<?php

namespace MoneyMath;

class CurrencyTest extends \PHPUnit_Framework_TestCase {
    public function test_formats_amount_1() {
        $chf = new Currency('CHF');
        $this->assertEquals('560.00', $chf->format(new Decimal2('560.00')));
        $this->assertEquals('-1,560.00', $chf->format(new Decimal2('-1560.00')));
    }

    public function test_formats_amount_2() {
        $jpy = new Currency('JPY');
        $this->assertEquals('560', $jpy->format(new Decimal2('560.00')));
        $this->assertEquals('236,800', $jpy->format(new Decimal2('236800.00')));
        $this->assertEquals('-1,000,000,000', $jpy->format(new Decimal2('-1000000000.00')));
        $this->assertEquals('-100,000,000,000', $jpy->format(new Decimal2('-100000000000.00')));
    }

    public function test_formats_amount_3() {
        $eur = new Currency('EUR');
        $this->assertEquals('560,00', $eur->format(new Decimal2('560.00')));
        $this->assertEquals('-1.560,00', $eur->format(new Decimal2('-1560.00')));
        $this->assertEquals('-100.000.000.000,00', $eur->format(new Decimal2('-100000000000.00')));
    }
}

