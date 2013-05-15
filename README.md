# clue/graph-composer [![Build Status](https://travis-ci.org/clue/graph-composer.png?branch=master)](https://travis-ci.org/clue/graph-composer)

Graph visualization for your project's `composer.json` and its dependencies:


![example dependency graph for clue/graph-composer](http://i.imgur.com/3DERCoA.png)

## Usage

Once clue/graph-composer is [installed](#install), you can simply invoke it via command line like this:

```
$ php graph-composer.php ~/path/to/your/project
```

## Install

This project requires PHP 5.3+ and GraphViz

```
$ sudo apt-get install php5-cli graphviz
$ git clone https://github.com/clue/graph-composer.git
$ cd graph-composer
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

## License

MIT
