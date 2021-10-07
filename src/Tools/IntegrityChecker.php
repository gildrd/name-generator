<?php

namespace GilDrd\NameGenerator\Tools;

use GilDrd\NameGenerator\Exceptions\IntegrityException;

class IntegrityChecker
{
    /**
     * @throws IntegrityException
     */
    public static function check(Parameter $parameter, string $name): void
    {
        self::checkMinLength($parameter, $name);
        self::checkMaxLength($parameter, $name);
        self::checkTripleLetters($parameter, $name);
    }

    /**
     * @throws IntegrityException
     */
    private static function checkMinLength(Parameter $parameter, string $name): void
    {
        if (strlen($name) < $parameter->getMinLength()) {
            throw new IntegrityException(
                sprintf('Generated name \'%s\' is shorter than expected (%s for %s)',
                    $name, strlen($name), $parameter->getMinLength()
                )
            );
        }
    }

    /**
     * @throws IntegrityException
     */
    private static function checkMaxLength(Parameter $parameter, string $name): void
    {
        if (strlen($name) > $parameter->getMaxLength()) {
            throw new IntegrityException(
                sprintf('Generated name \'%s\' is longer than expected (%s for %s)',
                    $name, strlen($name), $parameter->getMaxLength()
                )
            );
        }
    }

    /**
     * @throws IntegrityException
     */
    private static function checkTripleLetters(Parameter $parameter, string $name): void
    {
        if ($parameter->getLetterInTriple()) {
            return;
        }

        $maxCheck = strlen($name) - 2;

        for ($i = 0; $i < strlen($name); $i++) {
            if ($i === $maxCheck) {
                break;
            }

            if ($name[$i] === $name[$i+1] && $name[$i] === $name[$i+2]) {
                throw new IntegrityException(
                    sprintf('Letter \'%s\' can\'t be three times in a row in name %s',
                        $name[$i], $name
                    )
                );
            }
        }
    }
}