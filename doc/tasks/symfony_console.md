# Symfony Console

Run a symfony console command.

## Composer

Requires the Symfony Console component: [Console Component](https://symfony.com/components/Console)

```bash
composer require symfony/console
```

## Config

The task lives under the `symfony_console` namespace and has following configurable parameters:

```yaml
# grumphp.yml
grumphp:
    tasks:
        symfony_console:
            command: [ "lint:container", "-vvv" ]
```

**command**

Specify the symfony command with defined options and arguments.  
Verify the installed console component version for available commands `./bin/console list`

## Note: Multiple Console command tasks

[Run the same task twice with different configuration](../tasks.md#run-the-same-task-twice-with-different-configuration)

Specific running multiple symfony console commands:

```yaml
# grumphp.yml
grumphp:
  lint-container:
      command: [ "lint:container", "-vvv"]
      metadata:
        task: symfony_console
  lint-yaml:
      command: [ "lint:yaml", "path/to/yaml"]
      metadata:
        task: symfony_console
```
