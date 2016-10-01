[![Build Status](https://api.travis-ci.org/zerustech/string.svg)](https://travis-ci.org/zerustech/string)

ZerusTech String Component
================================================
The *ZerusTech String Component* is a library that provides classes and
utilities to manipulate string.

Installation
-------------

You can install this component in 2 different ways:

* Install it via Composer
```bash
$ cd <project-root-directory>
$ composer require zerustech/string
```

* Use the official Git repository [zerustech/string][2]

Examples
-------------

### UTF32 ###

This class reads ascii hexadecimal bytes from the subordinate input stream and
converts them to binary bytes. Two hexadecimal bytes are converted to one binary
byte.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\String\Unicode\UTF32;

// Converts UTF-32 code 0x20ac to UTF-8 
UTF32::convertToUTF8(0x20ac); // 'e282ac'

// Converts UTF-32 code 0x20ac to UTF-16
UTF32::convertToUTF16(0x20ac); // '20ac'

```

References
----------
* [The zerustech/string project][2]

[1]:  https://opensource.org/licenses/MIT "The MIT License (MIT)"
[2]:  https://github.com/zerustech/string "The zerustech/string Project"

License
-------
The *ZerusTech String Component* is published under the [MIT License][1].
