<?php

namespace MoneyMath;

class Decimal2Test extends \PHPUnit_Framework_TestCase {
    public function testParsesInteger1() {
        $this->assertD(new Decimal2('3456'), 3456, 0, '3456.00');
    }

    public function testParsesNegativeInteger() {
        $this->assertD(new Decimal2('-13'), -13, 0, '-13.00');
    }

    public function testParsesInteger2() {
        $this->assertD(new Decimal2('003456'), 3456, 0, '3456.00');
    }

    public function testParsesIntegerZero1() {
        $this->assertD(new Decimal2('0'), 0, 0, '0.00');
    }

    public function testParsesIntegerZero2() {
        $this->assertD(new Decimal2('000000'), 0, 0, '0.00');
    }

    public function testParsesFractionZero() {
        $this->assertD(new Decimal2('0.0'), 0, 0, '0.00');
    }

    public function testParsesFraction1() {
        $this->assertD(new Decimal2('399.9'), 399, 90, '399.90');
    }

    public function testParsesFraction2() {
        $this->assertD(new Decimal2('1.05'), 1, 5, '1.05');
    }

    public function testParsesFraction3() {
        $this->assertD(new Decimal2('210.00'), 210, 0, '210.00');
    }

    public function testParsesFraction4() {
        $this->assertD(new Decimal2('0.99'), 0, 99, '0.99');
    }

    public function testParsesNegativeFraction1() {
        $this->assertD(new Decimal2('-1.1'), -1, -10, '-1.10');
    }

    public function testParsesNegativeFraction2() {
        $this->assertD(new Decimal2('-14.05'), -14, -5, '-14.05');
    }

//--------------------------------------------------------------------------------------------------

    public function testPositiveDecimalsCanBeSummed1() {
        $a = new Decimal2('16.11');
        $b = new Decimal2('17.07');
        $this->assertD(Decimal2::plus($a, $b), 33, 18, '33.18');
    }

    public function testPositiveDecimalsCanBeSummed2() {
        $a = new Decimal2('65535.79');
        $b = new Decimal2('1024.85');
        $this->assertD(Decimal2::plus($a, $b), 66560, 64, '66560.64');
    }

    public function testPositiveDecimalsCanBeSummed3() {
        $a = new Decimal2('1.99');
        $b = new Decimal2('0.02');
        $this->assertD(Decimal2::plus($a, $b), 2, 1, '2.01');
    }

    public function testNegativeDecimalsCanBeSummed() {
        $a = new Decimal2('-1.99');
        $b = new Decimal2('-0.02');
        $this->assertD(Decimal2::plus($a, $b), -2, -1, '-2.01');
    }

    public function testMixedDecimalsCanBeSummed1() {
        $a = new Decimal2('-1.99');
        $b = new Decimal2('1.99');
        $this->assertD(Decimal2::plus($a, $b), 0, 0, '0.00');
    }

    public function testMixedDecimalsCanBeSummed2() {
        $a = new Decimal2('-1.99');
        $b = new Decimal2('0.98');
        $this->assertD(Decimal2::plus($a, $b), -1, -1, '-1.01');
    }

    public function testMixedDecimalsCanBeSummedInChain() {
        $a = new Decimal2('194.00');
        $b = new Decimal2('23.30');
        $c = new Decimal2('210.00');
        $d = new Decimal2('355.00');

        $this->assertD(
            Decimal2::plus($a, Decimal2::plus($b, Decimal2::plus($c, $d))), 782, 30, '782.30');
    }

    public function testDecimalsAdditionIsCommutative() {
        $a = new Decimal2('194.00');
        $b = new Decimal2('23.30');

        $this->assertEquals(strval(Decimal2::plus($a, $b)), strval(Decimal2::plus($b, $a)));
    }

//--------------------------------------------------------------------------------------------------

    public function testDecimalCanBeMultiplied1() {
        $d = new Decimal2('-1.99');
        $this->assertD(Decimal2::multiply($d, 100), -199, 0, '-199.00');
    }

    public function testDecimalCanBeMultiplied2() {
        $d = new Decimal2('150');
        $this->assertD(Decimal2::multiply($d, 2), 300, 0, '300.00');
    }

    public function testDecimalCanBeMultiplied3() {
        $d = new Decimal2('1.55');
        $this->assertD(Decimal2::multiply($d, -10), -15, -50, '-15.50');
    }

    public function testDecimalCanBeMultiplied4() {
        $d = new Decimal2('210.0');
        $this->assertD(Decimal2::multiply($d, 5), 1050, 0, '1050.00');
    }

//--------------------------------------------------------------------------------------------------

    public function testSurvivesBigNumbers1() {
        $a = new Decimal2('9000000000.00');
        $b = new Decimal2('9000000000.00');
        $this->assertD(Decimal2::plus($a, $b), '18000000000', 0, '18000000000.00');
    }

    public function testSurvivesBigNumbers2() {
        $a = new Decimal2('9000000000.20');
        $this->assertD(Decimal2::multiply($a, 1), '9000000000', 20, '9000000000.20');
    }

