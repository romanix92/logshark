<?php
//
//echo $_SERVER["REQUEST_METHOD"].": ".$data."\n";
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('memory_limit', '-1');

$log_storage = "/home/dpavlyuk/logagg/storage";
$rules_filename = "/home/dpavlyuk/logagg/rules.json";

if ($_GET["action"]=="save_rules") {
	  $data = file_get_contents('php://input');
	  $file = fopen($rules_filename, "w");
	  if (flock($file, LOCK_EX)) {
	     fwrite($file, $data);
	     fflush($file);
	     flock($file, LOCK_UN);
	  } else {
	     die("Can't lock file!");
	  }
	  fclose($file);
}

if ($_GET["action"]=="get_rules") {
   header('Content-type: text/plain');
   $file = fopen($rules_filename, "r");
   if (flock($file, LOCK_EX)) {
     fpassthru($file);
     flock($file, LOCK_UN);
   } else {
     die("Can't lock file!");
   }
   fclose($file);
}



if ($_GET["action"]=="log") {
   if (array_key_exists("file", $_GET)) {
       $filename = $log_storage."/".$_GET["file"];
       if (preg_match("#^([a-z0-9\.]+)\$#", $_GET["file"])) {
          $data = file_get_contents('php://input');
          echo $filename;
          //echo $data;
          $file = fopen($filename, "c");
          if (flock($file, LOCK_EX)) {
             fseek($file, 0, SEEK_END);
             echo "End position: ".ftell($file);
             fwrite($file, $data);
             fflush($file);
             echo "End position after: ".ftell($file);
             flock($file, LOCK_UN);
          } else {
             die("Can't lock file!");
          }
          fclose($file);
       }
   }
} else if ($_GET["action"]=="get") {
   header('Content-type: text/plain');
   
   if (array_key_exists("file", $_GET)) {
       $filename = $log_storage."/".$_GET["file"];
       if (preg_match("#^([a-z0-9\.]+)\$#", $_GET["file"])) {
	   
	   	header('Content-Disposition: attachment; filename="'.$_GET["file"].'.txt"');
       
          if (!file_exists($filename)) {
             touch($filename);
          }
          $file = fopen($filename, "r");
          if (flock($file, LOCK_EX)) {
             if (array_key_exists("offset", $_GET)) {
                 fseek($file, $_GET["offset"]);
             }
             
             if (array_key_exists("length", $_GET)) {
                 print(fread($file, $_GET["length"]));
				 print(fgets($file));
             } else {
                 fpassthru($file);
             }
             
             flock($file, LOCK_UN);
          } else {
             die("Can't lock file!");
          }
          fclose($file);
       }
   }
} else if ($_GET["action"]=="get_size") {
   header('Content-type: text/plain');
   if (array_key_exists("file", $_GET)) {
       $filename = $log_storage."/".$_GET["file"];
       if (preg_match("#^([a-z0-9\.]+)\$#", $_GET["file"])) {
          if (file_exists($filename)) {
			 print(filesize($filename));
          }
       }
   }
} else if ($_GET["action"]=="truncate") {
   header('Content-type: text/plain');
   if (array_key_exists("file", $_GET)) {
       $filename = $log_storage."/".$_GET["file"];
       if (preg_match("([a-z0-9\.]+)", $filename)) {
          $file = @fopen($filename, "r+");
          if (flock($file, LOCK_EX)) {
             ftruncate($file, 0);
             flock($file, LOCK_UN);
          } else {
             die("Can't lock file!");
          }
          fclose($file);
       }
    }
 }


//readfile('php://input')
?>