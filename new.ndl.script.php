<?php
/// $fname = $_GET['file'];
/// $fpath = "downloads/$fname";
$fsize = filesize($fpath);
$bufsize = 20000;

if(isset($_SERVER['HTTP_RANGE']))  //Partial download
{
   if(preg_match("/^bytes=(\\d+)-(\\d*)$/", $_SERVER['HTTP_RANGE'], $matches)) { //parsing Range header
       $from = $matches[1];
       $to = $matches[2];
       if(empty($to))
       {
           $to = $fsize - 1;  // -1  because end byte is included
                               //(From HTTP protocol:
// 'The last-byte-pos value gives the byte-offset of the last byte in the range; that is, the byte positions specified are inclusive')
       }
       $content_size = $to - $from + 1;

       header("HTTP/1.1 206 Partial Content");
       header("Content-Range: $from-$to/$fsize");
       header("Content-Length: $content_size");
       header("Content-Type: application/force-download");
       header("Content-Disposition: attachment; filename=$fname");
       header("Content-Transfer-Encoding: binary");

       if(file_exists($fpath) && $fh = fopen($fpath, "rb"))
       {
           fseek($fh, $from);
           $cur_pos = ftell($fh);
           while($cur_pos !== FALSE && ftell($fh) + $bufsize < $to+1)
           {
               $buffer = fread($fh, $bufsize);
               print $buffer;
               $cur_pos = ftell($fh);
           }

           $buffer = fread($fh, $to+1 - $cur_pos);
           print $buffer;

           fclose($fh);
       }
       else
       {
           header("HTTP/1.1 404 Not Found");
           exit;
       }
   }
   else
   {
       header("HTTP/1.1 500 Internal Server Error");
       exit;
   }
}
else // Usual download
{
   header("HTTP/1.1 200 OK");
   header("Content-Length: $fsize");
   header("Content-Type: application/force-download");
   header("Content-Disposition: attachment; filename=$fname");
   header("Content-Transfer-Encoding: binary");

   if(file_exists($fpath) && $fh = fopen($fpath, "rb")){
       while($buf = fread($fh, $bufsize))
           print $buf;
       fclose($fh);
   }
   else
   {
       header("HTTP/1.1 404 Not Found");
   }
}
?>