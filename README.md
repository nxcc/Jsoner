# JSONer

This is a MediaWiki extension that allows one to embed external JSON data (i.e. from
a REST API) into an article.

## Installation

Put the extension in your `extension/` folder and add this in your `LocalSettings.php`:

    wfLoadExtension( 'JSONer' );
    $jsonerBaseUrl = 'https://example.com/api/';
    $jsonerUser = '<your_user>';
    $jsonerPass = '<your_pass>';

This will enable the JSONer extension and add some functions to the MediaWiki parser:

* `#jsoner` with parameters `url` and optionally filters

## Usage

To use JSONer, first think of a resource you want to access. We will use `/person/42`, which
will return

    {
        "_id": 42,
        "_type": "Person",
        "name": "Jonas",
        "age": 26,
        "status": "developer"
    }
    
and we want to filter this list to only return `name` and `status`. This can be done as follows:

## Available Filters

### SelectSubtreeFilter

Given the following data
    {
        "_id": 42,
        "_type": "Person",
        "location": {
            "city": "SomeCity",
            "street": "SomeStreet",
        },
        "age": 26,
        "status": "developer"
    }
    
And a filter like this

    {{ #jsoner:url=… | subtree=location }}
    
Will return this

    "location": {
        "city": "SomeCity",
        "street": "SomeStreet",
    }

### SelectKeysFilter

TODO: Document


## Development

**Currently, this extension is non-functional** and under heavy development. You can still
clone is using

    git clone git@gitlab.noris.net:cda-ad/JSONer.git && cd JSONer

This automates the recommended code checkers for PHP and JavaScript code in Wikimedia projects
(see https://www.mediawiki.org/wiki/Continuous_integration/Entry_points).
To take advantage of this automation:

  # install nodejs, npm, and PHP composer
  # change to the extension's directory
  # npm install
  # composer install

Once set up, running `npm test` and `composer test` will run automated code checks.
