<?php
  session_start();
  $suffisso=$_GET['suffisso'];
  @require_once("../php-ini" . $suffisso . ".php");
  @require_once("../lib/funzioni.php");
  $token = $tokenbototp; 
  $url = $urlbottelegram;//Token bot Telegram
  $urlDestinazione =$url."registra.php?suffisso=$suffisso";
  $data = array("url" => $urlDestinazione);
  $data = json_encode($data);
  print $urlDestinazione."<br>";
  $url = "https://api.telegram.org/bot" . $token . "/setWebhook";
  $options = array(
      'http' => array(
          'header'  => "Content-type: application/json",
          'method'  => 'POST',
          'content' => $data
      )
  );
  $context  = stream_context_create($options);
  $r = file_get_contents($url, false, $context);
  echo $r;
 ?>
