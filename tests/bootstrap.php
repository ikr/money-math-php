<?php

require __DIR__ . '/../vendor/autoload.php';

set_error_handler(
    create_function(
        '$a, $b, $c, $d',
        'if (0 == error_reporting()) return; throw new ErrorException($b, 0, $a, $c, $d);'
    ),
    E_ALL
);

mb_internal_encoding("UTF-8");