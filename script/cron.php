<?php

// A sample PHP Script to POST data using cURL

$date= date('l jS \of F Y h:i:s A');
$url_array = array('https://foodibd.com/v1/api/reRouting');

$urls = array();
foreach ($url_array as $key => $value) {
    //echo file_put_contents("testlog.txt","$value\n", FILE_APPEND);
    //callCurl($value); // call curl function. and store products in an array. you will get the all records in single arry.

        //echo file_put_contents("testlog.txt", "$value\n", FILE_APPEND);
            //callCurl($value); // call curl function. and store products in an array. you will get the all records in single arry.

            $crl = curl_init($value);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($crl, CURLINFO_HEADER_OUT, true);
            curl_setopt($crl, CURLOPT_POST, true);
            //curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);

            // Set HTTP Header for POST request
            curl_setopt(
                $crl,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                )
            );

            // Submit the POST request
            $result = curl_exec($crl);

            // handle curl error
            if ($result === false) {
                // throw new Exception('Curl error: ' . curl_error($crl));
                echo file_put_contents("cronlog.txt", "$result\n", FILE_APPEND);
                $result_noti = 0;
                die();
            } else {
                echo file_put_contents("cronlog.txt", "Succesfully send to $value $date\n", FILE_APPEND);
                $result_noti = 1;
               // die();
            }
            // Close cURL session handle
            curl_close($crl);
}
