# JSONer

This is a MediaWiki extension that allows one to embed external JSON data (i.e. from
a REST API) into an article.

## Installation
**Currently, this extension is non-functional** and under heavy development. You can still
clone is using

    git clone git@gitlab.noris.net:cda-ad/JSONer.git && cd JSONer

## Development

This automates the recommended code checkers for PHP and JavaScript code in Wikimedia projects
(see https://www.mediawiki.org/wiki/Continuous_integration/Entry_points).
To take advantage of this automation.
  # install nodejs, npm, and PHP composer
  # change to the extension's directory
  # npm install
  # composer install

Once set up, running `npm test` and `composer test` will run automated code checks.
