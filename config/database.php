<?php

if ($_SERVER['HTTP_HOST'] == 'bdw1.univ-lyon1.fr') {
    $user = $_SERVER["REMOTE_USER"];
    $password = $_SERVER["PHP_AUTH_PW"];

    return array(
        'dsn' => "mysql:dbname=$user;host=localhost;charset=utf8",
        'username' => $user,
        'password' => $password
    );
}

return array(
    'dsn' => "mysql:dbname=morpion;host=localhost;charset=utf8;unix_socket=/run/mysqld/mysqld.sock",
    'username' => 'root',
    'password' => ''
);
