# Upgrade from 6.x to 7.x

Version 7.0.0 no longer depends on `helhum/typo3-console`. `ExecuteSqlTrait`
executes the SQL file through Doctrine DBAL instead of the `mysql` command-line
client. TYPO3 15 (development versions) is allowed in addition to 13.4 and 14.3.

## Why

In 6.x, `ExecuteSqlTrait` created the console's `MysqlCommand`, which started the
`mysql` client as a separate process and piped the SQL file into it. This needed:

* `helhum/typo3-console`, which has no version for TYPO3 15
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
