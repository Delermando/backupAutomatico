<?php

session_start();
ini_set('display_errors', 'off');
//error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');


require_once 'class/clsBackup.class.php';
$arrayDatabase = array(
    'db01' => array(
        'dbHost'  => '186.202.13.12',
        'dbUser'=> 'pcvideo',
        'dbPass'  => 'P2S79SF2',
        'dbName'=> 'pcvideo'
    ),
    'db02' => array(
        'dbHost'  => '186.202.152.103',
        'dbUser'=> 'pcvideo1', 
        'dbPass'  => 'C6GZXIZ7',
        'dbName'=> 'pcvideo1'
    ),
    'db03' => array(
        'dbHost'  => '186.202.152.10',
        'dbUser'=> 'pcvideo2',
        'dbPass'  => 'F7DWY9BM',
        'dbName'=> 'pcvideo2'
    ),
    'db04' => array(
        'dbHost'  => '186.202.152.10',
        'dbUser'=> 'pcvideo3',
        'dbPass'  => 'P3A23XEE',
        'dbName'=> 'pcvideo3'
    ),
    'db05' => array(
        'dbHost'  => '186.202.152.33',
        'dbUser'=> 'pcvideo4',
        'dbPass'  => 'J7MKN3BG',
        'dbName'=> 'pcvideo4'
    ),
    'db06' => array(
        'dbHost'  => '186.202.152.65',
        'dbUser'=> 'pcvideo5',
        'dbPass'  => 'L6BJJWP3',
        'dbName'=> 'pcvideo5'
    ),
    'db07' => array(
        'dbHost'  => '186.202.152.127',
        'dbUser'=> 'pcvideo7',
        'dbPass'  => 'V92L8F45',
        'dbName'=> 'pcvideo7'
    )
);
            
$backup = new \classBackup\backupBD($arrayDatabase);
var_dump($backup->gerateBackup());       
    
