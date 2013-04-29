# laCarte [![Build Status](https://travis-ci.org/rtens/lacarte.png?branch=master)](https://travis-ci.org/rtens/lacarte)

With *laCarte* you can collect orders for a group of people. Each Order consists of a number of Menus, each with a date and a number of Dishes.

## Installation

The installation skript `install.php` downloads all dependencies with composer and sets up the database SQLite database and configuration files. So all you need to do is.

	git clone https://github.com/rtens/lacarte.git
	cd lacarte
	php install.php

## Background

This project started as a proof-of-concept for an experimental framework I've working on named [watoki]. I'm glad to say that it worked pretty well. I haven't taken full advantage of composite components yet though. And also, there is not JavaScript so far. If you want to check out watoki let me know.

[watoki]: http://github.com/watoki

## Contribution

I would be happy to find contributers. If you find a bug or are missing a feature, check out the [issues] which I'm using . The `+` tags refer to the importance of an issue.

If you are only interested in HTML: you can browse through the templates even without PHP. There is also a `test` module which renders the output of the component's automated tests.

[issues]: https://github.com/rtens/lacarte/issues