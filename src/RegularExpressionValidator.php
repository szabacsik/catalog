<?php

namespace Szabacsik\Catalog;

use InvalidArgumentException;

class RegularExpressionValidator
{
    public static function validate(string $regularExpression): void
    {
        if (@preg_match($regularExpression, '') === false) {
            throw new InvalidArgumentException("The given `$regularExpression` is an invalid regular expression");
        }
    }
}
