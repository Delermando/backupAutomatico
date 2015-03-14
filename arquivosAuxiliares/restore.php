<?
if (!file_exists("dbinfo.php")) {
   die("Cannot find backup info file, restore aborted";
}   
extract($_GET);
if ($password=="") die ("Password is empty!");
include "dbinfo.php"; 
?>
<html>
<head>
<title>MySQL PHP Helper :: Restore</title>
<style type="text/css">
body { font-family: "verdana", sans-serif }
</style>
</head>

<body bgcolor="#f4f4f4" link="#000000" alink="#000000" vlink="#000000">

<center>
  <TABLE WIDTH="80%" border="0" cellspacing="0" bgcolor="#8BA5C5">
    <TR> 
      <TD valign="top"> <h4>MySQL PHP Backup :: Restore</h4>
      </TD>
    </TR>  

<?
if (!isset($file)) {
    echo '
    <TR> 
      <TD valign="top">
        Below is your backup file. Click the Restore link to restore this backup. 
        Note that by restoring your database you will overwrite its current entries 
        with the backup file. </TD>
    </TR> ';
} ?>    

    <TR> 
      <TD valign="top"> 
        <?
$path = $path . "dump/";
if ($file!="") {
      if (eregi("gz",$file)) { //zip file decompress first than show only
         @unlink($path."backup.sql");
         $fp2 = @fopen("dump/backup.sql","w");
         fwrite ($fp2,"");
	 fclose ($fp2);
         chmod($path."backup.sql", 0777);
         $fp = @fopen("dump/backup.sql","w");
         $zp = @gzopen("dump/$file", "rb");
         if(!$fp) {
              die("No sql file can be created"); 
         }    
         if(!$zp) {
              die("Cannot read zip file");
         }    
         while(!gzeof($zp)){
	      $data=gzgets($zp, 8192);// buffer php
	      fwrite($fp,$data);
         }
         fclose($fp);
         gzclose($zp);
         $file="backup.sql";
         echo " <br>File backup.sql extracted from $file. <br>";
         $file='';
      } // end of unzip
}
if ($file!=""){  
         
      flush();
      $conn = mysql_connect($dbhost,$dbuser,$password) or die(mysql_error());
	$filename = $file;
	set_time_limit(1000);
	$file=fread(fopen($path.$file, "r"), filesize($path.$file));
	$query=explode(";#%%\n",$file);
	for ($i=0;$i < count($query)-1;$i++) {
		mysql_db_query($dbname,$query[$i],$conn) or die(mysql_error());
	}
	echo "<table width=\"90%\"><tr><td align=\"center\"><b>Your restore 
request was processed.</b> If you did not receive any errors on the screen, then 
you should find that your database tables have been restored. If you attemped to restore your 
database using a backup file that was not created from this script, you likely encountered 
errors. This script can <b>only</b> restore backups made with this script.<br><br></td></tr></table>";
} 
?>
      </TD>
    </TR>
    <TR> 
      <TD valign="top"><table width="625" cellspacing="0">
          <tr> 
            <td width="125" align="center"><font size="2"><u><i>File</i></u></font></td>
            <td width="125" align="center"><font size="2"><u><i>Size</i></u></font></td>
            <td width="125" align="center"><font size="2"><u><i>Date</i></u></font></td>
            <td width="125"><font size="2">&nbsp;</font></td>
            <td width="125"><font size="2">&nbsp;</font></td>
          </tr>
          <?
	$dir=opendir($path); 
	$file = readdir($dir);
	while ($file = readdir ($dir)) { 
	    if ($file != "." && $file != ".." &&  (eregi("\.sql",$file) || eregi("\.gz",$file))){ 
	        if (eregi("\.sql",$file) ) {
	           echo "<tr><td nowrap bgcolor=\"#dddddd\" align=\"center\">$file</td>
	        	 <td nowrap bgcolor=\"#dddddd\" align=\"center\">".round(filesize($path.$file) / 1024, 2)." KB</td>
	        	 <td nowrap bgcolor=\"#dddddd\" align=\"center\">".date("d-m-Y",filemtime($path.$file))."</td>
	        	 <td nowrap align=\"center\"><a href=\"restore.php?file=$file\">Restore</a></td>
	        	 <td nowrap align=\"center\"><a href=\"dump/$file\">View</a></td></tr>"; 
	        } else {
	           echo "<tr><td nowrap bgcolor=\"#dddddd\" align=\"center\">$file</td>
	        	 <td nowrap bgcolor=\"#dddddd\" align=\"center\">".round(filesize($path.$file) / 1024, 2)." KB</td>
	        	 <td nowrap bgcolor=\"#dddddd\" align=\"center\">".date("d-m-Y",filemtime($path.$file))."</td>
	        	 <td nowrap align=\"center\"><a href=\"restore.php?file=$file\">Unzip</a></td>
	        	 <td></td></tr>"; 
               }
	    } 
	}
	closedir($dir);
    ?>
        </table></TD>
    </TR>
    <TR> 
      <TD height="20" valign="top"><p><br>
          <b><a href="index.php"><font color="#FFFFFF" size="1">Return to Main</font></a></b></p></TD>
    </TR>
    <TR>
      <TD height="15" valign="top" bgcolor="#FFFFFF"><div align="right"><font color="#9999CC" face="Arial, Helvetica, sans-serif" style="font-size:6Pt">MySql 
          Php Backup &copy; 2003 by <a href="http://www.absoft-my.com" target="_blank">AB 
          Webservices</a></font> </div></TD>
    </TR>
  </TABLE>
</center>
</body>
</html>
