<?php

namespace GilDrd\NameGenerator;

class NameGenerator
{
    private array $firstLetters;
    private array $possibleNextLetters;

    public const MALE_ELVES = 10;
    public const FEMALE_ELVES = 11;

    public function __construct(?int $example = null)
    {
        $this->firstLetters = [];
        $this->possibleNextLetters = [];

        if (null !== $example) {
            $this->generateFromInternalFile($example);
        }
    }

    private function generateFromInternalFile(int $example)
    {
        $exampleDirectory = __DIR__.'/exemples';

        switch ($example) {
            case self::MALE_ELVES:
                $handle = fopen($exampleDirectory.'/male_elves.csv', 'r');
                break;
            case self::FEMALE_ELVES:
                $handle = fopen($exampleDirectory.'/female_elves.csv', 'r');
                break;
        }

        $list = [];

        while (($data = fgetcsv($handle)) !== false) {
            $list[] = $data[0];
        }
        fclose($handle);

        $this->analyseFromArray($list);
    }

    public function analyseFromArray(array $nameList): void
    {
        $this->firstLetters = $this->getFirstLetters($nameList);
        $this->possibleNextLetters = $this->getSequences($nameList);
    }

    public function analyseFromJson(string $nameList): void
    {
        $array = json_decode($nameList, true);

        $this->analyseFromArray($array);
    }

    public function generate(): string
    {
        $name = '';
        $nextLetter = '';

        $name.= $this->firstLetters[rand(0, count($this->firstLetters)-1)];

        while ($nextLetter !== '*' || strlen($name) < 4) {
            $nextLetter = $this->possibleNextLetters[strtoupper(substr($name, -1))][rand(0, count($this->possibleNextLetters[strtoupper(substr($name,-1))])-1)];
            if ($nextLetter !== '*') {
                $name.= strtolower($nextLetter);
            }
        }

        return $name;
    }

    public function getFirstLetters(array $nameList): array
    {
        asort($nameList);
        $stats = [];
        $availableLetters = [];

        foreach ($nameList as $name) {
            $firstLetter = strtoupper($name[0]);

            $stats[$firstLetter] = array_key_exists($firstLetter, $stats) ?
                (int) round($stats[$firstLetter]+100/count($nameList)) :
                (int) round(100/count($nameList));
        }

        foreach ($stats as $letter => $stat) {
            for ($i = 0; $i < $stat; $i++) {
                $availableLetters[] = $letter;
            }
        }

        return $availableLetters;
    }

    public function getSequences(array $nameList): array
    {
        asort($nameList);
        $stats = [];
        $availableLetters = [];

        foreach ($nameList as $name) {
            $length = strlen($name);

            for ($i = 0; $i < $length; $i++) {
                if ($i+1 <= $length) {
                    $currentLetter = strtoupper($name[$i]);
                    $nextLetter = $i+1 === $length ? '*' : strtolower($name[$i+1]);

                    if (!array_key_exists($currentLetter, $stats)) {
                        $stats[$currentLetter] = [];
                    }

                    $stats[$currentLetter][$nextLetter] = array_key_exists($nextLetter, $stats[$currentLetter]) ?
                        $stats[$currentLetter][$nextLetter] + 1 : 1;

                    ksort($stats[$currentLetter]);
                }
            }
        }
        ksort($stats);

        foreach ($stats as $letter => $stat) {
            $total = 0;

            $availableLetters[$letter] = [];

            foreach ($stat as $quantity) {
                $total = $total + $quantity;
            }

            foreach ($stat as $nextLetter => $quantity) {
                $percent = (int) round($quantity * 100 / $total);

                for ($i = 0; $i < $percent; $i++) {
                    $availableLetters[$letter][] = $nextLetter;
                }
            }
        }

        return $availableLetters;
    }
}