# Upgrade from 6.x to 7.x

Version 7.0.0 no longer depends on `helhum/typo3-console`. `ExecuteSqlTrait`
executes the SQL file through Doctrine DBAL instead of the `mysql` command-line
client.

## Why

In 6.x, `ExecuteSqlTrait` created the console's `MysqlCommand`, which started the
`mysql` client as a separate process and piped the SQL file into it. This needed:

* `helhum/typo3-console`, which has no version for TYPO3 15 so far
* a `mysql` or `mariadb` client binary on the machine that runs the command,
  which is often missing in production containers
* a MySQL connection; other drivers were refused

## What to change in your project

1. If your project requires `helhum/typo3-console` only because of this package,
   you can remove it. If you use the console for other commands, keep it.
2. If a class uses `ExecuteSqlTrait`, check the points below.

## Changes in `ExecuteSqlTrait`

| Topic | 6.x | 7.x |
|-------|-----|-----|
| Execution | `mysql` client process via console `MysqlCommand` | Doctrine DBAL, one `executeStatement()` per statement |
| Database client binary | Required | Not needed |
| Connection drivers | Only drivers containing `mysql` | Every TYPO3 connection; the SQL must suit its platform |
| Protected property `$connectionConfigurationFactory` | Console `ConnectionConfigurationFactory` | Removed |
| Trait constant `CONSOLE_SCOPE_DATABASE_IMPORT` | Selected console `commandOptions` | Removed |
| Console `commandOptions` (`EXTCONF/typo3_console/commandOptions`) | Applied to the `mysql` call | Not applied |
| Constant `DEFAULT_MYSQL_ARGUMENTS` | Passed to the `mysql` call | Not read any more; removed from `ExampleCommand` |
| Exit code `1_641_390_076` | No suitable connection | Connection name is not configured in TYPO3 |
| Exit code `1_641_390_086` | SQL execution failed | Unchanged; the database error message is printed as well |
| Exit code `0` (success) | Returned | Unchanged |
| Constructor `__construct(?string $name = null)` | Available | Unchanged |
| Command names `t3extension-tools:example`, `t3extension-tools:delete-logs` | Available | Unchanged |

### Format of the SQL file

The trait splits the file into statements itself:

* A statement ends with `;` at the end of a line. Statements may span several
  lines. Several statements on one line are not split; they are sent as one
  query, which the default `mysqli` driver rejects.
* Lines starting with `#` or `--` and `/* ... */` blocks on their own lines are
  skipped.
* Client commands of the `mysql` CLI (`DELIMITER`, `SOURCE`, `\G`, ...) are not
  supported, so stored procedures or triggers with custom delimiters cannot be
  created this way.
* Execution stops at the first failing statement. Statements executed before it
  are not rolled back (as before with the `mysql` client).

### Check your own code

- Code that reads or assigns `$connectionConfigurationFactory`, or uses
  `CONSOLE_SCOPE_DATABASE_IMPORT`, has to be removed.
- SQL files with several statements on one line need a line break after each `;`.
- SQL files using client commands need another way to run, e.g. your own command
  that calls the `mysql` client, or the console's `database:import`.
- Error messages of `ERROR_MISSING_CONNECTION` in your command may still mention
  MySQL; any configured connection is accepted now.

## Other changes in 7.0

These changes are not breaking in most projects, but they change behaviour.
Check them if you use the affected classes.

### Plugin registration

* PHP plugin classes and YAML plugin files are registered through one service,
  `PluginRegistrar`. `PluginRegistrationService` and
  `YamlPluginRegistrationService` accept it as an optional constructor
  argument; existing constructor calls keep working.
* The FlexForm is registered for the signature returned by `registerPlugin()`.
  For YAML plugins the FlexForm was registered under a different key before
  and did not reach the plugin.
* TYPO3 14: the FlexForm is passed to `registerPlugin()`. The `ds` array layout
  and `addPiFlexFormValue()` are no longer used. TYPO3 13: tab plus
  `addPiFlexFormValue('*', ..., $signature)`.
* An empty icon or group falls back to `content-plugin` / `plugins`.
  `registration.description` from YAML files is passed on.
* Plugin services from the tagged iterator are used as they come from the
  container. Before, they were instantiated again with `new`, which failed for
  classes with constructor arguments.
* YAML plugin files are validated: `plugin.extensionName` and `plugin.name` must
  be non-empty strings; `plugin`, `registration`, `controllerActions` and
  `nonCacheableControllerActions` must be maps. Invalid files are skipped (and
  logged if a logger is injected) instead of aborting the TCA build.

### Command `t3extension-tools:delete-logs`

* Missing or invalid arguments are rejected with exit code `2` (`Command::INVALID`):
  an empty pattern, a pattern that is not a valid regular expression, an age that
  is not a number of days.
* A directory that does not exist returns `1` (`Command::FAILURE`).
* A file that cannot be deleted is reported; the command continues with the
  other files and returns `1` at the end.
* Without a date prefix in the pattern, the age of a file is taken from its
  modification time instead of the inode change time.
* `DeleteLogs::DEFAULT_NAME` is `t3extension-tools:delete-logs` (was
  `t3extension-tools:delete-logs:delete-logs`). The registered command name is
  unchanged.

### Command arguments

* The argument classes use `InputArgument::OPTIONAL` as `MODE` instead of
  `InputOption::VALUE_REQUIRED`. Both have the value `2`, so the behaviour does
  not change.
* `ExampleArgument` implements `InputArgumentInterface`.

### Deprecations

* `Status` and `StatusAwareInterface` are deprecated and will be removed in 8.0.
  `Status::success()`, `failure()` and `invalid()` trigger `E_USER_DEPRECATED`.
  Use `Command::SUCCESS`, `Command::FAILURE` and `Command::INVALID`.
* `ExtensionConfiguration::registerIcons()` and `registerIconsWithProvider()`
  are still deprecated (since 6.2) and still available in 7.0. Register icons in
  `Configuration/Icons.php`.
