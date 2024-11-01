<?php
	
function scr_is_bot() {
  $ua = $_SERVER['HTTP_USER_AGENT'];
 
  $bot = array(
        "googlebot",
        "msnbot",
        "yahoo"
  );
  foreach( $bot as $bot ) {
    if (stripos( $ua, $bot ) !== false){
      return true;
    }
  }
  return false;

}


