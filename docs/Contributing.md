# Contributing

The full description is in
[CONTRIBUTING.md](https://github.com/dwenzel/t3extension_tools/blob/main/CONTRIBUTING.md).
This chapter summarises it.

## Branching model

* `main` is the default branch and holds the newest major version. Changes go
  through pull requests; direct pushes, force-pushes and deleting the branch
  are blocked. Maintainers merge pull requests after the checks have passed.
* Older major versions get `support/{major}.x` branches (e.g. `support/6.x`) if
  a fix is needed for them. They follow the same convention as `main`.
* All work happens in feature branches named after the issue:
  `feature/{issue-number}-{title-in-kebab-case}`.
* There are no `develop` or `master` branches. The former `develop` branch is
  kept as the tag `archive/develop`.

## Workflow

1. Create or pick a GitHub issue.
2. Create a feature branch from `main` (or from the support branch).
3. Make the change, including tests.
4. Run `composer lint && composer sca:php && composer test`.
5. Open a pull request and reference the issue (`Closes #12`).
6. By convention, a maintainer merges the pull request after the checks have
   passed and it has been reviewed.

Commit messages start with one of the tags `[FEATURE]` `[TASK]` `[BUGFIX]`
`[DOC]` `[CI]` `[SECURITY]` `[RELEASE]`. Breaking changes are prefixed with
`[!!!]`. The commit subjects become the entries of the `ChangeLog`, which is
written in the release pull request.

## Releases

1. A release pull request (`[RELEASE] Release of t3extension_tools x.y.z`) adds
   the version block to `ChangeLog` and sets the version in `composer.json`
   (`extra.typo3/cms.version`) and `ext_emconf.php`.
2. The release pull request is merged with a merge commit, and the merge
   commit is tagged with `x.y.z` (no prefix).
3. The tag starts `release.yml` (GitHub release, Packagist) and
   `publish-ter.yml` (TER upload, needs the secret `TYPO3_API_TOKEN`). Both
   check that the tag matches the versions in `composer.json` and
   `ext_emconf.php`.

## Tests and quality tools

| Command                             | Purpose                                     |
|-------------------------------------|---------------------------------------------|
| `composer test:unit`                | Unit tests                                  |
| `composer test:functional`          | Functional tests, on SQLite by default      |
| `composer test:coverage:unit`       | Unit tests with coverage (PCOV or Xdebug)   |
| `composer test:coverage:functional` | Functional tests with coverage              |
| `composer lint` / `composer fix`    | Check / fix coding standards                |
| `composer sca:php`                  | PHPStan                                     |

See [Development](Development.md#running-the-tests) for running the tests in
DDEV.
