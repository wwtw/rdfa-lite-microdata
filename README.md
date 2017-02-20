# jkphl/rdfa-lite-microdata

> RDFa Lite 1.1 and HTML Microdata parser for web documents (HTML, SVG, XML)

[![Build Status][travis-image]][travis-url] [![Coverage Status][coveralls-image]][coveralls-url] [![Scrutinizer Code Quality][scrutinizer-image]][scrutinizer-url]  [![Documentation Status][readthedocs-image]][readthedocs-url]  [![Clear architecture][clear-architecture-image]][clear-architecture-url]

## Documentation

Please find the [project documentation](doc/index.md) in the `doc` directory. We recommend [reading it](http://jkphl-rdfa-lite-microdata.readthedocs.io/) via *Read the Docs*.

## Installation

This library requires PHP >=5.5 or later. I recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

It is installable and autoloadable via [Composer](https://getcomposer.org/) as [jkphl/rdfa-lite-microdata](https://packagist.org/packages/jkphl/rdfa-lite-microdata).

```bash
composer require jkphl/rdfa-lite-microdata
```

Alternatively, [download a release](https://github.com/jkphl/rdfa-lite-microdata/releases) or clone this repository, then require or include its [`autoload.php`](autoload.php) file.


## Dependencies

![Composer dependency graph](https://rawgit.com/jkphl/rdfa-lite-microdata/master/doc/dependencies.svg)

## Quality

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If you notice compliance oversights, please send a patch via pull request.

## Contributing

Found a bug or have a feature request? [Please have a look at the known issues](https://github.com/jkphl/rdfa-lite-microdata/issues) first and open a new issue if necessary. Please see [contributing](CONTRIBUTING.md) and [conduct](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email joschi@tollwerk.de instead of using the issue tracker.

## Credits

- [Joschi Kuphal][author-url]
- [All Contributors](../../contributors)

## License

Copyright © 2017 [Joschi Kuphal][author-url] / joschi@tollwerk.de. Licensed under the terms of the [MIT license](LICENSE).


[travis-image]: https://secure.travis-ci.org/jkphl/rdfa-lite-microdata.svg
[travis-url]: https://travis-ci.org/jkphl/rdfa-lite-microdata
[coveralls-image]: https://coveralls.io/repos/github/jkphl/rdfa-lite-microdata/badge.svg?branch=master
[coveralls-url]: https://coveralls.io/github/jkphl/rdfa-lite-microdata?branch=master
[scrutinizer-image]: https://scrutinizer-ci.com/g/jkphl/rdfa-lite-microdata/badges/quality-score.png?b=master
[scrutinizer-url]: https://scrutinizer-ci.com/g/jkphl/rdfa-lite-microdata/?branch=master
[readthedocs-image]: https://readthedocs.org/projects/jkphl-rdfa-lite-microdata/badge/?version=latest
[readthedocs-url]: http://jkphl-rdfa-lite-microdata.readthedocs.io/en/latest/?badge=latest
[clear-architecture-image]: https://img.shields.io/badge/Clear%20Architecture-%E2%9C%94-brightgreen.svg
[clear-architecture-url]: https://github.com/jkphl/clear-architecture
[author-url]: https://jkphl.is
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
