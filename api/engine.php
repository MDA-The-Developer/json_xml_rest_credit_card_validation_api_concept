<?php
class valid {
    private function data_validation()
    {
        log_message_to_file::add_log("Validation started....");

// we can scan directory to get names of files to require them and then we will call every plugin
// i think that this is the easiest way to connect any singleton public function to call it later inside some
// private function ...


        /////    plugins
        $plugins_files = scandir($_SERVER['DOCUMENT_ROOT'] . '/api/plugins');
        for ($i = 2; $i < count($plugins_files); $i++) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/api/plugins/' . "$plugins_files[$i]";
        }


        // getting data from json object
        if (isset($_POST['json'])) {
            log_message_to_file::add_log("test #1 started");
            $json_decoded = json_decode($_POST['json']);
            $some_card_number = $json_decoded->{'card'};
            $some_exp_date = $json_decoded->{'exp_date'};
            $some_cvv = $json_decoded->{'CVV2'};
            $some_email = $json_decoded->{'email'};
        }

        // getting data from json object phone

        if (isset($_POST['json_p'])) {
            log_message_to_file::add_log("test #2 started");
            $json_p_decoded = json_decode($_POST['json_p']);
            $phone_to_check = $json_p_decoded->{'phone'};
            $passphrase = $json_p_decoded->{'passphrase'};
        }


        //xml
        if (isset($_POST['xml'])) {
            log_message_to_file::add_log("test #3 started");
            $xml = simplexml_load_string($_POST['xml']);
            $some_card_number = $xml->{'card'};
            $some_exp_date = $xml->{'exp_date'};
            $some_cvv = $xml->{'cvv2'};
            $some_email = $xml->{'email'};
        }

        // xml phone
        if (isset($_POST['xml_p'])) {
            log_message_to_file::add_log("test #4 started");
            $xml = simplexml_load_string($_POST['xml_p']);
            $phone_to_check = $xml->{'phone'};
            $passphrase = $xml->{'passphrase'};


        }

        if (isset($_POST['json']) || isset($_POST['xml'])) {
            // in default case every field validation equals to false
            $json_luhn_algorithm_ok = false;
            $json_card_valid = false;
            $json_exp_date_valid = false;
            $json_cvv2_value_valid = false;
            $json_email_value_valid = false;


            // Luhn's algorithm test
            if (test_luhn::is_valid_luhn($some_card_number)) {
                log_message_to_file::add_log("Luhn's algorithm ok...");
                $json_luhn_algorithm_ok = true;
            } else {
                log_message_to_file::add_log("Luhn's algorithm error...");
                return false;
            }


            // registration expired test
            if (preg_match("/^(0[1-9]|1[0-2])\/?([0-9]{4}|[0-9]{2})$/", $some_exp_date)) {
                $json_exp_date_valid = true;
                log_message_to_file::add_log("reg expired date ok...");
            } else {
                log_message_to_file::add_log("reg expired date bad data...");
                return false;
            }


            if (strlen($some_cvv) == 3) {
                $json_cvv2_value_valid = true;
                log_message_to_file::add_log("CVV2  --- ok..................");
            } else {

                log_message_to_file::add_log("CVV2 --- error...............");
                return false;
            }


            if (preg_match("/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i", $some_email)) {
                $json_email_value_valid = true;
                log_message_to_file::add_log("email ok...");
            } else {
                log_message_to_file::add_log("email error...");
                return false;
            }

            // creating hash of card code which is entered
            $hash_of_card_to_test = hash_hmac('snefru256', 'lets write some text here', $some_card_number);
            // need to get some variables from database
            $var_from_sql = get_sql_data::get_some_data_from_sql("SELECT * FROM cards WHERE email='$some_email'AND cvv2='$some_cvv' AND exp_date='$some_exp_date' AND card_hash='$hash_of_card_to_test'");

            if (empty($var_from_sql)) {
                log_message_to_file::add_log("card data is not match...");
                return false;
            }

            if (count($var_from_sql) > 1) {
                log_message_to_file::add_log("more then one matches in database...");
                return false;
            }

            // profile (card) exist check
            if (!isset($var_from_sql[0]['email']) || !isset($var_from_sql[0]['cvv2']) || !isset($var_from_sql[0]['exp_date']) || !isset($var_from_sql[0]['card_hash'])) {
                log_message_to_file::add_log("error with array data from database ");
                return false;
            } else {
                log_message_to_file::add_log("sql request ok...");
            }


            ///  checking hashes between card hash from json and existed from database
            if ($hash_of_card_to_test == $var_from_sql[0]['card_hash']) {
                log_message_to_file::add_log("credit card hash test passed ");
                $json_card_valid = true;
            } else {
                return false;
            }


            ///  everything is ok then true
            if ($json_luhn_algorithm_ok == true && $json_card_valid == true && $json_exp_date_valid == true && $json_cvv2_value_valid == true && $json_email_value_valid == true) {
                log_message_to_file::add_log("card validation finished successfully");
                return true;
            } else {
                log_message_to_file::add_log("something wrong ");
                return false;
            }
        }


        // validation by phone from json
        if (isset($_POST['json_p']) || isset($_POST['xml_p'])) {


            if (preg_match("/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]‌​)\s*)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)([2-9]1[02-9]‌​|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})/", $phone_to_check)) {
                log_message_to_file::add_log("phone check started ");

                $test_hash = hash_hmac('tiger160,3', 'people always talk about the weather', $passphrase);
                $phone_sql_array_to_get = get_sql_data::get_some_data_from_sql("SELECT * FROM cards WHERE phone='$phone_to_check' AND secret_word='$test_hash'");

                if (!empty($phone_sql_array_to_get)) {
                    log_message_to_file::add_log("phone is ok");
                    return true;            //////////////  true
                } else {
                    log_message_to_file::add_log("phone number empty database result");
                    return false;
                }
            } else {
                log_message_to_file::add_log("phone number is not correct");
                return false;
            }
        }


    }



    function bool_r(){
        $bool_var=$this->data_validation();

        if($bool_var==true){
            log_message_to_file::add_log("bool true");
            return true;
        }else{
            log_message_to_file::add_log("bool false");
            return false;
        }
    }
}

