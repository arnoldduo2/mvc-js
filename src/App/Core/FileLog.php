<?php

declare(strict_types=1);

//Multiline error log class

// ersin güvenç 2008 eguvenc@gmail.com

//For break use "\n" instead '\n'

namespace App\App\Core;

class FileLog
{

   //

   public const USER_ERROR_DIR = '/home/site/error_log/Site_User_errors.log';

   public const GENERAL_ERROR_DIR = '/home/site/error_log/Site_General_errors.log';

   public function __construct(
      public string $general_dir = self::GENERAL_ERROR_DIR,
      public string $user_dir = self::USER_ERROR_DIR
   ) {
      $this->general_dir = $general_dir;
      $this->user_dir = $user_dir;
   }


   /**
    * Logger for user errors
    * @param mixed $msg
    * @param mixed $username
    * @return void
    */
   public function user(string $msg, string $username): void
   {
      $date = date('d.m.Y h:i:s');
      $log = "$msg   |  Date:  $date  |  User:  $username\n";
      error_log($log, 3, $this->user_dir);
   }

   /**
    * Logger for general errors
    * @param mixed $msg 
    * @return void
    */
   public function general($msg): void
   {
      $date = date('d.m.Y h:i:s');
      $log = "$msg   |  Date:  $date\n";
      error_log("$log   |  Tarih:  $date", 3, $this->general_dir);
   }
}

// $log = new log();

// $log->user($msg, $username); //use for user errors

//$log->general($msg); //use for general errors