    public function testSurvivesBigNumbers3() {
        $a = new Decimal2('9000000000.20');
        $this->assertD(Decimal2::multiply($a, 5), '45000000001', 0, '45000000001.00');
    }

    public function testSurvivesBigNumbers4() {
        $a = new Decimal2('9000000000.20');
        $this->assertD(Decimal2::multiply($a, -5), '-45000000001', 0, '-45000000001.00');
    }

    public function testSurvivesBigNumbers5() {
        $a = new Decimal2('99999999999999999999999999999999999999999999999999999999999999999999999999999999.99');

        $this->assertEquals(
            '100000000000000000000000000000000000000000000000000000000000000000000000000000000.00',
            strval(Decimal2::plus($a, new Decimal2('0.01')))
        );
    }

//--------------------------------------------------------------------------------------------------

    public function testReturnsCents1() {
         $a = new Decimal2('-9000000000.20');
         $this->assertEquals('-900000000020', $a->centsValue());
    }

    public function testReturnsCents2() {
         $a = new Decimal2('1.99');
         $this->assertEquals(199, $a->centsValue());
    }

//--------------------------------------------------------------------------------------------------

    public function testHandlesGracefullyTooPreciseValues1() {
        $a = new Decimal2('0.001');
        $this->assertEquals('0.00', strval($a));

        $b = new Decimal2('0.009');
        $this->assertEquals('0.01', strval($b));

        $this->assertEquals('0.01', strval(Decimal2::plus($a, $b)));
    }

    public function testHandlesGracefullyTooPreciseValues2() {
        $a = new Decimal2('1597.847056');
        $this->assertEquals('1597.85', strval($a));
    }

    public function testHandlesGracefullyTooPreciseValues3() {
        $a = new Decimal2('-7.455');
        $this->assertEquals('-7.46', strval($a));
    }

    public function testHandlesGracefullyTooPreciseValues4() {
        $a = new Decimal2('7.455');
        $this->assertEquals('7.46', strval($a));
    }

    public function testHandlesGracefullyTooPreciseValues5() {
        $a = new Decimal2('7.450');
        $this->assertEquals('7.45', strval($a));
    }

    public function testHandlesGracefullyTooPreciseValues6() {
        $a = new Decimal2('7.451');
        $this->assertEquals('7.45', strval($a));
    }

    public function testHandlesGracefullyTooPreciseValues7() {
        $a = new Decimal2('7.454');
        $this->assertEquals('7.45', strval($a));
    }

//--------------------------------------------------------------------------------------------------

    public function testCalculatesPercents1() {
        $d = new Decimal2('100');
        $p = new Decimal2('7.45');
        $this->assertEquals('7.45', strval(Decimal2::getPercentsOf($d, $p)));
    }

    public function testCalculatesPercents2() {
        $d = new Decimal2('100');
        $p = new Decimal2('7.60');
        $this->assertEquals('7.60', strval(Decimal2::getPercentsOf($d, $p)));
    }

    public function testCalculatesPercents3() {
        $d = new Decimal2('100');
        $p = new Decimal2('0.01');
        $this->assertEquals('0.01', strval(Decimal2::getPercentsOf($d, $p)));
    }

    public function testCalculatesPercents4() {
        $d = new Decimal2('100');
        $p = new Decimal2('110');
        $this->assertEquals('110.00', strval(Decimal2::getPercentsOf($d, $p)));
    }

    public function testCalculatesPercents5() {
        $d = new Decimal2('-200');
        $p = new Decimal2('3.25');
        $this->assertEquals('-6.50', strval(Decimal2::getPercentsOf($d, $p)));
    }

    public function testCalculatesPercents6() {
        $d = new Decimal2('0.50');
        $p = new Decimal2('33.00');
        $this->assertEquals('0.17', strval(Decimal2::getPercentsOf($d, $p)));
    }

//--------------------------------------------------------------------------------------------------

    public function testMultipliesTwoDecimals1() {
        $a = new Decimal2('-2');
        $b = new Decimal2('2');
        $this->assertEquals('-4.00', strval(Decimal2::mul($a, $b)));
    }

    public function testMultipliesTwoDecimals2() {
        $a = new Decimal2('24.0');
        $b = new Decimal2('0.25');
        $this->assertEquals('6.00', strval(Decimal2::mul($a, $b)));
    }

//--------------------------------------------------------------------------------------------------

