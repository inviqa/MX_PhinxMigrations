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

> 1 exception(s):\
> Exception #0 (Magento\Framework\Exception\LocalizedException): Please upgrade your database: Run "bin/magento setup:upgrade" from the Magento root directory.\
>  The following modules are outdated:\
>  Acme_Foo schema: current version - 0.2.0, required version - 0.3.0\
>  Acme_Foo data: current version - 0.2.0, required version - 0.3.0

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

## The phinx configuration file

After installing the module a `phinx.php` file should have been copied to your project root folder. There should be no
further changes required to this file but feel free to edit it as necessary for your project setup.

NOTE: Currently the file is copied using the [Magento Composer Installer](https://github.com/magento/magento-composer-installer)'s
`map` functionality in this module's `composer.json` file. It will copy over the top of your `phinx.php` every time this module
is installed. You should obviously commit your `phinx.php` file to version control if you make any changes to it.

## Using

Migrations are custom classes that extend `phinx` migrations and execute some code to change the DB. Migration class files
can sit in any of your Magento modules (`phinx` will scan for all migration folders at run time) in the following locations:

* `src/*/*/etc/migrations`
* `app/code/*/*/etc/migrations`

### Creating a migration

`phinx` can create a migration for us. For example

    bin/phinx create AddProductPimIdAttribute

It will then ask us which module we want to add the migration file to:

    Which migrations path would you like to use?
      [0] src/Foo/Catalog/etc/migrations
      [1] src/Foo/Sales/etc/migrations
     >

When the migration file is created in the selected destination we should see a class similar to the following:

    <?php

    use MX\PhinxMigrations\Migration;

    class AddProductPimIdAttribute extends Migration
    {
        /**
         * Upgrade the database.
         */
        public function up()
        {

        }

        /**
         * Rollback the database.
         */
        public function down()
        {

        }
    }

When writing our migration we should always include rollback code so that we can reverse deployments easily.

#### Magento setup helpers

We have access to 3 different setup helpers provided by Magento in each migration:

1. `$this->schemaSetup` (instance of `Magento\Framework\Setup\SchemaSetupInterface`)
2. `$this->dataSetup` (instance of `Magento\Framework\Setup\ModuleDataSetupInterface`)
3. `$this->eavSetup` (instance of `Magento\Eav\Setup\EavSetup`)

### Execute migrations

When you are happy with your migration you can execute it as follows

    bin/phinx migrate

### Rolling back

If you want to undo your migration then simply execute

    bin/phinx rollback

## Limitations

1. Upgrading Magento itself will still result in version changes in the Magento `module.xml` files, therefore zero-downtime 
deployments are not possible in this situation.

## FAQ / troubleshooting

Q. I have run `composer install` but no `phinx.php` file is generated for me, what happened?

A. `MX_PhinxMigrations` relies on the [Magento Composer Installer](https://github.com/magento/magento-composer-installer)'s map
functionality to copy files into the Magento project root. For some reason it fails to copy some files if you don't have an
`app/code` folder present, make sure this folder exists and then re-run `composer install` (remember to remove this module from
your vendor folder first so it re-installs it).

Q. When I run `bin/phinx create ...` it cannot find any migration paths to create the migration file, why?

A. In order for a migration to be created `phinx` will scan your project for `migration` folders in the paths mentioned in the
[section above](#using). Make sure you create at least one `migration` folder inside one of your Magento module's `etc` folders
and then it should work as expected.
