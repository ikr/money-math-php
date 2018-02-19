<?php

namespace MoneyMath;

/**
 * An arbitrary big decimal number with 2-digit fraction part, such as 399.99, 100.00, or
 * 99999999999999999999999999999999999999999999999999999999999999999999999999999999.99
 */
class Decimal2 {
    const SEPARATOR = '.';

    /**
     * @var string
     */
    private $cents;

    /**
     * @param string $stringRepresentation Fraction part must be separated from integer part with
     * the dot (.) symbol.
     */
    public function __construct($stringRepresentation) {
        if (false === strpos($stringRepresentation, self::SEPARATOR)) {
            $this->cents = gmp_strval(
                gmp_mul(
                    gmp_init($stringRepresentation, 10),
                    100
                )
            );

            return;
        }

        $parts = explode(self::SEPARATOR, $stringRepresentation);

        while (strlen(trim($parts[1])) < 2) {
            $parts[1] .= '0';
        }

        $is_negative = ('-' === trim($stringRepresentation[0]));

        $this->cents = gmp_strval(
            gmp_add(
                gmp_mul(
                    gmp_abs(gmp_init($parts[0], 10)),
                    100
                ),
                self::roundToHundred(trim($parts[1]))
            )
        );

        if ($is_negative) {
            $this->cents = gmp_strval(
                gmp_neg(
                    gmp_init($this->cents, 10)
                )
            );
        }
    }

//--------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function __toString() {
        return $this->integerValue() . self::SEPARATOR
            . (abs($this->fractionValue()) < 10? '0' : '') . abs($this->fractionValue());
    }

    /**
     * The integer part of this amount (without cents part).
     *
     * @return integer Or string, if the number is too big.
     */
    public function integerValue() {
        $sign = (
            gmp_cmp(
                gmp_init($this->cents, 10), 0
            ) < 0
        )? '-' : '';

        return $sign . gmp_strval(
            gmp_div_q(
                gmp_abs(
                    gmp_init($this->cents, 10)
                ),
                100
            ),
            10
        );
    }

    /**
     * Returns the amount of hundredth's in this decimal fraction part. That would be cents for,
     * say, an USD amount.
     *
     * @return integer
     */
    public function fractionValue() {
        $ret = gmp_div_r(
            gmp_abs(
                gmp_init($this->cents, 10)
            ),
            100
        );

        if (gmp_cmp(gmp_init($this->cents, 10), 0) < 0) {
            $ret = gmp_neg($ret);
        }

        return gmp_intval($ret);
    }

    /**
     * @return integer Or string, if the number is too big.
     */
    public function centsValue() {
        if (gmp_cmp(gmp_abs(gmp_init($this->cents, 10)), PHP_INT_MAX) > 0) {
            return $this->cents;
        }

        return gmp_intval(gmp_init($this->cents, 10));
    }

