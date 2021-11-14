[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gildrd/name-generator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gildrd/name-generator/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/gildrd/name-generator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gildrd/name-generator/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/gildrd/name-generator/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

# name-generator
Generate names following Markov algorithm. This way, you will have names that looks "realistic" and not just a random succession of letters.

The way names are generated is only statistic. The more names you have in a list, the more "realitic" the result wille be. Of course, 
some strange results can occur.

# How markov chains work
Each names are analysed and an array is generated, indicating what are the probability a letter is followed by another specific letter.

Per exemple, if you have two words 'aa' and 'ab' :
* First letter will be 'a'
* 'a' will be followed by another 'a', a 'b', or will be the last letter with 33% chance each.
* 'b' will be the last letter


#How to install
Use composer to simply add this package to your project:

```
composer require gildrd/name-generator
```

#How to use
There are several ways to use this package:


## By using built-in names lists
This package comes with some names lists: elves, dwarves and for Warhammer 40K. Others will be added later.

You can use one or more list as references to generate new names:

```
$nameGenerator = new NameGenerator(Type::FEMALE_40K_HIGHER_GOTHIC, Type::FEMALE_40K_LOWER_GOTHIC);
$name = $nameGenerator->generate();
```

## By using your own list
If you don't like the lists included, you can work with your own lists. These can be PHP array or JSON.

### Array list
```
$nameGenerator = new NameGenerator();
$nameGenerator->analyseFromArray(['Riri', 'Fifi', 'Loulou', 'Picsou']);
$name = $nameGenerator->generate();
```

### JSON list
```
$nameGenerator = new NameGenerator();
$nameGenerator->analyseFromJson('["Riri", "Fifi", "Loulou", "Picsou"]');
$name = $nameGenerator->generate();
```

## Setting up some parameters
There are some parameters you can set:
* mininimum length: by default, the shortest name of your list
* maximum length: by default, the longest name of your list
* can a letter be three times in a row: by default false to avoid names such as 'aellla'
* can be a name without vowels: by default false to avoid names such as 'rvrk'

To configure your parameters, create a new Parameter instance:
```
$nameGenerator = new NameGenerator(Type::MALE_40K_ARCHAIC);
$parameter = new Parameter();
$parameter->setLetterInTriple(false)
    ->setMinLength(5)
    ->setMaxLength(9)
    ->setNoVowelsInName(false);
$nameGenerator->setParameter($parameter);
$name = $nameGenerator->generate();
```
