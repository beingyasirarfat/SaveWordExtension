<?php

namespace App;

//use Illuminate\Support\Env;

class Environment
{
        static $Environment;
        static $APP_NAME;
        static $APP_ENV;
        static $APP_KEY;
        static $APP_DEBUG;
        static $APP_URL;

        static $DB_CONNECTION;
        static $DB_HOST;
        static $DB_PORT;
        static $DB_DATABASE;
        static $DB_USERNAME;
        static $DB_PASSWORD;


        private static function parseFile($file)
        {
                $Variables = [];

                if (!file_exists($file)) return false;

                $lines = file($file);
                // Output one line until end-of-file
                foreach ($lines as $line) {

                        $line = ltrim($line);

                        if (trim($line) === '' || (isset($line[0]) && $line[0] == '#')) {
                                continue;
                        }
                        $Variables[] = $line;
                }

                return $Variables;
        }

        public static function setEnvironment($file)
        {
                $Variables = Environment::parseFile($file);
                if (!$Variables) {
                        return false;
                }

                $assosiative = [];

                foreach ($Variables as $variable) {
                        $arr = explode('=', $variable, 2);
                        $assosiative[trim($arr[0])] = trim($arr[1]);
                }
                foreach ($assosiative as $key => $value) {
                        self::${trim($key)} = trim($value);
                }
                self::$Environment = true;
        }
}

Environment::setEnvironment('.env');

function env(string $var, $default = null)
{
        if (!isset(Environment::$Environment)) return "Environment is not Set Properly";

        switch ($var) {
                case 'name':
                        return Environment::$APP_NAME ? Environment::$APP_NAME : $default ? $default : "YasirArfat.com";
                case 'environment':
                        $dev = Environment::$APP_ENV == 'dev' || Environment::$APP_ENV == 'development' || Environment::$APP_ENV == 'local';
                        return $dev ? 'development' : $default ? $default : 'production';
                case 'key':
                        return Environment::$APP_KEY ? Environment::$APP_KEY : $default ? $default : '';
                case 'debugging':

                        $de = Environment::$APP_DEBUG;

                        $debug = $de == true || $de == 'debug' || $de == 'Debug' || $de == 'DEBUG';

                        return  $debug ? Environment::$APP_DEBUG : $default ? $default : false;

                case 'url':
                        return Environment::$APP_URL ? Environment::$APP_URL : $default ? $default : 'http://localhost/';
                default:
                        return $default;
        }
}

function base_url($url = "")
{
        $base = Environment::$APP_URL ? Environment::$APP_URL : "http://localhost/";
        return $base . $url;
}
