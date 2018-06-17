# Starter

A starter PHP project with many services and features pre-configured. Simply use `composer create-project`, and a new GitHub repository will be created and configured to be tested on Travis CI.

[![Travis CI](https://travis-ci.org/g1a/starter.svg?branch=master)](https://travis-ci.org/example-org/example-project)
[![Windows CI](https://ci.appveyor.com/api/projects/status/ey7eubrwjss0gca6?svg=true)](https://ci.appveyor.com/project/greg-1-anderson/starter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/g-1-a/starter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/g-1-a/starter/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/g-1-a/starter/badge.svg?branch=master)](https://coveralls.io/github/g-1-a/starter?branch=master) 
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

## Usage

To get started, export your [GitHub personal access token](https://help.github.com/articles/creating-an-access-token-for-command-line-use/) and then create a new project.
```
$ export GITHUB_TOKEN='...'
$ export APPVEYOR_TOKEN='...'      # Optional
$ export SCRUTINIZER_TOKEN='...'   # Optional
$ composer create-project g1a/starter my-new-project
```
The new project will be owned by the currently-authenticated user. If you would like to create a new project in an organization instead, then set the `GITHUB_ORG` environment variable.
```
$ export GITHUB_ORG='my-github-username-or-org'
```

Once the new project is created, automated scripts will customize it and set up a number of serivces. See the section [Description of Features](#description-of-features) below for more information. Once the scripts have finished running, you may customize your README file and start coding!

### Dependency Installation

Before you begin, make sure that the [Travis CLI tool](https://github.com/travis-ci/travis.rb#installation) has been installed on your system.

### Access Token Generation

Generating and exporting a personal access token for the services below is recommended, as doing this will allow the post-create-project scripts to configure and enable these services automatically.

| Export                         | Token Generation URL       
| ------------------------------ | -------------------------- 
| exoirt GITHUB_TOKEN='...'      | [Generate GitHub token](https://github.com/settings/tokens)
| export APPVEYOR_TOKEN='...'    | [Generate Appveyor token](https://ci.appveyor.com/api-token)   
| export SCRUTINIZER_TOKEN='...' | [Generate Scrutinizer token](https://scrutinizer-ci.com/profile/applications)

### Manual Service Configuration

If the personal access token for these services is not set up, then the service may be manually configured later. In addition, this project is also configured for use with Packagist, Dependencies.io and Coveralls; these services only need to be manually authorized through their web interface to enable them for projects created with this template.

Follow the links in the table below to configure the services you would like to use.

| Feature                   | Manual Setup URL
| ------------------------- | ----------------
| Collaborative repository  | [Create GitHub repository](https://github.com/new)
| Linux permutation testing | [Enable Travis CI](https://travis-ci.org/profile)
| Windows testing           | [Enable Appveyor CI](https://ci.appveyor.com/projects/new)
| Static analysis           | [Enable Scrutinizer CI](https://scrutinizer-ci.com/g/new)
| Code coverage             | [Enable Coveralls](https://coveralls.io/repos/new)
| Package manager           | [Register with Packagist](https://packagist.org/packages/submit)
| Dependency updates        | [Enable Dependencies.io](https://app.dependencies.io/add-project)

## Description of Features

This project comes with a number of configuration files already set up for a number of services. A Composer post-install hook makes further modifications, and, where possible, makes API calls to complete the setup for some services.

The following things are provided:

- Project information
  - [composer.json](/composer.json): Automatically customized with project-specific information.
    - Project name (taken from `create-project` project name argument)
    - Author name and email address (from git configuration)
  - [README.md](/customize/templates/README.md): Example template with badges to get you started.
  - [CHANGELOG.md](/CHANGELOG.md): Blank slate provided in the hopes that releases may be recorded here.
  - [LICENSE](/LICENSE): Defaults to MIT. Will automatically be updated with dependency licenses and copyright year every time 'composer update' is run.
- Project metadata
  - [.editorconfig](/.editorconfig): Set up for PSR-2 conventions for compliant editors.
  - [.gitattributes](/.gitattributes): Ensures that tests, build results and so on are not exported to Packagist.
  - [.gitignore](/.gitignore): Ensures that vendor directory and so on is not committed to the repository.
- Repository
  - **GitHub:** Automatically creates a new repository on GitHub and pushes up your new project. Starter GitHub contribution templates are provided:
    - [CONTRIBUTING.md](/CONTRIBUTING.md)
    - [issue_template.md](/.github/issue_template.md)
    - [pull_request_template](/.github/pull_request_template.md)
  - Tag and push a release, as identified in the VERSION file.
    - $ `composer release`
- Commandline Tool
  - Comes with an [example command line tool](/src/cli/ExampleCommands.php) based on the [Robo PHP task runner](https://robo.li/getting-started/), to make it easy to add commands for ad-hoc execution of your project classes.
  - Commandline dependencies are declared in the `require-dev` section of the composer.json file, other parties that wish to use your project as a library will not unnecessarily inherit them.
  - Build a phar version of your commandline tool locally:
    - $ `composer phar:install-tools`
    - $ `composer phar:build`
- Testing
  - **Travis:** Automatically enables testing for the new project in Travis.
    - [phpunit.xml.dist](/phpunit.xml.dist): Test configuration with code coverage (html coverage report configuration is present, but commented out).
    - [Example.php](/src/Example.php): A simple class that multiplies. Replace with your own code.
    - [ExampleTest.php](/tests/ExampleTest.php): A simple data-driven test that pulls fixture data from a data provider. Replace with your own tests.
    - [ExampleCommandsTest.php](/tests/ExampleCommandsTest.php): Another simple data-driven test that pulls commandline arguments from fixture data and runs it through the commandline tool. Replace with your own tests.
  - **Coveralls:** Project must be manually configured on [coveralls.io](https://coveralls.io). PHPUnit and Travis are already configured to export coverage data to Coveralls automatically; all that needs to be done is to enable the Coveralls service for the new project.
  - **Appveyor:** An appveyor configuration file is provided. If an APPVEYOR_TOKEN environment variable is defined when the project is created, Appveyor testing will be automatically configured. Otherwise, it will need to be manually enabled on [appveyor](https://www.appveyor.com/) if Windows testing is desired.
  - **Scrutinizer:** If a SCRUTINIZER_TOKEN environment variable is defined when the project is created, then Scrutinizer static code analysis is automatically enabled. Otherwise, the project must be manually enabled on [scrutinizer-ci.com](https://scrutinizer-ci.com).
  - Provides handy composer scripts for testing:
    - `composer test`: Run all tests.
    - `composer unit`: Run just the phpunit tests.
    - `composer lint`: Run the php linter.
    - `composer cs`: Run the code sniffer to check for PSR-2 compliance.
    - `composer cbf`: Fix code style violations where possible.
- Composer
  - **Packagist:** Project must be manually submitted to [packagist.org](https://packagist.org) in order to be able to `require` it as a dependency of some other project.
  - **Dependencies:** A [dependencies.yml](/dependencies.yml) configuration file is provided; if the project is enabled on [dependencies.io](https://www.dependencies.io/), then pull requests will automatically be created any time newer versions of the project's dependencies become available.
  - [Composer test scenarios](https://github.com/g1a/composer-test-scenarios) are configured to allow tests to be written for PHPUnit 6, and still use PHPUnit 5 for testing on PHP 5.6. Highest/lowest testing is also configured by default. This project also contains the scripts used to keep the LICENSES file up-to-date for this project's dependencies.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- This project makes heavy use of configuration techniques and code from [Drush](https://drush.org), [Robo PHP](https://robo.li) and other [Consolidation projects](https://github.com/consolidation).
- The [KnpLabs github-api](https://github.com/KnpLabs/php-github-api) and [guzzle](http://docs.guzzlephp.org/en/stable/) made the API calls done by this project very easy.
