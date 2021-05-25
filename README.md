# Catalog

**Catalog** is a file indexing layer, like a table of contents. Its purpose is to make files, named according to a
uniform schema, easily and quickly retrievable without searching in the file system, and with adequate performance.

It can be used together for example with the [Flysystem](https://github.com/thephpleague/flysystem) file storage
library.

## Example use case

Suppose we are working with files named according to the following uniform schema:

```shell
../path/to/files/2020
../path/to/files/2021
../path/to/files/2020/USER#1
../path/to/files/2020/USER#2
../path/to/files/2020/USER#1/BLOGPOST#1_001.png
../path/to/files/2020/USER#1/BLOGPOST#1_002.png
../path/to/files/2020/USER#1/BLOGPOST#1_003.png
../path/to/files/2020/USER#1/BLOGPOST#2_001.png
../path/to/files/2020/USER#1/BLOGPOST#2_002.png
../path/to/files/2020/USER#1/PROFILE_001.png
../path/to/files/2020/USER#2/BLOGPOST#1_001.png
../path/to/files/2020/USER#2/BLOGPOST#1_002.png
../path/to/files/2020/USER#2/PROFILE_001.png
../path/to/files/2021/USER#1
../path/to/files/2021/USER#3
../path/to/files/2021/USER#1/BLOGPOST#2_003.png
../path/to/files/2021/USER#1/PROFILE_002.png
../path/to/files/2021/USER#3/BLOGPOST#1_001.png
../path/to/files/2021/USER#3/PROFILE_001.png
```

you can easily search between them using regular expression:

### Find all files upload by "user 1"

```php
$re = '/^(?=.*USER#1).*$/i';
$filenames = $catalog->findAll($re);
```

will result:

```php
array(8) {
  [0]=>
  string(51) "/path/to/files/USER#1/BLOGPOST#1_001.png"
  [1]=>
  string(51) "/path/to/files/USER#1/BLOGPOST#1_002.png"
  [2]=>
  string(51) "/path/to/files/USER#1/BLOGPOST#1_003.png"
  [3]=>
  string(51) "/path/to/files/USER#1/BLOGPOST#2_001.png"
  [4]=>
  string(51) "/path/to/files/USER#1/BLOGPOST#2_002.png"
  [5]=>
  string(48) "/path/to/files/USER#1/PROFILE_001.png"
  [6]=>
  string(51) "/path/to/files/USER#1/BLOGPOST#2_003.png"
  [7]=>
  string(48) "/path/to/files/USER#1/PROFILE_002.png"
}
```

### Find all files related to "blogpost 2" created by "user 1"

```php
$re = '/^(?=.*USER#1)(?=.*BLOGPOST#2).*/i';
$filenames = $catalog->findAll($re);
```

will result:

```php
array(3) {
  [0]=>
  string(51) "/path/to/files/2020/USER#1/BLOGPOST#2_001.png"
  [1]=>
  string(51) "/path/to/files/2020/USER#1/BLOGPOST#2_002.png"
  [2]=>
  string(51) "/path/to/files/2021/USER#1/BLOGPOST#2_003.png"
}
```

To make it work, all you have to do is name the files in the uniform schema and add them to the catalog when you save
them:

```php
$filename = '/path/to/files/2021/USER#1/BLOGPOST#2_004.png';
$filesystem->write($filename, $data);
$catalog->add($filename);
```

## Getting Started

```shell
composer require szabacsik/catalog
```

```shell
mkdir ../path/to/files/catalog -p
```

```php
<?php
use Szabacsik\Catalog\CatalogFile;
require_once(__DIR__.'/vendor/autoload.php');
$catalogWorkingDirectory='D:\tmp\catalog';
$catalog = new CatalogFile($catalogWorkingDirectory);
$catalog->add('/path/to/files/2020/USER#1/BLOGPOST#1_001.png');
$catalog->add('/path/to/files/2020/USER#1/BLOGPOST#2_001.png');
$re = '/^(?=.*USER#1).*$/i';
$re = '/^(?=.*USER#1)(?=.*BLOGPOST#2).*/i';
$filenames = $catalog->findAll($re);
var_dump($filenames);
```
