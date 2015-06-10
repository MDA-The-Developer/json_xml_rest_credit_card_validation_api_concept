<?php
class set_some_sql{
public static function set_some_data_to_sql($sql){
    $dbh = new PDO(CONNECT, DB_USER, DB_PASS);
    $sth = $dbh->prepare($sql);
    $sth->execute();
        $dbh = null;
    }
}