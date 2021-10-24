<?php

namespace GilDrd\NameGenerator\Tests;

use GilDrd\NameGenerator\Exceptions\IntegrityException;
use GilDrd\NameGenerator\Tools\IntegrityChecker;
use GilDrd\NameGenerator\Tools\Parameter;
use \PHPUnit\Framework\TestCase;

class IntegrityCheckerTest extends TestCase
{


    public function testIntegrityMinlength()
    {
        $parameter = new Parameter();
        $parameter->setMinLength(3)
            ->setMaxLength(5);

        $this->expectException(IntegrityException::class);
        IntegrityChecker::check($parameter, 'la');
    }

    public function testIntegrityMaxlength()
    {
        $parameter = new Parameter();
        $parameter->setMinLength(3)
            ->setMaxLength(5);

        $this->expectException(IntegrityException::class);
        IntegrityChecker::check($parameter, 'azerty');
    }

    public function testIntegrityTripleLetter()
    {
        $parameter = new Parameter();
        $parameter->setMinLength(3)
            ->setMaxLength(7)
            ->setLetterInTriple(false);

        $this->expectException(IntegrityException::class);
        IntegrityChecker::check($parameter, 'azreee');
    }

    public function testIntegrityNoVowels()
    {
        $parameter = new Parameter();
        $parameter->setMinLength(3)
            ->setMaxLength(7)
            ->setNoVowelsInName(false);

        $this->expectException(IntegrityException::class);
        IntegrityChecker::check($parameter, 'zrtplm');
    }

    public function testIntegrityNoVowelsPositionedToTrue()
    {
        $parameter = new Parameter();
        $parameter->setMinLength(3)
            ->setMaxLength(7)
            ->setNoVowelsInName(true);

        IntegrityChecker::check($parameter, 'zrtplm');
        $this->assertTrue(true);
    }
}