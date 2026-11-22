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
| Public constant `ExampleCommand::CONNECTION_TYPE_MYSQL` | Driver check for the connection | Removed |
| Constant `ExampleCommand::ERROR_MISSING_CONNECTION` | `No suitable MySQL connection found.` | `No database connection with this name found.` |
| Exit code `1_641_390_076` | No suitable connection | Connection name is not configured in TYPO3 |
| Exit code `1_641_390_086` | SQL execution failed | Unchanged; the database error message is printed as well |
| Exit code `1_795_339_730` | - | New: the SQL file is missing or not readable |
| Exit code `0` (success) | Returned | Unchanged |
| Constructor `__construct(?string $name = null)` | Read the SQL file | Unchanged signature; does not read the file any more |
| Reading the SQL file | In the constructor, on every instantiation; a missing file caused a PHP warning or error | In `execute()`, after the connection check |
| Protected property `$sqlToExecute` | Filled in the constructor | Filled in `execute()`; a value assigned earlier is overwritten |
| New protected methods | - | `getSqlFilePath()` (returns `static::SQL_FILE_PATH`), `readSqlFile()` |
| Protected method `splitSqlStatements()` | - | `splitSqlStatements(string $sql, bool $mysqlSyntax = true)` |
| Command names `t3extension-tools:example`, `t3extension-tools:delete-logs` | Available | Unchanged |

### Format of the SQL file

The trait splits the file into statements itself:

* A statement ends with `;` outside of quotes and comments. Statements may span
  several lines, and several statements may be on one line.
* `'...'`, `"..."` and `` `...` `` are copied unchanged, so a `;` or comment
  marker inside them does not end the statement. A quote inside is escaped by
  doubling it; on MySQL and MariaDB connections also by a backslash (not inside
  `` `...` ``).
* Comments up to the end of the line, also after a statement:
  * MySQL and MariaDB: `#`, and `--` followed by whitespace, a control
    character or the end of the input. `--` at the start of a line (after
    optional whitespace) is a comment as well, e.g. a line of dashes.
    Otherwise `1--1` stays an expression, as in MySQL. The line-start rule
    applies anywhere in the file, also inside a statement: in
    `SELECT 1\n--1;` the second line is removed as a comment, while the
    `mysql` client would send `SELECT 1 --1`. This is a documented limit.
  * Other platforms: `--` always. `#` is an ordinary character (e.g. the
    PostgreSQL operators `#` and `#>>`), so do not use `#` comments there.
* `/* ... */` comments are removed anywhere. MySQL executable comments and
  optimizer hints (`/*! ... */`, `/*+ ... */`) are kept and sent to the
  database.
* Client commands of the `mysql` CLI (`DELIMITER`, `SOURCE`, `\G`, ...) are not
  supported, so stored procedures or triggers with custom delimiters cannot be
  created this way. Also not supported: PostgreSQL dollar-quoted strings
  (`$$ ... $$`) and escape strings (`E'...'`), and the MySQL SQL mode
  `NO_BACKSLASH_ESCAPES`. Statements containing them may be split or merged
  wrongly.
* A missing or unreadable file ends the command with exit code `1_795_339_730`
  and an error message.
* Execution stops at the first failing statement. Statements executed before it
  are not rolled back (as before with the `mysql` client).

The exit codes `1_641_390_076`, `1_641_390_086` and `1_795_339_730` are the
values returned by the command, e.g. by `Command::run()`. A process exit status
is limited to 0-255, so a shell sees them modulo 256: `252`, `6` and `210`.

### Check your own code

- Code that reads or assigns `$connectionConfigurationFactory`, or uses
  `CONSOLE_SCOPE_DATABASE_IMPORT`, has to be removed.
- Code that assigns `$sqlToExecute` (e.g. in a constructor) has no effect any
  more; override `getSqlFilePath()` or `readSqlFile()` instead.
- SQL files for connections other than MySQL/MariaDB need `--` instead of `#`
  comments.
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
  be non-empty strings; `plugin.type`, if set, must be a string; `plugin`,
  `registration`, `controllerActions` and `nonCacheableControllerActions` must
  be maps. Invalid files are skipped (and
  logged if a logger is injected) instead of aborting the TCA build.

### Command `t3extension-tools:delete-logs`

* Missing or invalid arguments are rejected with exit code `2` (`Command::INVALID`):
  an empty pattern, a pattern that is not a valid regular expression, an age that
  is not a number of days.
* A directory that does not exist returns `1` (`Command::FAILURE`).
* A file that cannot be deleted is reported; the command continues with the
  other files and returns `1` at the end.
* A pattern expects a date prefix if it starts with `^[0-9]{4}-[0-9]{2}-[0-9]{2}`
  right after the delimiter. Any delimiter is accepted and no `_` after the date
  is required (before: only `/^[0-9]{4}-[0-9]{2}-[0-9]{2}_`).
* If the pattern expects a date prefix but a file name does not start with a
  valid date, the modification time is used instead of throwing an exception.
* Without a date prefix, the age of a file is taken from its modification time
  instead of the inode change time.
* Only files (and links to files) are considered; directories and other entries
  are skipped. Files are deleted by their path (`getPathname()`) instead of
  `getRealPath()`, so a symbolic link is deleted itself, not its target.
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
