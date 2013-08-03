MVC skeleton
============

Main goal
-------------------------------------------

It's small and fast skeleton for prototyping web apps.
Inspired by Silex, but this framework is more easy for using and fixing.

Setup
-----

* Requires Debian like system

```shell
mkdir -p /root/sources/project
cd /root/sources/project
git clone https://github.com/flrnull/mvc-skeleton.git
cd mvc-skeleton/misc
chmod +x server-setup.sh
./server-setup.sh
```

Details
-------

* framework/ — abstract and solid classes, interfaces, never touch it!
* misc/ — setup script and software configs
* src/ — project back-end files (controllers, services, routes, etc)
    * controllers/ — MVC controllers folder
    * cron/ — crontab tasks
    * models/ — MVC models
    * resources/ — different project resources and data
        * db/ — DB creation files
        * logs/ — error and debug logs
    * services/ — service layer, bridge between controllers and models
    * templates/ — twig templates
    * app.php — project routes and ioc defines
    * config.php.default — default conf (could be overwrite by config.php file)
* vendor/ — external dependencies, never touch it!
* web/ — web root
    * css/ — styles 
    * js/ — javascript files
    * img/ — images
    * index.php — bootstrap file, all requests are started here

