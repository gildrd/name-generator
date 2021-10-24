<?php

namespace GilDrd\NameGenerator;

use GilDrd\NameGenerator\Exceptions\GenerateFromFileException;
use GilDrd\NameGenerator\Exceptions\IntegrityException;
use GilDrd\NameGenerator\Tools\IntegrityChecker;
use GilDrd\NameGenerator\Tools\Parameter;
use GilDrd\NameGenerator\Tools\Type;

class NameGenerator
{
    private array $firstLetters;
    private array $possibleNextLetters;

    private Parameter $parameter;

    public function __construct(?int $example = null)
    {
        $this->firstLetters = [];
        $this->possibleNextLetters = [];
        $this->parameter = new Parameter();

        if (null !== $example) {
            $this->generateFromInternalFile($example);
        }
    }

    private function generateFromInternalFile(int $example)
    {
        $exampleDirectory = __DIR__.'/examples';

        switch ($example) {
            case Type::MALE_40K_ARCHAIC:
                $handle = fopen($exampleDirectory.'/40k_male_archaic.csv', 'r');
                break;
            case Type::MALE_40K_LOWER_GOTHIC:
                $handle = fopen($exampleDirectory.'/40k_male_lower_gothic.csv', 'r');
                break;
            case Type::MALE_40K_HIGHER_GOTHIC:
                $handle = fopen($exampleDirectory.'/40k_male_hiher_gothic.csv', 'r');
                break;
            case Type::MALE_40K_PRIMITIVE:
                $handle = fopen($exampleDirectory.'/40k_male_primitive.csv', 'r');
                break;
            case Type::MALE_ELVES:
                $handle = fopen($exampleDirectory.'/male_elves.csv', 'r');
                break;
            case Type::FEMALE_ELVES:
                $handle = fopen($exampleDirectory.'/female_elves.csv', 'r');
                break;
            default:
                throw new GenerateFromFileException(sprintf('There is no file configured for ID %s', $example));
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
        $this->getParameter()->setLengthsFromNameList($nameList);

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

        try {
            IntegrityChecker::check($this->parameter, $name);
        } catch (IntegrityException $exception) {
            return $this->generate();
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

    public function getParameter(): Parameter
    {
        return $this->parameter;
    }

    public function setParameter(Parameter $parameter): void
    {
        $this->parameter = $parameter;
    }
}