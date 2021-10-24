<?php

namespace GilDrd\NameGenerator\Tools;

class Parameter
{
    private int $minLength = 0;
    private int $maxLength = 0;
    private bool $letterInTriple = false;
    private bool $noVowelsInName = false;

    public function setLengthsFromNameList(array $nameList): self
    {

        if (0 === $this->maxLength) {
            foreach ($nameList as $name) {
                if (strlen($name) > $this->maxLength) {
                    $this->maxLength = strlen($name);
                }
            }
        }

        if (0 === $this->minLength) {
            $this->minLength = $this->maxLength;

            foreach ($nameList as $name) {
                if (strlen($name) < $this->minLength) {
                    $this->minLength = strlen($name);
                }
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * @param int $minLength
     * @return Parameter
     */
    public function setMinLength(int $minLength): Parameter
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * @param int $maxLength
     * @return Parameter
     */
    public function setMaxLength(int $maxLength): Parameter
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function getLetterInTriple(): bool
    {
        return $this->letterInTriple;
    }

    public function setLetterInTriple(bool $letterInTriple): Parameter
    {
        $this->letterInTriple = $letterInTriple;

        return $this;
    }

    public function getNoVowelsInName(): bool
    {
        return $this->noVowelsInName;
    }

    public function setNoVowelsInName(bool $noVowelsInName): Parameter
    {
        $this->noVowelsInName = $noVowelsInName;

        return $this;
    }
}