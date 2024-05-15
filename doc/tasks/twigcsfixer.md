#  Twig-CS-Fixer

Check and fix Twig coding standard using [VincentLanglet/Twig-CS-Fixer](https://github.com/VincentLanglet/Twig-CS-Fixer).

***Composer***

```
composer require --dev "vincentlanglet/twig-cs-fixer:>=2"
```

***Config***

The task lives under the `twigcsfixer` namespace and has following configurable parameters:

```yaml
# grumphp.yml
grumphp:
    tasks:
        twigcsfixer:
            paths: ['.']
            level: 'NOTICE'
            config: ~
            report: 'text'
            fix: false
            no-cache: false
            debug: false
            quiet: false
            version: false
            ansi: false
            no-ansi: false
            no-interaction: false
            verbose: false
            triggered_by: ['twig']
```

**paths**

*Default: null*

By default [`.`] (current folder) will be used.
On precommit only changed files that live in the paths will be passed as arguments.


**level**

*Default: 'NOTICE'*

The level of the messages to display (possibles values are : 'NOTICE', 'WARNING', 'ERROR').

**config**

*Default: null*

Path to a `.twig-cs-fixer.php` config file. If not set, the default config will be used.

You can check config file [here](https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/configuration.md).

**report**

*Default: 'text'*

The `--report` option allows to choose the output format for the linter report.

Supported formats are:
- `text` selected by default.
- `checkstyle` following the common checkstyle XML schema.
- `github` if you want annotations on GitHub actions.
- `junit` following JUnit schema XML from Jenkins.
- `null` if you don't want any reporting.


**fix**

*Default: false*

Fix the violations.

**no-cache**

*Default: false*

Do not use cache.

**debug**

*Default: false*

Display debugging information.

**quiet**

*Default: false*

Do not output any message.

**version**

*Default: false*

Display this application version.

**ansi**

*Default: false*

Force ANSI output.

**no-ansi**

*Default: false*

Disable ANSI output.

**no-interaction**

*Default: false*

Do not ask any interactive question.

**verbose**

*Default: false*

Increase the verbosity of messages.

**triggered_by**

*Default: [twig]*

This option will specify which file extensions will trigger this task.
