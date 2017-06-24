# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased
### Added
- Added `aggregate()`, `count()`, `sum()`, `average()`, `min()`, and `max()` methods to select queries.
- Added a connection manager for managing connections to one or more databases.
- Added `whereInfix()` and `orWhereInfix()` for infix style where conditions.

## 1.3 - 2016-04-30
### Added
- Support for IN and NOT IN statements as WHERE conditions when passing in an array value.
- Select queries can now be unioned together (using SQL UNION).
- Support for BETWEEN and NOT BETWEEN as WHERE conditions.
- Added NOT where conditions.
- Added OR where conditions.
- Added EXISTS where conditions.
- Subqueries can now be used as where conditions.

### Changed
- SetStatement `getValues()` now returns parameterized values and `getSetValues()` returns the key-value map of values being set.
- ValuesStatement `getValues()` now returns parameterized values and `getInsertValues()` returns the key-value map of values being inserted.
- FromStatement `hasFrom()` has been removed and constructor signature now accepts a type.

### Fixed
 - Where statement writes `null` values in conditions as `IS NULL` and `IS NOT NULL`
 - Deep cloning now works
 - Rebuilding queries no longer adds duplicate parameterized values.

## 1.2.1 - 2016-01-05
### Added
- Database service for Infuse Framework.

## 1.2 - 2015-12-24
### Added
- `LimitStatement` class.
- PHP 7 support.

### Changed
- Refactored query building logic.
- Simplified abstract query class and renamed it to `AbstractQuery` to make it easier to extend JAQB.
- Moved composer package to `jaqb/jaqb`.

### Fixed
- Validate the direction on order by statements.

## 1.1.1 - 2015-11-07
### Added
- Addition and subtraction operators added to list of unescaped identifiers.

### Fixed
- Session handler now correctly returns boolean values.

## 1.1 - 2015-03-05
### Added
- Added PHP session handler for database powered sessions.
- Support for joins on select queries.

## 1.0 - 2015-02-02
### Added
- Initial release!