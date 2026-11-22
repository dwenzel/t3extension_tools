# Contributing

Thank you for contributing to t3extension_tools. This document describes how the
repository is organised and how changes get from an idea into a release.

## Branching model

The repository follows a trunk-based model, like
[CPS-IT/import-export-core](https://github.com/CPS-IT/import-export-core) and
[dwenzel/t3import_export](https://github.com/dwenzel/t3import_export).

### main

* `main` is the default branch. It holds the newest major version and supports
  the newest TYPO3 versions.
* Only maintainers can push or merge to `main`. By convention, changes go
  through pull requests and are merged after the automated checks have passed.
* There are no `develop`, `master` or `release/*` branches. The former `develop`
  branch is kept as the tag `archive/develop`.

### Support branches for older major versions

Older major versions of the extension are maintained in support branches, if
needed:

```
support/{major}.x        e.g. support/6.x
```

* A support branch is created from the last tag of that major version, as soon
  as a fix for that version is needed.
* Fixes for an older major version are merged into its support branch through
  pull requests.
* Releases from a support branch continue the version line of that branch
  (e.g. 6.2.0 -> 6.2.1 on `support/6.x`).
* Support branches follow the same convention as `main`.

### Feature branches

All work happens in feature branches. Every change starts with a GitHub issue;
name the branch after it:

```
feature/{issue-number}-{title-in-kebab-case}
feature/{title-in-kebab-case}        # only if there is no issue
```

Examples: `feature/46-contributing-trunk-model`, `feature/fix-delete-logs-age`

* Branch from `main`, or from `support/{major}.x` if the change only concerns an
  older major version.
* Branch names consist of lowercase alphanumeric characters and `-`, apart from
  the `/` after the prefix.
* Work on a TYPO3 version that is not yet stable can live in a long-running
  feature branch (e.g. `feature/v15-compatible` for TYPO3 15) until it is merged
  into `main`.

Branches that are no longer maintained but should not be lost are kept as tags
under `archive/` (for example `archive/develop`).

### Which branch does my pull request target?

| Change                                         | Target                                                              |
|------------------------------------------------|---------------------------------------------------------------------|
| Feature or fix for the current major version   | `main`                                                              |
| Fix that also affects an older major version   | `main` first, then a pull request into `support/{major}.x` (cherry-pick) |
| Fix that only affects an older major version   | `support/{major}.x`                                                 |

## Workflow

1. Create or pick a GitHub issue.
2. Create a feature branch from the target branch.
3. Make the change, including tests. Write commit messages that can be read as
   changelog entries (see [Commit messages](#commit-messages) and
   [ChangeLog](#changelog)). For a breaking change, prefix the commit message
   with `[!!!]` and describe the migration in the upgrade notes
   (`docs/UpgradeTo{major}.md`).
4. Run the tests and linters locally (see
   [Running tests locally](#running-tests-locally)).
5. Open a pull request into the target branch and reference the issue
   (`Closes #12`).
6. By convention, a maintainer merges the pull request after the checks have
   passed and it has been reviewed.
7. Delete the feature branch after merging.

## Commit messages

Commit messages are short and start with one of these tags:

`[FEATURE]` `[TASK]` `[BUGFIX]` `[DOC]` `[CI]` `[SECURITY]` `[RELEASE]` `[WIP]` `[DRAFT]`

`[WIP]` and `[DRAFT]` commits are not merged into `main` or a support branch.

Example: `[BUGFIX] DeleteLogs: validate input, age by modification time`

Breaking changes are prefixed with `[!!!]`, e.g.
`[!!!][TASK] Drop typo3-console, execute SQL through Doctrine DBAL`.

## ChangeLog

The file `ChangeLog` lists the commits of each release. It is written in the
release pull request (see below), so feature pull requests do not edit it. The
subject lines of the commits become the entries, which is why they should
describe the change on their own.

Each release is one block, newest first, separated by an empty line:

```
2026-11-22         [RELEASE] Release of t3extension_tools 7.0.0 (Dirk Wenzel)
2026-11-22 4a35b01 [CI] Functional tests: same action versions as the other jobs (Dirk Wenzel)
2026-11-14 474c09c [!!!][TASK] Drop typo3-console, execute SQL through Doctrine DBAL (Dirk Wenzel)
```

The workflows `release.yml` and `publish-ter.yml` read this block to create
the release notes and the TER upload comment. They look for the line containing
`Release of t3extension_tools {version} ` and take the following lines up to
the next empty line. Keep this format, otherwise the release notes fall back to
a generic text.

## Releases

* Only tags are released. Tags follow [Semantic Versioning](https://semver.org/)
  without prefix (e.g. `7.0.0`).
* The version that `main` is working towards is the branch alias in
  `composer.json` (e.g. `"dev-main": "7.1.x-dev"`), so that a project can
  require the development state as `7.1.x-dev`. Support branches have no
  branch alias.

### Release steps

1. Open a pull request into `main` or the support branch, from a branch like
   `feature/release-7.1.0`, with a commit
   `[RELEASE] Release of t3extension_tools 7.1.0` that
   * adds the block of the new version at the top of `ChangeLog`; the
     commits since the last release can be listed with
     ```bash
     git log --no-merges --date=short --format='%ad %h %s (%an)' 7.0.0..HEAD
     ```
     The release line itself has no hash (see [ChangeLog](#changelog)),
   * sets the version in `ext_emconf.php` and in `composer.json`
     (`extra.typo3/cms.version`),
   * after a major or minor release from `main`, sets the branch alias in
     `composer.json` to the next minor version (e.g. `7.2.x-dev`),
   * updates the requirements in `README.md` if they changed.
2. Merge the release pull request with a merge commit. After the checks on the
   branch have passed, tag the merge commit:
   ```bash
   git tag 7.1.0 <merge-commit> && git push origin 7.1.0
   ```
3. Check that the version is available on
   [Packagist](https://packagist.org/packages/dwenzel/t3extension-tools) and in
   the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/t3extension_tools).

Pushing the tag starts two workflows:

* `release.yml` checks that the tag matches the version in `composer.json`
  (`extra.typo3/cms.version`) and in `ext_emconf.php`, runs the quality checks
  and the unit tests, creates the GitHub release with the `ChangeLog` block as
  notes and asks Packagist to update (secrets `PACKAGIST_USERNAME` and
  `PACKAGIST_TOKEN`, optional).
* `publish-ter.yml` checks the versions again and publishes the tag to the TER
  with [tailor](https://github.com/TYPO3/tailor). It needs the repository
  secret `TYPO3_API_TOKEN`. An existing tag can be published by starting the
  workflow manually with the tag as input.

A fix that affects `main` and a support branch is merged into `main` first and
then cherry-picked into a pull request for the support branch. Releases of the
branches are independent of each other.

## Continuous integration

All workflows below run on pushes to `main`, `feature/**` and `support/**` and
on pull requests into `main` and `support/**`.

| Workflow                  | Checks                                                                 |
|---------------------------|------------------------------------------------------------------------|
| `ci.yml`                  | Quality gate (composer validate and normalize, PHP-CS-Fixer, PHPStan, Rector, Fractor, EditorConfig, unit tests, composer audit), unit tests on PHP 8.3 and 8.4, tool and configuration checks, outdated dependencies |
| `tests.yml`               | Unit and functional tests (SQLite) for TYPO3 13.4 and 14.3 on PHP 8.3 and 8.4, coverage upload to Codecov |
| `code-quality.yml`        | PHP-CS-Fixer, PHPStan, Rector, Fractor, EditorConfig                   |
| `static-analysis.yml`     | PHPStan                                                                |
| `composer-validate.yml`   | composer validate, normalize and audit                                 |
| `typo3-compatibility.yml` | Rector checks against TYPO3 13.4 and 14.3, extension scanner           |
| `release.yml`             | On version tags: version check, GitHub release, Packagist update       |
| `publish-ter.yml`         | On version tags or manually: publish to the TER                        |

## Running tests locally

Install the dependencies with Composer (PHP 8.3 or 8.4 with the `pdo_sqlite`
extension):

```bash
composer install
```

| Command                             | Purpose                                                  |
|-------------------------------------|----------------------------------------------------------|
| `composer test:unit`                | Unit tests                                               |
| `composer test:functional`          | Functional tests, on SQLite by default                   |
| `composer test`                     | Both suites                                              |
| `composer test:coverage:unit`       | Unit tests with coverage (needs PCOV or Xdebug)          |
| `composer test:coverage:functional` | Functional tests with coverage (needs PCOV or Xdebug)    |

The functional tests use SQLite unless the environment variable
`typo3DatabaseDriver` says otherwise (see `Tests/Build/FunctionalTests.xml`),
so no database server is needed. The coverage reports are written to
`.Build/log/coverage/html/`. With Xdebug set `XDEBUG_MODE=coverage`.

A DDEV environment is included as well; see
[Running the Tests](docs/Development.md#running-the-tests).

### Quality tools

| Command               | Purpose                                                               |
|-----------------------|-----------------------------------------------------------------------|
| `composer lint`       | composer normalize, EditorConfig, PHP-CS-Fixer, Rector and Fractor (dry run) |
| `composer fix`        | Apply the fixes of the tools above                                    |
| `composer sca:php`    | PHPStan                                                               |

Before opening a pull request, run:

```bash
composer lint && composer sca:php && composer test
```
