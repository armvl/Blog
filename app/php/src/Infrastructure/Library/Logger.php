<?php

namespace App\Infrastructure\Library;

class Logger extends \Idearia\Logger {

  public static $log_level = 'debug';
  public static $print_log = false;
  public static $write_log = true;
  public static $log_dir = '/app/log';
  public static $log_file_name = 'php';
  public static $log_file_extension = 'log';

  public function __construct (string $logDir = '') {
    if ($logDir)
      static::$log_dir = $logDir;
  }

  public static function dbg ($msg, $name = '') {
    self::debug($msg, $name);
  }

  public static function inf ($msg, $name = '') {
    self::info($msg, $name);
  }

  public static function err ($msg, $name = '') {
    self::error($msg, $name);
  }
}