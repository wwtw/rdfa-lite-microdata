# jkphl/rdfa-lite

[![Build Status][travis-image]][travis-url] [![Coverage Status][coveralls-image]][coveralls-url] [![Scrutinizer Code Quality][scrutinizer-image]][scrutinizer-url]  [![Documentation Status][readthedocs-image]][readthedocs-url]

> Simple and lightweight RDFa Lite 1.1 parser for web documents (HTML, SVG, XML)

## Documentation

Please find the [project documentation](doc/index.md) in the `doc` directory. We recommend [reading it](http://jkphl-rdfa-lite.readthedocs.io/) via *Read the Docs*.

## Installation

This library requires PHP >=5.5 or later. I recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

## Dependencies

![Composer dependency graph](https://rawgit.com/jkphl/rdfa-lite/master/doc/dependencies.svg)

## Quality

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If you notice compliance oversights, please send a patch via pull request.

## Contributing

Found a bug or have a feature request? [Please have a look at the known issues](https://github.com/jkphl/rdfa-lite/issues) first and open a new issue if necessary. Please see [contributing](CONTRIBUTING.md) and [conduct](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email joschi@tollwerk.de instead of using the issue tracker.

## Credits

- [Joschi Kuphal][author-url]
- [All Contributors](../../contributors)

## License

Copyright © 2017 [Joschi Kuphal][author-url] / joschi@tollwerk.de. Licensed under the terms of the [MIT license](LICENSE.md).


[travis-image]: https://secure.travis-ci.org/jkphl/rdfa-lite.svg
[travis-url]: https://travis-ci.org/jkphl/rdfa-lite
[coveralls-image]: https://coveralls.io/repos/jkphl/rdfa-lite/badge.svg?branch=master&service=github
[coveralls-url]: https://coveralls.io/github/jkphl/rdfa-lite?branch=master
[scrutinizer-image]: https://scrutinizer-ci.com/g/jkphl/rdfa-lite/badges/quality-score.png?b=master
[scrutinizer-url]: https://scrutinizer-ci.com/g/jkphl/rdfa-lite/?branch=master
[readthedocs-image]: https://readthedocs.org/projects/jkphl-rdfa-lite/badge/?version=latest
[readthedocs-url]: http://jkphl-rdfa-lite.readthedocs.io/en/latest/?badge=latest
[author-url]: https://jkphl.is
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
