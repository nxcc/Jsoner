# JSONer

This is a MediaWiki extension that allows one to embed external JSON data (i.e. from
a REST API) into an article.

## Requirements

This extension requires at least `PHP >= 5.6` and the following PHP extensions:

* curl
* fileinfo
* intl
* mbstring

Using Debian / Ubuntu you can install the extensions like this:

    sudo apt-get install php5-curl php5-intl
    sudo service apache2 restart

To test if they are enabled (use your php.ini):

    $ php5 --php-ini /etc/php5/apache2/php.ini -m | grep -E 'fileinfo|mbstring|intl|curl'
    curl
    fileinfo
    intl
    mbstring

## Installation

### Download (recommended, with Composer)

Put this to your `composer.local.json`:

    {
        "require": {
            "noris/jsoner": "0.0.*"
        }
    }
    
and run `composer update` (or `composer install` if you don't have a composer.lock yet). 

### Download (not recommended, manually)

Download the extension and put it in your `extension/` folder.

### Add to MediaWiki

To enable this extension, add this to your `LocalSettings.php`:

    wfLoadExtension( 'JSONer' );

This will enable the JSONer extension and add the following functions to the MediaWiki parser:

* `#jsoner` with parameters `url` and filters, [see below](#available-filters).

## Configuration

The extension has multiple settings. Please put them after the `wfLoadExtension( 'JSONer' );`. 

### $jsonerBaseUrl (default = null)

    $jsonerBaseUrl = 'https://example.com/api/';

This can be used to prefix all `#jsoner` calls (the `url` argument specifically) with this url
so that you don't have to repeat yourself, if you only consume data from one domain. If omitted,
you have to provide complete domains in `url`.

### $jsonerUser / $jsonerPass (default = null)

    $jsonerUser = '<your_user>';
    $jsonerPass = '<your_pass>';

If both are set, this is passed to cURL to authenticate. If omitted, cURL tries unauthenticated.

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

This extension is under development. Anything may change.

You can clone is using

    git clone git@gitlab.noris.net:cda-ad/JSONer.git && cd JSONer
    # Install NodeJS, npm and PHP composer
    make devenv
    
To install it into your development MediaWiki, just symlink it to your `extensions` folder

    # Assuming you are in JSONer folder
    cd /path/to/your/extensions/folder
    ln -s /path/to/the/JSONer/extension JSONer
    
Then, install it [like described above](#installation).

To see what you can do run one of

    make
    make help

To test, you can run

    make test
    
To fix warnings etc. from `make test`, you can run:

    make fix
    
To clean, you can run
    
    make clean

## License
None yet.
