# clue/graph-composer [![Build Status](https://travis-ci.org/clue/graph-composer.png?branch=master)](https://travis-ci.org/clue/graph-composer)

Graph visualization for your project's `composer.json` and its dependencies:


![example dependency graph for clue/graph-composer](http://www.lueck.tv/graph-composer/graph-composer.svg)

## Usage

Once clue/graph-composer is [installed](#install), you can simply invoke it via command line like this:

```bash
$ php graph-composer.phar show ~/path/to/your/project
```

## Install

You can grab a copy of clue/graph-composer in either of the following ways.

### As a phar (recommended)

You can simply download a pre-compiled and ready-to-use version as a Phar
to any directory:

```bash
$ wget http://www.lueck.tv/graph-composer/graph-composer.phar
```


> If you prefer a global (system-wide) installation without having to type the `.phar` extension
each time, you may simply invoke:
> 
> ```bash
> $ chmod 0755 graph-composer.phar
> $ sudo mv graph-composer.phar /usr/local/bin/graph-composer`
> ```
>
> You can verify everything works by running:
> 
> ```bash
> $ graph-composer --version
> ```

#### Updating phar

There's no separate `update` procedure, simply overwrite the existing phar with the new version downloaded.

> Note: [Ticket #1](https://github.com/clue/graph-composer/issues/1) will introduce a `self-update` command eventually.

### Manual Installation from Source

This project requires PHP 5.3+, Composer and GraphViz:

```bash
$ sudo apt-get install php5-cli graphviz
$ git clone https://github.com/clue/graph-composer.git
$ cd graph-composer
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

> If you want to build the above mentioned `graph-composer.phar` yourself, you have
to install [clue/phar-composer](https://github.com/clue/phar-composer#install)
and can simply invoke:
>
> ```bash
> $ php phar-composer.phar build ~/workspace/graph-composer
> ```

#### Updating manually

```bash
$ git pull
$ php composer.phar install
```

## License

MIT
