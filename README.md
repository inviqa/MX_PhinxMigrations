# MX Phinx Migrations

## About

The **MX_PhinxMigrations** module integrates [Phinx](https://phinx.org) database migrations into Magento 2 as a 
replacement for the built-in `setup:upgrade` method of triggering schema and data changes, therefore enabling zero-downtime
deployments.

## Why?

The idea of this module is to avoid having to change module versions (in `module.xml` files) in order to trigger database
changes in Magento. When Magento bootstraps itself it verifies that all module versions in the `setup_module` database table
match the versions on disk. If a module version in the database does not match the version in our code then Magento will error
and show something like:

> 1 exception(s):
  Exception #0 (Magento\Framework\Exception\LocalizedException): Please upgrade your database: Run "bin/magento setup:upgrade" from the Magento root directory.
  The following modules are outdated:
  Acme_Foo schema: current version - 0.2.0, required version - 0.3.0
  Acme_Foo data: current version - 0.2.0, required version - 0.3.0

If we consider the following high level deployment process, this makes zero-downtime deployments impossible due to the fact 
that at some point, even if just for a very short time, the code and database versions will be out of sync between steps
2 and 3:

1. Build Magento container
2. Before deploying the new container(s), run `setup:upgrade` process on the target environment database
3. Deploy the new container(s)

## Requirements

The module is currently supported on Magento >= 2.1.

## Installing

Add the module to the require section of the composer file:

```
$ composer require mx/module-phinx-migrations
```

## Enabling the module

```
$ bin/magento setup:upgrade
$ bin/magento c:c
```

We can verify that the module is enabled by running the status command:

```
$ bin/magento module:status MX_PhinxMigrations
```

Create a `phinx.php` configuration file in the root of your Magento project. You can find an example [here](etc/phinx.php)

## Using

TODO

## Limitations

1. Upgrading Magento itself will still result in version changes in the Magento `module.xml` files, therefore zero-downtime 
deployments are not possible in this situation.
