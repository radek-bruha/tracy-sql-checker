# [**Tracy**](https://github.com/nette/tracy) [**Sql**](https://github.com/doctrine/orm) Checker
[![Downloads](https://img.shields.io/packagist/dt/radek-bruha/tracy-sql-checker.svg?style=flat-square)](https://packagist.org/packages/radek-bruha/tracy-sql-checker)
[![Build Status](https://img.shields.io/github/workflow/status/radek-bruha/tracy-sql-checker/Workflow?style=flat-square)](https://github.com/radek-bruha/tracy-sql-checker/actions)
[![Latest Stable Version](https://img.shields.io/github/release/radek-bruha/tracy-sql-checker.svg?style=flat-square)](https://github.com/radek-bruha/tracy-sql-checker/releases)

**Usage**
```
composer require radek-bruha/tracy-sql-checker
```

**Tracy Usage**
```php
Tracy\Debugger::getBar()->addPanel(new \Bruha\Tracy\SqlCheckerPanel());
```

**Nette Framework Usage**
```yml
tracy:
    bar: [\Bruha\Tracy\SqlCheckerPanel()]
```

**Example Website Usage**

![Tracy Sql Checker](https://i.imgur.com/2nb8C23.png)
