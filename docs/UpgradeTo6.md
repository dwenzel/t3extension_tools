# Upgrade from 5.x to 6.x

Version 6.0.0 requires `helhum/typo3-console` **^9** and drops support for console 8.
TYPO3 stays at `typo3/cms-core ^13.4`.

## What to change in your project

1. Require console 9 in your project (or in the extension that uses this package):

    ```bash
    composer require helhum/typo3-console:^9 dwenzel/t3extension-tools:^6
    ```

    Console 9 requires PHP >= 8.2. A project that must stay on console 8 has to stay on `dwenzel/t3extension-tools ^5`.

2. If a class uses `ExecuteSqlTrait`, check the points below.

## Changes in `ExecuteSqlTrait`

| Topic | 5.x | 6.x |
|-------|-----|-----|
| Protected property `$connectionConfiguration` | Console 8 `ConnectionConfiguration` | Removed. Replaced by `$connectionConfigurationFactory` (console 9 `ConnectionConfigurationFactory`) |
| Exit code `1_641_390_077` (output is not a `ConsoleOutput`) | Returned | Retired. Any `OutputInterface` is accepted, so the command runs and returns `0` on success |
| `CONNECTION_TYPE_MYSQL` constant | Read by the trait | Not read any more. It may stay defined in your command |
| Exit code `1_641_390_076` (no suitable connection) | Returned | Unchanged |
| Exit code `1_641_390_086` (SQL execution failed) | Returned | Unchanged |
| Exit code `0` (success) | Returned | Unchanged |
| Constructor `__construct(?string $name = null)` | Available | Unchanged |
| Command names `t3extension-tools:example`, `t3extension-tools:delete-logs` | Available | Unchanged |

### Check your own code

- Subclasses or commands that read or assign `$connectionConfiguration` must use `$connectionConfigurationFactory` instead, or stop touching it.
- Code or scripts that evaluate exit code `1_641_390_077` can drop that branch.
- Constructor overrides keep working. The trait now creates the console 9 factory from TYPO3's `ConnectionPool` itself.
- Connections are still offered only if their driver name contains `mysql`.
- The MySQL options of the console scope `database:import` (`EXTCONF/typo3_console/commandOptions`) apply to the SQL import. The scope is defined by the trait constant `CONSOLE_SCOPE_DATABASE_IMPORT`.
