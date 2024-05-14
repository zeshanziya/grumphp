#  Twig CS Fixer

Check and fix Twig coding standard using [VincentLanglet/Twig-CS-Fixer](https://github.com/VincentLanglet/Twig-CS-Fixer).
You can check config file [here](https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/configuration.md).

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
            path: '.'
            triggered_by: ['twig']
```

**path**

*Default: null*

By default `.` (current folder) will be used.
On precommit the path will not be used, changed files will be passed as arguments instead.
You can specify an alternate location by changing this option. If the path doesn't exist or is not accessible an exception will be thrown.

**triggered_by**

*Default: [twig]*

This option will specify which file extensions will trigger this task.
