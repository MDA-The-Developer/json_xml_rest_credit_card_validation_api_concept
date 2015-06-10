<?php

class log_message_to_file{
  public static  function add_log($message){



// current date with appropriate view
     $current_time= date('l jS \of F Y h:i:s A');
$file = fopen($_SERVER['DOCUMENT_ROOT'].'/api/log.txt',"a");
fwrite($file,$message.' '.$current_time."\r\n");
fclose($file);

}
}
