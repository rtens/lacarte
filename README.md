# laCarte [![Build Status](https://travis-ci.org/rtens/lacarte.png?branch=master)](https://travis-ci.org/rtens/lacarte)

With *laCarte* you can collect orders for a group of people. Each Order consists of a number of Menus, each with a
date and a number of Dishes.

## Installation

The installation script `setup.php` downloads all dependencies with composer and sets up the SQLite
database and configuration files and can also run the tests. So all you need to do is.

	git clone https://github.com/rtens/lacarte.git
	cd lacarte
	php setup.php test

## Background

This project started as a proof-of-concept for an experimental framework I've been working on named [watoki].
Above all the routing/controller system [curir] and the template engine [tempan]. Buts it's not just an academic
exercise since it's actually in use at [researchgate] together with an iPad application.

[watoki]: http://github.com/watoki
[curir]: http://github.com/watoki/curir
[tempan]: http://github.com/watoki/tempan
[researchgate]: http://researchgate.net

## Contribution

I would be happy to find contributers. If you find a bug or are missing a feature, check out the [issues] which
I'm using for project management. The `+` tags refer to the importance of an issue.

If you are only interested in HTML: you can browse through the templates even without PHP.

[issues]: https://github.com/rtens/lacarte/issues