**Spyc** is a YAML loader/dumper written in pure PHP. Given a YAML document, Spyc will return an array that
you can use however you see fit. Given an array, Spyc will return a string which contains a YAML document 
built from your data.

**YAML** is an amazingly human friendly and strikingly versatile data serialization language which can be used 
for log files, config files, custom protocols, the works. For more information, see http://www.yaml.org.

Spyc supports YAML 1.0 specification.

## Using Spyc

Using Spyc is trivial:

```php
<?php
require_once "spyc.php";
$Data = Spyc::YAMLLoad('spyc.yaml');
```

or (if you prefer functional syntax)

```php
<?php
require_once "spyc.php";
$Data = spyc_load_file('spyc.yaml');
```

## Donations, anyone?

If you find Spyc useful, I'm accepting Bitcoin donations (who doesn't these days?) at 193bEkLP7zMrNLZm9UdUet4puGD5mQiLai
