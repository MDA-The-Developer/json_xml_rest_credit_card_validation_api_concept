<?php
class get_sql_data{
  public static  function get_some_data_from_sql($get_some)
    {
        $dbh = new PDO(CONNECT, DB_USER, DB_PASS);
        $sth = $dbh->prepare($get_some);
        $sth->execute();
        $result = $sth->fetchAll();
        return $result;
        $dbh = null;
    }
}