    public function testDividesTwoDecimals1() {
        $a = new Decimal2('2');
        $b = new Decimal2('2');
        $this->assertEquals('1.00', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals2() {
        $a = new Decimal2('-1');
        $b = new Decimal2('4');
        $this->assertEquals('-0.25', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals3() {
        $a = new Decimal2('-399');
        $b = new Decimal2('-1.2');
        $this->assertEquals('332.50', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals4() {
        $a = new Decimal2('140.10');
        $b = new Decimal2('1.55');
        $this->assertEquals('90.39', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals5() {
        $a = new Decimal2('210');
        $b = new Decimal2('1.55');
        $this->assertEquals('135.48', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals6() {
        $a = new Decimal2('45.99');
        $b = new Decimal2('-1');
        $this->assertEquals('-45.99', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals7() {
        $a = new Decimal2('0');
        $b = new Decimal2('-1');
        $this->assertEquals('0.00', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals8() {
        $a = new Decimal2('2');
        $b = new Decimal2('3');
        $this->assertEquals('0.67', strval(Decimal2::div($a, $b)));
    }

    public function testDividesTwoDecimals9() {
        $a = new Decimal2('0.02');
        $b = new Decimal2('0.03');
        $this->assertEquals('0.67', strval(Decimal2::div($a, $b)));
    }

//--------------------------------------------------------------------------------------------------

    public function testCalculatesDifference1() {
        $a = new Decimal2('700000000000000000000');
        $b = new Decimal2('700000000000000000000');
        $this->assertEquals('0.00', strval(Decimal2::minus($a, $b)));
    }

    public function testCalculatesDifference2() {
        $a = new Decimal2('-10');
        $b = new Decimal2('5');
        $this->assertEquals('-15.00', strval(Decimal2::minus($a, $b)));
    }

//--------------------------------------------------------------------------------------------------

    public function testAddsPercentsToAValue1() {
        $a = new Decimal2('377.80');
        $p = Decimal2::getPercentsOf($a, new Decimal2('1.00'));

        $this->assertEquals('3.78', strval($p));
    }

//--------------------------------------------------------------------------------------------------

    public function testSumsDecimalsInEmptyArray() {
        $this->assertEquals(
            '0.00',
            strval(
                Decimal2::sum([])
            )
        );
    }

    public function testSumsDecimalsInArray1() {
        $this->assertEquals(
            '3.00',
            strval(
                Decimal2::sum([
                    new Decimal2('1'),
                    new Decimal2('1'),
                    new Decimal2('2'),
                    new Decimal2('-1')
                ])
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    public function testAveragesDecimalsInEmptyArray() {
        $this->assertFalse(Decimal2::avg([]));
    }

    public function testAveragesDecimalsInOneElementArray() {
        $this->assertEquals(
            '100000000000.99',
            strval(
                Decimal2::avg([
                    new Decimal2('100000000000.99')
                ])
            )
        );
    }

    public function testAveragesDecimalsInArray1() {
        $this->assertEquals(
            '1.00',
            strval(
                Decimal2::avg([
                    new Decimal2('1'),
                    new Decimal2('1'),
                    new Decimal2('1')
                ])
            )
        );
    }

    public function testAveragesDecimalsInArray2() {
        $this->assertEquals(
            '1.50',
            strval(
                Decimal2::avg([
                    new Decimal2('1'),
                    new Decimal2('2'),
                    new Decimal2('1'),
                    new Decimal2('2')
                ])
            )
        );
    }

    public function testAveragesDecimalsInArray3() {
        $this->assertEquals(
            '0.33',
            strval(
                Decimal2::avg([
                    new Decimal2('-1'),
                    new Decimal2('2'),
                    new Decimal2('0')
                ])
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    public function testDecimalsCanBeCompared() {
        $this->assertEquals(
            0,
            Decimal2::cmp(new Decimal2('-1'), new Decimal2('-1'))
        );

        $this->assertEquals(
            0,
            Decimal2::cmp(new Decimal2('111119898989898.23'), new Decimal2('111119898989898.23'))
        );

        $this->assertGreaterThan(
            0,
            Decimal2::cmp(new Decimal2('1.01'), new Decimal2('1'))
        );

        $this->assertLessThan(
            0,
            Decimal2::cmp(new Decimal2('-0'), new Decimal2('100'))
        );
    }

//--------------------------------------------------------------------------------------------------

    public function testRoundsUpZeroTo5Cents() {
        $this->assertEquals('0.00', strval(Decimal2::roundUpTo5Cents(new Decimal2('0'))));
    }

    public function testRoundsUpTo5CentsDoesNothingToA5CentsRoundAmount1() {
        $this->assertEquals(
            '1.05', strval(Decimal2::roundUpTo5Cents(new Decimal2('1.05'))));
    }

    public function testRoundsUpTo5CentsDoesNothingToA5CentsRoundAmount2() {
        $this->assertEquals(
            '1.10', strval(Decimal2::roundUpTo5Cents(new Decimal2('1.10'))));
    }

    public function testRoundsUpTo5Cents1() {
        $this->assertEquals(
            '0.05', strval(Decimal2::roundUpTo5Cents(new Decimal2('0.02'))));
    }

    public function testRoundsUpTo5Cents2() {
        $this->assertEquals(
            '0.10', strval(Decimal2::roundUpTo5Cents(new Decimal2('0.06'))));
    }

//--------------------------------------------------------------------------------------------------

    /**
     * @param Decimal2 $d
     * @param integer $int
     * @param integer $fraction
     * @param string $strRep
     */
    private function assertD($d, $int, $fraction, $strRep) {
        $this->assertEquals($int, $d->integerValue());
        $this->assertEquals($fraction, $d->fractionValue());
        $this->assertEquals($strRep, strval($d));
    }
}
