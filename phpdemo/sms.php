<?php

$params=array('sender_id'=>'FSTSMS', 'message'=>'Hello User, You have succesfully registered', 'language'=>'english', 'route'=>'p','numbers'=>$mobile);
      $post_data = json_encode($params);
      $defaults = array(
      CURLOPT_URL => 'https://www.fast2sms.com/dev/bulk',
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $post_data,
      CURLINFO_HEADER_OUT => true,
      CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'authorization: xiCL2ucXABlhDp5JrGnSqmPbOjZ8zk1otdvVIQTeYU0a6NEfM4qNuD7basXVOHTgpEtZhYBwcnGoRri0')
      );
      $ch = curl_init();
      curl_setopt_array($ch, ($defaults));
      $rest = curl_exec($ch);
 
    if ($rest === false) {
        // throw new Exception('Curl error: ' . curl_error($crl));
        //print_r('Curl error: ' . curl_error($crl));
        $result_noti = 0;
    } else {
 
        $result_noti = 1;
    }
 
    //curl_close($crl);
    //print_r($result_noti);die;
    print_r($rest);
?>