# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.1] - 2020-11-29

- Fix and improve the wording of FormatNotFoundException message
- Improve composer keywords

## [1.2.0] - 2020-11-29

- Drop `ERASE_TO_LINE_END_2` and `ERASE_TO_LINE_END_1` constants from `AnsiEscapeSequences` because they where broken.
- Add `ERASE_TO_LINE_END` to `AnsiEscapeSequences`
- Add `ErrorFormatter\RawTextErrorFormatter` that reflects a Doctum error output format
- Add `OutputFormat` to handle the input/output user choice

## [1.1.0] - 2020-11-27

- Add `isDecorated` method on `SymfonyOutput` class and `Output` interface.
- Fix missing `strict_types` on `Error` class
- Add `AnsiEscapeSequences` to help with some often used sequences

## [1.0.0] - 2020-11-26

- Initial version with support for `ErrorFormatter` classes from phpstan and more classes
- Fixed imported tests
- Added GitHub actions
- Added phpstan
- Removed some useless imported code
- Made the imported source code compatible with PHP 7.1
