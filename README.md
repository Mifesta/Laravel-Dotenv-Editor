# Laravel Dotenv Editor

![laravel-dotenv-editor](https://cloud.githubusercontent.com/assets/9862115/25982836/029612b2-370a-11e7-82c5-d9146dc914a1.png)

[![Latest Stable Version](https://poser.pugx.org/mifesta/dotenv-editor/v/stable)](https://packagist.org/packages/mifesta/dotenv-editor)
[![Total Downloads](https://poser.pugx.org/mifesta/dotenv-editor/downloads)](https://packagist.org/packages/mifesta/dotenv-editor)
[![Latest Unstable Version](https://poser.pugx.org/mifesta/dotenv-editor/v/unstable)](https://packagist.org/packages/mifesta/dotenv-editor)
[![License](https://poser.pugx.org/mifesta/dotenv-editor/license)](https://packagist.org/packages/mifesta/dotenv-editor)

Laravel Dotenv Editor is an the .env file editor (or files with same structure and syntax) for Laravel 5+. Now you can easily edit .env files with following features:

* Read raw content of file
* Read lines in file content
* Read setters (key-value-pair) in file content
* Determine one key name of setter if exists
* Append empty lines into file
* Append comment lines into file
* Append new or update exists setter lines into file
* Delete exists setter line in file
* Backup and restore file
* Manage backup files

# Documentation
Look at one of the following topics to learn more about Laravel Dotenv Editor

* [Versions and compatibility](#versions-and-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
    - [Auto backup mode](#auto-backup-mode)
    - [Backup location](#backup-location)
* [Usage](#usage)
    - [Working with facade](#working-with-facade)
    - [Using dependency injection](#using-dependency-injection)
    - [Loading file for working](#loading-file-for-working)
    - [Reading file content](#reading-file-content)
        - [Reading raw content](#reading-raw-content)
        - [Reading content by lines](#reading-content-by-lines)
        - [Reading content by keys](#reading-content-by-keys)
        - [Determine if key is exists](#determine-if-key-is-exists)
        - [Get value of a key](#get-value-of-a-key)
    - [Writing content into file](#writing-content-into-file)
        - [Add an empty line into buffer](#add-an-empty-line-into-buffer)
        - [Add a comment line into buffer](#add-a-comment-line-into-buffer)
        - [Add or update a setter into buffer](#add-or-update-a-setter-into-buffer)
        - [Add or update multi setter into buffer](#add-or-update-multi-setter-into-buffer)
        - [Delete a setter line in buffer](#delete-a-setter-line-in-buffer)
        - [Delete multi setter lines in buffer](#delete-multi-setter-lines-in-buffer)
        - [Save buffer into file](#save-buffer-into-file)
    - [Backing up and restoring file](#backing-up-and-restoring-file)
        - [Backup your file](#backup-your-file)
        - [Get all backup versions](#get-all-backup-versions)
        - [Get latest backup version](#get-latest-backup-version)
        - [Restore your file from latest backup or other file](#restore-your-file-from-latest-backup-or-other-file)
        - [Delete one backup file](#delete-one-backup-file)
        - [Delete multi backup files](#delete-multi-backup-files)
        - [Change auto backup mode](#change-auto-backup-mode)
    - [Method chaining](#method-chaining)
    - [Working with Artisan CLI](#working-with-artisan-cli)
* [License](#license)
* [Thanks from author](#thanks-for-use)

## Versions and compatibility
Currently, Laravel Dotenv Editor only have version 1.x that is compatible with Laravel 5+ and later. This package is not support for Laravel 4.2 and earlier versions.

## Installation
You can install this package through [Composer](https://getcomposer.org).

- First, edit your project's `composer.json` file to require `mifesta/dotenv-editor`:
```php
"require": {
    // other reqire packages
    "mifesta/dotenv-editor": "1.*"
},
```

- Next, run the composer update command in your command line interface:
```shell
$ composer update
```

> **Note:** Instead of performing the above two steps, you can perform faster with the command line `$ composer require mifesta/dotenv-editor:1.*`.

- Once update operation completes, the third step is add the service provider. Open `config/app.php`, and add a new item to the providers array:
```php
'providers' => array(
    // other providers
    Mifesta\DotenvEditor\DotenvEditorServiceProvider::class,
),
```

- The next step is add the follow line to the section `aliases` in file `config/app.php`:
```php
'DotenvEditor' => Mifesta\DotenvEditor\Facades\DotenvEditor::class,
```

## Configuration
To get started, you'll need to publish configuration file:
```shell
$ php artisan vendor:publish --provider="Mifesta\DotenvEditor\DotenvEditorServiceProvider" --tag="config"
```

This will create a `config/dotenv-editor.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

#### Auto backup mode
The option `autoBackup` is determine that your orignal file will be backed up before save or not.

#### Backup location
The option `backupPath` is where that your file is backed up to. This value is the sub path (sub-folder) from root folder of project application.

## Usage

#### Working with facade
Laravel Dotenv Editor has a facade with name is `Mifesta\DotenvEditor\Facades\DotenvEditor`. You can do any operation through this facade. For example:
```php
<?php

namespace YourNamespace;

// your code

use Mifesta\DotenvEditor\Facades\DotenvEditor;

class YourClass
{
    public function yourMethod()
    {
        DotenvEditor::doSomething();
    }
}
```

#### Using dependency injection
This package also supports dependency injection, you can easily use dependency injection to inject an instance of the `Mifesta\DotenvEditor\DotenvEditor` class into your controller or other class. Example:
```php
<?php

namespace App\Http\Controllers;

// your code

use Mifesta\DotenvEditor\DotenvEditor;

class TestDotenvEditorController extends Controller {

    protected $editor;

    public function __construct(DotenvEditor $editor)
    {
        $this->editor = $editor;
    }

    public function doSomething()
    {
        $editor = $this->editor->doSomething();
    }
}
```

#### Loading file for working
Default, Laravel Dotenv Editor will load file `.env` in root folder of your project whenever you use the `DotenvEditor` facade. Example:
```php
$content = DotenvEditor::getContent(); // Get raw content of file .env in root folder
```
However, if you want to explicitly specify what files you will work with, you should use method `load()`. Example:

    $file = DotenvEditor::load(); // Working with file .env in root folder
    $file = DotenvEditor::load('.env.example'); // Working with file .env.example in root folder
    $file = DotenvEditor::load(storage_path('dotenv-editor/backups/.env.backup')); // Working with file .env.backup in folder storage/dotenv-editor/backups/

Method `load()` have three parameters:
```php
$file = DotenvEditor::load($filePath, $restoreIfNotFound, $restorePath);
```
- The first parameter is the path to file you want to work with. Set `null` to work with file `.env` in root folder.
- The second parameter is allow restoring your file if it is not found.
- The third parameter is the path to file use to restoring. Set `null` to restore from a earlier backup file.

#### Reading file content

###### Reading raw content
You can use method `getContent()` to get raw content in your file. Example:
```php
$content = DotenvEditor::getContent();
```
This will return raw file content as a string

###### Reading content by lines
Use method `getLines()` to get all lines in your file. Example:
```php
$lines = DotenvEditor::getLines();
```
This will return an array. Each element in array, you can see following info:
- Number of line
- Raw content of line
- Parsed content of line, include: type of line (empty, comment, setter...), key name of setter, value of setter, comment of setter...

###### Reading content by keys
Use method `getKeys($keys = [])` to get all setter lines in your file. Example:
```php
$keys = DotenvEditor::getKeys(); // Get all keys
$keys = DotenvEditor::getKeys(['APP_DEBUG', 'APP_URL']); // Only get two given keys if exists
```
This will return an array. Each element in array, you can see following info:
- Number of line
- Key name of setter
- Value of setter
- Comment of setter
- This key used "export " command or not

###### Determine if key is exists
Use method `keyExists($key)`. Example:
```php
$keyExists = DotenvEditor::keyExists('APP_URL'); // Return true|false
```

###### Get value of a key
Use method `getValue($key)`. Example:
```php
$value = DotenvEditor::getValue('APP_URL');
```

#### Writing content into file

To edit file content, you have two job:
- First is writing content into buffer
- Second is saving buffer into file

###### Add an empty line into buffer

Use method `addEmpty()`. Example:
```php
$file = DotenvEditor::addEmpty();
```

###### Add a comment line into buffer
Use method `addComment($comment)`. Example:
```php
$file = DotenvEditor::addComment('This is a comment line');
```

###### Add or update a setter into buffer
Use method `setKey($key, $value = null, $comment = null, $export = false)`. Example:
```php
$file = DotenvEditor::setKey('ENV_KEY'); // Set key ENV_KEY with empty value
$file = DotenvEditor::setKey('ENV_KEY', 'anything-you-want'); // Set key ENV_KEY with none empty value
$file = DotenvEditor::setKey('ENV_KEY', 'anything-you-want', 'your-comment'); // Set key ENV_KEY with a value and comment
$file = DotenvEditor::setKey('ENV_KEY', 'new-value-1'); // Update key ENV_KEY with a new value and keep earlier comment
$file = DotenvEditor::setKey('ENV_KEY', 'new-value', null, true); // Update key ENV_KEY with a new value, keep earlier comment and use 'export ' before key name
$file = DotenvEditor::setKey('ENV_KEY', 'new-value-2', '', false); // Update key ENV_KEY with a new value and clear comment
```

###### Add or update multi setter into buffer
Use method `setKeys($data)`. Example:
```php
$file = DotenvEditor::setKeys([
    [
        'key'     => 'ENV_KEY_1',
        'value'   => 'your-value-1',
        'comment' => 'your-comment-1',
        'export'  => true
    ],
    [
        'key'     => 'ENV_KEY_2',
        'value'   => 'your-value-2',
        'export'  => true
    ],
    [
        'key'     => 'ENV_KEY_3',
        'value'   => 'your-value-3',
    ]
]);
```

###### Delete a setter line in buffer
Use method `deleteKey($key)`. Example:
```php
$file = DotenvEditor::deleteKey('ENV_KEY');
```

###### Delete multi setter lines in buffer
Use method `deleteKeys($keys)`. Example:
```php
$file = DotenvEditor::deleteKeys(['ENV_KEY_1', 'ENV_KEY_2']); // Delete two keys
```

###### Save buffer into file
```php
$file = DotenvEditor::save();
```

#### Backing up and restoring file

###### Backup your file
```php
$file = DotenvEditor::backup();
```

###### Get all backup versions
```php
$backups = DotenvEditor::getBackups();
```

###### Get latest backup version
```php
$latestBackup = DotenvEditor::getLatestBackup();
```

###### Restore your file from latest backup or other file
```php
$file = DotenvEditor::restore(); // Restore from latest backup
$file = DotenvEditor::restore(storage_path('dotenv-editor/backups/.env.backup_2017_04_10_152709')); // Restore from other file
```

###### Delete one backup file
```php
$file = DotenvEditor::deleteBackup(storage_path('dotenv-editor/backups/.env.backup_2017_04_10_152709'));
```

###### Delete multi backup files
```php
$file = DotenvEditor::deleteBackups([
    storage_path('dotenv-editor/backups/.env.backup_2017_04_10_152709'),
    storage_path('dotenv-editor/backups/.env.backup_2017_04_11_091552')
]); // Delete two backup file

$file = DotenvEditor::deleteBackups(); // Delete all backup
```

###### Change auto backup mode
```php
$file = DotenvEditor::autoBackup(true); // Enable auto backup
$file = DotenvEditor::autoBackup(false); // Disable auto backup
```

#### Method chaining
Some functions of loading, writing, backing up, restoring are implementation and usage of method chaining. So these functions can be called to chained together in a single statement. Example:
```php
$file = DotenvEditor::load('.env.example')->backup()->setKey('APP_URL', 'http://example.com')->save();
return $file->getKeys();
```

#### Working with Artisan CLI
Now, Laravel Dotenv Editor have total 6 commands can use easily with Artisan CLI. Such as:
- php artisan dotenv:backup
- php artisan dotenv:get-backups
- php artisan dotenv:restore
- php artisan dotenv:get-keys
- php artisan dotenv:set-key
- php artisan dotenv:delete-key

Please use each above command with option --help(-h) for details of usage. Example:
```shell
$ php artisan dotenv:get-backups --help
```

#### Exceptions

## License
The Laravel Dotenv Editor is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Thanks for use
Hopefully, this package is useful to you.
