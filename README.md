
# [nette commits](https://commits.nette.org)

Aggregation of all [nette](https://github.com/nette)/* repositories.

## Setup

1. fork and clone the project
2. copy [app/config/config.local.neon.template](app/config/config.local.neon.template) to `app/config/config.local.neon` and configure it properly:
    - `database` for database connection
    - `githubAPI` for GitHub API token - generate one [here](https://github.com/settings/tokens/new) with public access
3. run `composer install`
4. run `bower install`
5. run `bin/console orm:schema-tool:create` to create database schema
6. run `bin/console dbal:import fixtures.sql` to import default projects and repositories

## Synchronization

During the synchronization process, all commits from all repositories are being synchronized.

You can run the synchronization either via console command

```
./bin/console synchronize
```

or via HTTP GET request

```
GET http://yourhost.com/synchronize.php
```

You can set this as a CRON job to run every 5 minutes.

To prevent running the synchronization multiple time at once, [Symfony Lock](https://symfony.com/doc/current/components/lock.html) component is used.
