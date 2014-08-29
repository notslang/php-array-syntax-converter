# php-array-syntax-converter
Bidirectional conversion between the old and new array syntaxes in PHP

## features
- convert from `array()` to `[]`
- convert from `[]` to `array()`

## how to use
Install the deps using [composer](https://getcomposer.org/) and run `php <path to old-to-new.php or new-to-old.php>`. All the PHP files in the current directory (and recursive subdirectories) will be converted.

This could use a better CLI that makes use of bash's globbing and just pipes the output to `stdout`... PRs would be welcome for this.

## why?
I wrote this because I want to use PHP 5.4's short array syntax, but if my code needs to run on on something lower than PHP 5.4, and I happen to have not made use of any other PHP 5.4+ features, then I want to also be able to convert it into something that works on that platform.

## credit
Based off of https://gist.github.com/grom358/11387145 and [Pharborist](https://github.com/grom358/pharborist/).
