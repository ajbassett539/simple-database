<?php
    ////////////////////////////////////////////////////////////////////////////
    // Database\DB
    ////////////////////////////////////////////////////////////////////////////

    namespace Database;
    
    class DB {
        private static $objectQueries = Array("SELECT","SHOW","DESCRIBE","EXPLAIN");
        private $db;
        private $readQuery;
        private $lastError;
        private $sql;
        
        ////////////////////////////////////////////////////////////////////////
        public function __construct() {
            if( class_exists('mysqli') ) {
                $this->db = new \mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                if( $this->db->connect_errno ) {
                    throw new \Exception("Connection Error: " . $this->db->connect_error, 1);
                }
            } else {
                throw new \Exception("mysqli class not found. Please install mysql extension for PHP", 1);
            }
        }

        ////////////////////////////////////////////////////////////////////////
        public function query($sql = "", $multirow = false, $raw = false) {
            $this->sql = $sql;
            $pieces = explode(' ', $sql); $type = strtoupper($pieces[0]); unset($pieces); 
            $this->readQuery = (in_array($type, self::$objectQueries) ? true : false);
            if( ($this->readQuery && !($res = $this->db->query($sql))) || (!$this->readQuery && !($res = $this->db->query($sql))) ) {
                $this->log("Query Failed");
                return false;
            } else {
                if( $raw ) { $this->log(); return $res; }
                if( $this->readQuery && $res->num_rows ) {
                    if( $multirow ) {
                        $set = Array();
                        while( $row = $res->fetch_object() ) { $set[] = $row; } unset($row);
                        $this->log("OK, ". count($set) . " results returned");
                        return $set;
                    } else {
                        $single = $res->fetch_object();
                        $this->log("OK, result returned");
                        return $single;
                    }
                } else {
                    switch( $type ) {
                        case "INSERT":    $this->log("OK, insert_id ". $this->db->insert_id); return $this->db->insert_id;
                        case "UPDATE":    $this->log("OK, affected_rows ". $this->db->affected_rows); return $this->db->affected_rows;
                        case "DELETE":    $this->log("OK, executed"); return true;
                        case "SELECT":    $this->log("Empty Result"); return false;
                        default:        $this->log("OK"); return true;
                    }
                }
            }
        }
        
        ////////////////////////////////////////////////////////////////////////
        public static function execute($sql = "", $multirow = false, $raw = false) {
            $db = new self();
            return $db->query($sql, $multirow, $raw);
        }

        ////////////////////////////////////////////////////////////////////////
        public static function real_escape($string) {
            $db = new self();
            return $db->escape($string);
        }

        ////////////////////////////////////////////////////////////////////////
        public function escape($string) {
            return $this->db->escape_string($string);
        }

        ////////////////////////////////////////////////////////////////////////
        private function log($msg = "") {
            if( getenv('LOG_SQL_QUERIES') == true || $this->error() ) {
                error_log(
                    "[".($this->error() ? "ERROR" : "INFO")."] " . 
                    __CLASS__ . " " . 
                    ($msg ? " [\"$msg\"] " : " ") . 
                    ($this->error() ? "ERROR:(\"" . $this->error() ."\") "  : "") . 
                    "\"" . stripslashes(substr($this->sql, 0, 1024)) . "\" "
                );
            }
            if( $this->error() ) { 
                trigger_error($this->error() . "\n{$this->sql}", E_USER_WARNING);
                $this->db->error = null;
            }
        }

        ////////////////////////////////////////////////////////////////////////
        public function error() {
            $this->lastError = $this->db->error;
            return $this->db->error;
        }

        ////////////////////////////////////////////////////////////////////////
        public function getLastError() {
            return $this->lastError;
        }

        ////////////////////////////////////////////////////////////////////////
        public function alnum($value) {
            return preg_replace("/[^a-zA-Z0-9]+/", "", $value);
        }

        ////////////////////////////////////////////////////////////////////////
        public function affected_rows() {
            return $this->db->affected_rows;
        }

        ////////////////////////////////////////////////////////////////////////
        public function insert_id() {
            return $this->db->insert_id;
        }

    }
