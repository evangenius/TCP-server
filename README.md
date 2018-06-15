# TCP-server #
Implement this project with programming language PHP. (server)

Implement test program with shell script and telnet. (client)

## Development environment ##

    $ uname -a
    Linux ubuntu 4.2.0-42-generic #49~14.04.1-Ubuntu SMP Wed Jun 29 20:22:11 UTC 2016 x86_64 x86_64 x86_64 GNU/Linux
    $ php -v
    PHP 5.5.9-1ubuntu4.25 (cli) (built: May 10 2018 14:37:18)
    Copyright (c) 1997-2014 The PHP Group
    Zend Engine v2.5.0, Copyright (c) 1998-2014 Zend Technologies
        with Zend OPcache v7.0.3, Copyright (c) 1999-2014, by Zend Technologies

## Configuration ##

modify address and port for test environment in server.php

    $address = '192.168.43.128';
    $port = 10008;

modify address and port for test environment in repeat_output.sh

    address="192.168.43.128"
    port="10008"

execute server.php
    
    php server.php

execute repeat_output.sh

    sh repeat_output.sh

