<?PHP
extract($_POST);
function compress($zip) {
// compress a file without using shell
$zip=rtrim($zip);
$fp = @fopen("dump/backup.sql","rb");
if (file_exists("dump/".$zip.".gz")) unlink("dump/".$zip.".gz");
$zp = @gzopen("dump/".$zip.".gz", "wb9");
if(!$fp) {
   die("No sql file found"); 
}    
if(!$zp) {
   die("Cannot create zip file");
}    

while(!feof($fp)){
	$data=fgets($fp, 8192);	// buffer php
	gzwrite($zp,$data);
}
fclose($fp);
gzclose($zp);
return true;
}

if ($zipit) {
   if ($zipit==2) {
      if (compress($zipname)==true) header("location: dump/".$zipname.".gz");
   }
   if ($zipit==1) {
      header("location: dump/backup.sql");
   }
} else {
   die("File error");
}
?>         

	
	