//--------------------------------------------------------------------------------------------------

    /**
     * Creates a new decimal which is a sum of the passed $a and $b.
     *
     * @param Decimal2 $a
     * @param Decimal2 $b
     * @return Decimal2
     */
    public static function plus(Decimal2 $a, Decimal2 $b) {
        $ret = new Decimal2('0');

        $ret->cents = gmp_strval(
            gmp_add(
                gmp_init($a->cents, 10),
                gmp_init($b->cents, 10)
            )
        );

        return $ret;
    }

    /**
     * Returns the sum of all the decimal numbers in the array.
     *
     * @param array $decimals The array of Decimal2 objects.
     *
     * @return Decimal2 or boolean false for an empty $decimals array.
     */
    public static function sum(array $decimals) {
        $ret = new Decimal2('0');

        foreach ($decimals as $d) {
            $ret = Decimal2::plus($ret, $d);
        }

        return $ret;
    }

    /**
     * Returns the average of all the decimal numbers in the array.
     *
     * @param array $decimals The array of Decimal2 objects.
     *
     * @return Decimal2 or boolean false for an empty $decimals array.
     */
    public static function avg(array $decimals) {
        if (!count($decimals)) return false;

        return Decimal2::div(
            Decimal2::sum($decimals),
            new Decimal2(count($decimals))
        );
    }

    /**
     * Creates a new decimal which is a difference of the passed $a and $b.
     *
     * @param Decimal2 $a
     * @param Decimal2 $b
     * @return Decimal2
     */
    public static function minus(Decimal2 $a, Decimal2 $b) {
        $ret = new Decimal2('0');

        $ret->cents = gmp_strval(
            gmp_add(
                gmp_init($a->cents, 10),
                gmp_neg(gmp_init($b->cents, 10))
            )
        );

        return $ret;
    }

    /**
     * Creates a new decimal which is a result of a multiplication of the passed decimal by the
     * passed integer factor.
     *
     * @param Decimal2 $decimal
     * @param integer $byIntFactor
     * @return Decimal2
     */
    public static function multiply(Decimal2 $decimal, $byIntFactor) {
        $ret = new Decimal2('0');

        $ret->cents = gmp_strval(
            gmp_mul(
                gmp_init($decimal->cents, 10),
                gmp_init($byIntFactor)
            )
        );

        return $ret;
    }

    /**
     * Creates a new decimal which is a result of a multiplication of the passed decimals.
     *
     * @param Decimal2 $a
     * @param Decimal2 $b
     * @return Decimal2
     */
    public static function mul(Decimal2 $a, Decimal2 $b) {
        $ret = new Decimal2('0');

        $ret->cents = gmp_strval(
            gmp_div_q(
                gmp_mul(
                    gmp_init($a->cents, 10),
                    gmp_init($b->cents, 10)
                ),
                100
            )
        );

        return $ret;
    }

    /**
     * Creates a new decimal which is a result of a division of $a by $b.
     *
     * @param Decimal2 $a
     * @param Decimal2 $b
     * @return Decimal2
     */
    public static function div(Decimal2 $a, Decimal2 $b) {
        $strA = strval($a);
        $strB = strval($b);

        $sign_a = ('-' === $strA[0]) ? -1 : 1;
        $sign_b = ('-' === $strB[0])? -1 : 1;

        $ret = new Decimal2('0');

        $aAbsCentsMul100 = gmp_mul(
            gmp_abs(gmp_init($a->cents, 10)),
            100
        );

        $bAbsCents = gmp_abs(gmp_init($b->cents, 10));

        $retCents = gmp_div_q(
            $aAbsCentsMul100,
            $bAbsCents,
            GMP_ROUND_ZERO
        );

        $retCentsMod = gmp_mod($aAbsCentsMul100, $bAbsCents);

        if (gmp_cmp($retCentsMod, gmp_sub($bAbsCents, $retCentsMod)) >=0 ) {
            $retCents = gmp_add($retCents, 1);
        }

        $ret->cents = gmp_strval($retCents);

        if (($sign_a * $sign_b) < 0) {
            $ret->cents = gmp_strval(
                gmp_neg(
                    gmp_init($ret->cents, 10)
                )
            );
        }

        return $ret;
    }

    /**
     * Returns the specified amount of percents of the passed $decimal value.
     *
     * @param Decimal2 $decimal
     *
     * @param Decimal2 $percents
     *
     * @return Decimal2
     */
    public static function getPercentsOf(Decimal2 $decimal, Decimal2 $percents) {
        $ret = new Decimal2(strval($decimal));

        $ret->cents = gmp_strval(
            gmp_mul(
                gmp_init($ret->cents, 10),
                gmp_init($percents->cents, 10)
            )
        );

        $ret->cents = gmp_strval(
            gmp_div_q(
                gmp_init($ret->cents, 10),
                10000,
                GMP_ROUND_PLUSINF
            )
        );

        return $ret;
    }

    /**
     * Comparison operator.
     *
     * @param Decimal2 $a
     *
     * @param Decimal2 $b
     *
     * @return integer Returns a positive value if a > b, zero if a = b and a negative value
     * if a < b
     */

    public static function cmp(Decimal2 $a, Decimal2 $b) {
        return gmp_cmp(
            gmp_init($a->cents, 10),
            gmp_init($b->cents, 10)
        );
    }

    /**
     * @param Decimal2 $d
     * @return Decimal2
     */
    public static function roundUpTo5Cents(Decimal2 $d) {
        $lastDigit = intval(substr(strval($d), -1));
        $additon = 0;

        if (($lastDigit % 5) != 0) {
            $additon = '0.0' . strval(5 - ($lastDigit % 5));
        }

        return self::plus($d, new Decimal2($additon));
    }

//--------------------------------------------------------------------------------------------------

    private static function roundToHundred($num) {
        $digitsCount = strlen($num);
        if ($digitsCount < 3) return gmp_init($num, 10);

        $divider = gmp_pow(10, $digitsCount - 2);
        $addition = gmp_mul(5, gmp_pow(10, $digitsCount - 3));

        return gmp_div_q(
            gmp_add(gmp_init($num, 10), $addition),
            $divider,
            GMP_ROUND_ZERO);
    }
}
