<?php

namespace MoneyMath;

class Currency {
    /**
     * 3-letter code, like USD, EUR, CHF.
     *
     * @var string
     */
    private $code;

    public function __construct($code) {
        $this->code = $code;
    }

    /**
     * @param Decimal2 $amount
     * @return string
     */
    public function format(Decimal2 $amount) {
        switch ($this->code) {
            case 'JPY':
                return self::separateThousands(strval($amount->integerValue()), ',');

            case 'EUR':
            case 'GBP':
                return self::separateThousands(strval($amount->integerValue()), '.') . ','
                    . substr(strval($amount), -2);

            case 'CHF':
            case 'USD':
                return self::separateThousands(strval($amount->integerValue()), ',') . '.'
                    . substr(strval($amount), -2);

            default:
                return strval($amount);
        }
    }

//--------------------------------------------------------------------------------------------------

    private static function separateThousands($in_str, $with_str) {
        $sign = '';
        $src = $in_str;

        if ('-' == $in_str[0]) {
            $sign = '-';
            $src = substr($src, 1);
        }


        $ret = '';

        while (strlen($src) > 0) {
            if (strlen($ret) > 0) {
                $ret = $with_str . $ret;
            }

            if (strlen($src) <= 3) {
                $ret = $src . $ret;
                break;
            }

            $appendix = substr($src, strlen($src) - 3, 3);
            $ret = $appendix . $ret;
            $src = substr($src, 0, strlen($src) - 3);
        }

        return $sign . $ret;
    }
}

