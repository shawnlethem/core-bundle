VideoGamesRecordsCoreBundle
===========================

Master
------

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/video-games-records/CoreBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/video-games-records/CoreBundle/?branch=master)
[![Build Status](https://travis-ci.org/video-games-records/CoreBundle.svg?branch=master)](https://travis-ci.org/video-games-records/CoreBundle)

Develop
-------

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/video-games-records/CoreBundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/video-games-records/CoreBundle/?branch=develop)
[![Build Status](https://travis-ci.org/video-games-records/CoreBundle.svg?branch=develop)](https://travis-ci.org/video-games-records/CoreBundle)

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require video-games-records/core-bundle "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Configuration
---------------------

### Database

In order to link your User entity to this module you should add the following configuration:
(Replace ProjetNormandie\UserBundle\Entity\User with your user class).

[Official documentation](http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html)

```yaml
# Doctrine Configuration - config.yml
doctrine:
    orm:
        ...
        resolve_target_entities:
            VideoGamesRecords\CoreBundle\Entity\User\UserInterface: AppBundle\Entity\User
            VideoGamesRecords\CoreBundle\Entity\User\GroupeInterface: AppBundle\Entity\Group
```

After resolving the entity you can update your database schema.

### Routing

```yaml
video_games_records_core:
    resource: "@VideoGamesRecordsCoreBundle/Controller/"
    type:     annotation
    prefix:   /{_locale}/
    requirements:
        _locale: '%app_locales%'
    defaults:
        _locale: '%locale%'
```
