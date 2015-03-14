<?php

class Backup{

// backup the db function
function backup_database_tables($host, $user, $pass, $name, $tables) {
    $return = "";
    $link = mysql_connect($host, $user, $pass);
    mysql_select_db($name, $link);

    //get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while ($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }

//    //cycle through each table and format the data
    foreach ($tables as $table) {
        $result = mysql_query('SELECT * FROM ' . $table);
        $num_fields = mysql_num_fields($result);

        $listaColunasTabela = "";
        $colunasTabela = mysql_query("SHOW COLUMNS FROM ".$table);
        if (mysql_num_rows($colunasTabela) > 0) {
            while ($row = mysql_fetch_assoc($colunasTabela)) {
                $listaColunasTabela .= "`".$row['Field']."`,";
            }
            $listaColunasTabela  = rtrim($listaColunasTabela, ", ");
        }
        

        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
        $tabela = preg_replace("#CREATE TABLE#", "CREATE TABLE IF NOT EXISTS", $row2[1]);
        $return.= "\n\n" . $tabela . ";\n\n";
          
        for ($i = 0; $i < $num_fields; $i++) {
             $return .="INSERT `".$tabela. "`INTO (".$listaColunasTabela.") VALUES ";
            while ($row = mysql_fetch_row($result)) {
                $return .="(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace("#\n#", "\\n", $row[$j]);
                    
                    if (isset($row[$j])) {
                        $return.= '"' . $row[$j] . '"';
                    } else {
                        $return.= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return.= ',';
                    }
                }
                $return.= "),\n";
            }
        }
        $return.="\n\n\n";
    }
    
//
//    //save the file
//    $handle = fopen('db-backup-' . time() . '-' . (md5(implode(',', $tables))) . '.sql', 'w+');
//    fwrite($handle, $return);
//    fclose($handle);
       return $return;
       //return $insert;
}
}