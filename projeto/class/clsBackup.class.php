<?php

namespace classBackup;

require_once 'class/clsBD.class.php';

class backupBD extends \BD{

//    private $tables;
    private $backupFolder = "backups";
    private $arrayDataBase;

//    public function __construct($setArrayDataBase, $tables = "*") {
    public function __construct($setArrayDataBase) {
        $this->setArrayDataBase($setArrayDataBase);
    }

//    private function setTables($tables) {
//        if (is_array($tables)) {
//            $this->tables = $tables;
//        } else {
//            $tabela[] = $tables;
//            $this->tables = $tabela;
//        }
//    }

    private function setArrayDataBase($arrayDataBase) {
        if (is_array($arrayDataBase)) {
            $this->arrayDataBase = $arrayDataBase;
        } else {
            throw new RuntimeException('O dados informados não são um array');
        }
    }

    private function getTablesList() {
        $queryTableList = mysql_query('SHOW TABLES');
        while ($tabelas = mysql_fetch_row($queryTableList)) {
            $arrayTables[] = $tabelas[0];
        }
        return $arrayTables;
    }

    private function getColunsPerTable() {
        $arrayTabela = $this->getTablesList();
        $numbOfTables = sizeof($arrayTabela);
        for ($a = 0; $numbOfTables > $a; $a++) {
            $columnsList = "";
            $queryColumnsList = mysql_query("SHOW COLUMNS FROM " . $arrayTabela[$a]);
            while ($colunas = mysql_fetch_row($queryColumnsList)) {
                $columnsList[] = $colunas[0];
            }
            $arrayTableColumn[$arrayTabela[$a]] = $columnsList;
        }
        return $arrayTableColumn;
    }

    private function getTableConstruct($tabela) {
        $arrayCreateTable = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $tabela));
        $createTable = preg_replace("#CREATE TABLE#", "CREATE TABLE IF NOT EXISTS", $arrayCreateTable[1]) . ";";
        return $createTable;
    }

    private function getAllLinesPerTable() {
        $arrayTabela = $this->getTablesList();
        $numbOfTables = sizeof($arrayTabela);

        for ($c = 0; $numbOfTables > $c; $c++) {
            $queryAllLinesTable = mysql_query('SELECT * FROM ' . $arrayTabela[$c]);
            $AllLinesTable = mysql_fetch_assoc($queryAllLinesTable);

            while ($AllLinesTable = mysql_fetch_assoc($queryAllLinesTable)) {
                $linesTable[$arrayTabela[$c]][] = $AllLinesTable;
            }

            $numbOfLines = sizeof($linesTable[$arrayTabela[$c]]);
            for ($a = 0; $numbOfLines > $a; $a++) {
                $fields = array_keys($linesTable[$arrayTabela[$c]][$a]);
                $numbOfFields = sizeof($fields);
                for ($b = 0; $numbOfFields > $b; $b++) {
                    $LineTable[$arrayTabela[$c]][$fields[$b]][] = preg_replace("#'#", "''", $linesTable[$arrayTabela[$c]][$a][$fields[$b]]);          
                }
            }
        }

        return $LineTable;
    }

    public function gerateSQL() {
        $tableData = $this->getAllLinesPerTable();
        $arrayTables = array_keys($tableData);
        $numbOfTables = sizeof($arrayTables);
        $sql = "";
        for ($a = 0; $numbOfTables > $a; $a++) {
            $arrayTableFieds = array_keys($tableData[$arrayTables[$a]]);
            $numbOfTablesFields = sizeof($arrayTableFieds);
            $numbOfLines = sizeof($tableData[$arrayTables[$a]][$arrayTableFieds[0]]);
            $lineGroup = "";
            $tableFields = "";
            for ($b = 0; $numbOfTablesFields > $b; $b++) {
                $tableFields .= "`" . $arrayTableFieds[$b] . "`,";
            }

            for ($d = 0; $numbOfLines > $d; $d++) {
                $line = "";
                for ($c = 0; $numbOfTablesFields > $c; $c++) {
                    $line.= "'" . $tableData[$arrayTables[$a]][$arrayTableFieds[$c]][$d] . "',";
                }
                if (($numbOfLines - 1) == $d) {
                    $lineGroup .= "(" . rtrim($line, ",") . ")";
                } else {
                    $lineGroup .= "(" . rtrim($line, ",") . "),\n";
                }
            }
            $sql .= "--\n-- Estrutura da tabela $arrayTables[$a]\n--\n\n\n"
                    . $this->getTableConstruct($arrayTables[$a]) .
                    "\n\n\n--\n-- Extraindo dados da tabela $arrayTables[$a] \n--\n" .
                    "\n\nINSERT INTO `" . $arrayTables[$a] . "` (" . rtrim($tableFields, ", ") . ") VALUES \n" . rtrim($lineGroup, ",") . "\n\n\n";
        }
        return $sql;
    }

    public function gerateFolderBackup($dbName) {
        if (!file_exists($this->backupFolder)) {
            mkdir($this->backupFolder, 0777, true);
        }
        if ((!file_exists($this->backupFolder . "/" . $dbName))) {
            mkdir($this->backupFolder . "/" . $dbName, 0777, true);
        }
    }

    public function gerateBackup() {
        $arrayDataBase = $this->arrayDataBase;
        $numbOfDB = sizeof($arrayDataBase);
        $dataBasesName = array_keys($arrayDataBase);

       for ($a = 0; $numbOfDB > $a; $a++) {
            $dbHost = $arrayDataBase[$dataBasesName[$a]]['dbHost'];
            $dbUser = $arrayDataBase[$dataBasesName[$a]]['dbUser'];
            $dbPass = $arrayDataBase[$dataBasesName[$a]]['dbPass'];
            $dbName = $arrayDataBase[$dataBasesName[$a]]['dbName'];
            \BD::openConection($dbHost, $dbUser, $dbPass, $dbName);
            $this->gerateFolderBackup($dbName);

            $sql = $this->gerateSQL();
            $fileName = 'backups/' . $dbName . '/backup' . date("_Y-m-d_H-i-s") . '.sql';
            $handle = fopen($fileName, 'w+');
            fwrite($handle, $sql);
            fclose($handle);

            \BD::closeConection();
        }


        return $fileName;
       //return $dbName;
    }

}
