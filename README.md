# hashworks/PhergieMovieSearch

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin to search both IMDB & OMDB.

## Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require hashworks/PhergieMovieSearch
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
// dependency
new \WyriHaximus\Phergie\Plugin\Dns\Plugin,
new \WyriHaximus\Phergie\Plugin\Http\Plugin(array('dnsResolverEvent' => 'dns.resolver')),
new \Phergie\Irc\Plugin\React\Command\Plugin,
new \hashworks\Phergie\Plugin\MovieSearch\Plugin(array(
    // Optional. Sets the output when no specific information is requested.
    'responseFormat' => '%title% (%year%) [%imdb-id%], %imdb-rating%★ - %genre% - %plot%'
))
```
