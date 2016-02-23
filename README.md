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

To test, you can run

    make test
    
To fix warnings etc. from `make test`, you can run:

    make fix
    
To clean, you can run
    
    make clean
    
## License
None yet.
