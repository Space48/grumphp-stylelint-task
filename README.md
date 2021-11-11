# grumphp-stylelint-task

Adds a [stylelint](https://stylelint.io/) linting task to [GrumPHP](https://github.com/phpro/grumphp).

## Installation

Install through composer:

```shell
composer require --dev space48/grumphp-stylelint-task
```

## Configuration

Example configuration:

```yaml
# grumphp.yml
grumphp:
  tasks:
    stylelint:
      bin: "node_modules/.bin/stylelint"
      triggered_by: ["css", "scss"]
      allowed_paths: 
        - /^resources\/css/
      ignore_paths: 
        - /^ignored-folder\/css/
      max_warnings: 3
  extensions:
    - Space48\GrumPHPStylelintTask\Extension
```

Available options:

**bin**

*Default: null*

By default, the task will use `stylelint` from your `$PATH`. Use this option to override that. You can specify a path to the stylelint executable as a string, or a command to execute stylelint as an array, for example, to run stylelint through npx: `bin: ["npx", "stylelint"]`

**triggered_by**

*Default: ["css", "less", "scss", "sass", "pcss"]*

Define the list of file extensions that will trigger the stylelint task.

**allowed_paths**

*Default: []*

This option allows you to specify a list of regex patterns to filter the files that will be linted by the task.

**config**

*Default: null*

Specify an alternative configuration file for stylelint. If not specified, will let stylelint decide which configuration file will be used ([stylelint.io](https://stylelint.io/user-guide/usage/options#configfile)).

**disable_default_ignores**

*Default: false*

Prevent stylelint from automatically ignoring files in certain directories, such as `node_modules` ([stylelint.io](https://stylelint.io/user-guide/usage/options#disabledefaultignores)).

**format**

*Default: null*

Specify the output format. Will use stylelint's default output format if not specified. You can find the list of valid options on [stylelint.io](https://stylelint.io/user-guide/usage/options#formatter).

**max_warnings**

*Default: null*

Specify the maximum number of warnings allowed before the linter will exit with an error ([stylelint.io](https://stylelint.io/user-guide/usage/options#maxwarnings)).

**quiet**

*Default: false*

Output only errors, not warnings ([stylelint.io](https://stylelint.io/user-guide/usage/cli#--quiet--q)).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

This project is licensed unded the [MIT License](LICENSE.md).
