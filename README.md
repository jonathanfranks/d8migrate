D8 Migrate Camp Presentation
============================

This is the source code that goes along with my Twin Cities Drupal Camp presentation, [DRUPAL 8 MIGRATE: IT'S NOT ROCKET SCIENCE...](https://2018.tcdrupal.org/session/drupal-8-migrate-its-not-rocket-science).

It is used to demonstrate migrate components and show how to create custom migrations.

Installation
============

To set up this project, you'll need composer and drush.

1. First, get the project from github. I presume you've done this already, otherwise you probably wouldn't be reading this.
2. Install the composer dependencies.
3. Install the site and set your site UUID so you can import config.
4. Your site setup commands are:
  1. composer install
  2. drush si standard--db-url=mysql://drupal8:drupal8@database/drupal8 --account-pass=admin -y
  3. drush cset system.site uuid "7f5770ff-e9d2-4a17-b1ef-47e9e98b8d96" -y
  4. drush cim -y
  5. drush cr

Now that that's done, you can follow along with the presentation and start doing some migrations.