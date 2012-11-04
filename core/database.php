<?php
/**
 * @package Mambo
 * @subpackage Database
 * @author Mambo Foundation Inc see README.php
 * @copyright Mambo Foundation Inc.
 * See COPYRIGHT.php for copyright notices and details.
 * @license GNU/GPL Version 2, see LICENSE.php
 * Mambo is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 */

/**
 * Database connector class
 */
class database {
    /** @var string Internal variable to hold the query sql */
    var $_sql='';
    /** @var int Internal variable to hold the database error number */
    var $_errorNum=0;
    /** @var string Internal variable to hold the database error message */
    var $_errorMsg='';
    /** @var string Internal variable to hold the prefix used on all database tables */
    var $_table_prefix='';
    /** @var Internal variable to hold the connector resource */
    var $_resource='';
    /** @var Internal variable to hold the last query cursor */
    var $_cursor=null;
    /** @var boolean Debug option */
    var $_debug=0;
    /** @var array A log of queries */
    var $_log=array();
    /** @var string Null date */
    var $_null_date='0000-00-00 00:00:00';
    /**
     * Database object constructor
     * @param string Database host
     * @param string Database user name
     * @param string Database user password
     * @param string Database name
     * @param string Common prefix for all tables
     * @param string Database charset
     */
    function database( $host, $user, $pass, $db, $table_prefix,$charset='' ) {
        // perform a number of fatality checks, then die gracefully
        if (!function_exists( 'mysql_connect' )) $this->forceOffline(1);
        if (!($this->_resource = @mysql_connect( $host, $user, $pass ))) $this->forceOffline(2);
        if (!mysql_select_db($db)) $this->forceOffline(3);
        $this->_table_prefix = $table_prefix;
        if(floatval(mysql_get_client_info())>=4.1) {
            $mysql_charsets = $this->getCharsets();
            $charset = in_array($charset, array_keys($mysql_charsets)) ? $charset : $this->getCharsetFromDb();
            $charset = in_array($charset, array_keys($mysql_charsets)) ? $charset : 'utf-8';
            $cs=$mysql_charsets[$charset];
            mysql_query( "SET CHARSET '" .$cs. "'" );
        }
    }

    function getCharsetFromDb() {
        static $charset_from_db;
        if (!isset($charset_from_db)) {
            //retrieve from database
            $query = 'show variables like "character_set_database";';
            $this->setQuery( $query );
            $results = $this->loadObjectList();
            $charset_from_db = $results[0]->Value;
        }
        return $charset_from_db;
    }

    function getCharsets() {
        static $mysql_charsets;
        if (!isset($mysql_charsets)) {
            $mysql_charsets = array();
            $mysql_charsets['utf-8']='utf8';
            $mysql_charsets['iso-8859-1']='latin1';
            $mysql_charsets['iso-8859-15']='latin1';
            $mysql_charsets['koi8-r']='koi8r';
            $mysql_charsets['windows-1251']='cp1251';
            $mysql_charsets['cp1251']='cp1251';
            $mysql_charsets['gb2312']='gb2312';
            $mysql_charsets['gb18030']='gb2312';
            $mysql_charsets['gbk']='gb2312';
            $mysql_charsets['big5-hkscs']='big5';
            $mysql_charsets['big5']='big5';
            $mysql_charsets['euc-tw']='gb2312';
            $mysql_charsets['iso-8859-2']='latin2';
            $mysql_charsets['windows-1250']='latin2';
            $mysql_charsets['iso-8859-7']='latin7';
            $mysql_charsets['iso-8859-8-i']='hebrew';
            $mysql_charsets['iso-8859-8']='hebrew';
            $mysql_charsets['sjis']='sjis';
            $mysql_charsets['windows-1257']='latin7';
            $mysql_charsets['iso-8859-13']='latin7';
            $mysql_charsets['cp-866']='cp1251';
            $mysql_charsets['iso-8859-5']='latin5';
            $mysql_charsets['koi8-u']='koi8r';
            $mysql_charsets['windows-1252']='latin1';
            $mysql_charsets['tis-620']='tis620';
            $mysql_charsets['iso-8859-9']='latin5';
            $mysql_charsets['windows-1256']='cp1256';
            $mysql_charsets['georgian-ps']='geostd8';
            $mysql_charsets['euc-jp']='eucjpms';
            $mysql_charsets['euc-kr']='euckr';
            $mysql_charsets['iso-8859-6']='cp1256';
            $mysql_charsets['windows-1258']='latin1'; //No better match
        }
        return $mysql_charsets;
    }

    function forceOffline ($error_number) {
        echo "no hay conexion con la base de datos";
        exit();
    }

    function getNullDate () {
        return $this->_null_date;
    }
    /**
     * @param int
     */
    function debug( $level ) {
        $this->_debug = intval( $level );
    }

    function debug_trace () {
        trigger_error( $this->_errorNum, E_USER_NOTICE );
        //echo "<pre>" . $this->_sql . "</pre>\n";
        if (function_exists('debug_backtrace')) {
            foreach(debug_backtrace() as $back) {
                if (@$back['file']) {
                    echo '<br />'.$back['file'].':'.$back['line'];
                }
            }
        }
    }
    /**
     * @return int The error number for the most recent query
     */
    function getErrorNum() {
        return $this->_errorNum;
    }
    /**
     * @return string The error message for the most recent query
     */
    function getErrorMsg() {
        return str_replace( array( "\n", "'" ), array( '\n', "\'" ), $this->_errorMsg );
    }
    /**
     * Get a database escaped string
     * @return string
     */
    function getEscaped( $text ) {
        if (phpversion() < '4.3.0') {
            return mysql_escape_string( $text );
        } else {
            return mysql_real_escape_string( $text );
        }
    }
    /**
     * Get a quoted database escaped string
     * @return string
     */
    function Quote( $text ) {
        if (phpversion() < '4.3.0') {
            return '\'' . mysql_escape_string( $text ) . '\'';
        } else {
            return '\'' . mysql_real_escape_string( $text ) . '\'';
        }
    }
    /**
     * Sets the SQL query string for later execution.
     *
     * @param string The SQL query
     */
    function setBareQuery ($sql) {
        $this->_sql = $sql;
    }
    /**
     * Sets the SQL query string for later execution.
     *
     * This function replaces a string identifier <var>$prefix</var> with the
     * string held is the <var>_table_prefix</var> class variable.
     *
     * @param string The SQL query
     * @param string The common table prefix
     */
    function setQuery( $sql, $prefix='#_' ) {
        $this->setBareQuery ($this->replacePrefix($sql, $prefix));
//      This is maintenance code for catching particular SQL statements
//		if (strpos($this->_sql,'SELECT menutype') === 0) debug_print_backtrace();
    }

    /**
     * This function replaces a string identifier <var>$prefix</var> with the
     * string held is the <var>_table_prefix</var> class variable.
     *
     * @param string The SQL query
     * @param string The common table prefix
     * @author thede, David McKinnis
     */
    function replacePrefix ($sql, $prefix='#_') {
        $done = '';
        while (strlen($sql)) {
            $single = preg_match("/\'([^\\\']|\\.)*'/", $sql,$matches_single,PREG_OFFSET_CAPTURE);
            if ($double = preg_match('/\"([^\\\"]|\\.)*"/', $sql,$matches_double,PREG_OFFSET_CAPTURE) OR $single) {
                if ($single == 0 OR ($double AND $matches_double[0][1] < $matches_single[0][1])) {
                    $done .= str_replace($prefix, $this->_table_prefix, substr($sql,0,$matches_double[0][1])).$matches_double[0][0];
                    $sql = substr($sql,$matches_double[0][1]+strlen($matches_double[0][0]));
                }
                else {
                    $done .= str_replace($prefix, $this->_table_prefix, substr($sql,0,$matches_single[0][1])).$matches_single[0][0];
                    $sql = substr($sql,$matches_single[0][1]+strlen($matches_single[0][0]));
                }
            }
            else return $done.str_replace($prefix, $this->_table_prefix,$sql);
        }
        return $done;
    }
    /**
     * @return string The current value of the internal SQL vairable
     */
    function getQuery($sql='') {
        if ($sql == '') $sql = $this->_sql;
        return "<pre>" . htmlspecialchars( $sql ) . "</pre>";
    }
    /**
     * Execute the query
     * @return mixed A database resource if successful, FALSE if not.
     */
    function query($sql = '') {
        global $mosConfig_debug;
        if ($sql == '') $sql = $this->_sql;
        if ($this->_debug) $this->_log[] = $sql;
        if ($this->_cursor = mysql_query($sql, $this->_resource)) {
            $this->_errorNum = 0;
            $this->_errorMsg = '';
            return $this->_cursor;
        }
        else {
            $this->_errorNum = mysql_errno( $this->_resource );
            $this->_errorMsg = mysql_error( $this->_resource )." SQL=$sql";
            if ($this->_debug) $this->debug_trace();
            return false;
        }
    }

    function query_batch( $abort_on_error=true, $p_transaction_safe = false) {
        $this->_errorNum = 0;
        $this->_errorMsg = '';
        if ($p_transaction_safe) {
            $si = mysql_get_server_info();
            preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
            $prefix = '';
            if ($m[1] >= 4) $prefix = 'START TRANSACTION; ';
            elseif ($m[2] >= 23) {
                if ($m[3] >= 19) $prefix = 'BEGIN WORK; ';
                elseif ($m[3] >= 17) $prefix = 'BEGIN; ';
            }
            if ($prefix) $this->_sql = $prefix.$this->_sql.'; COMMIT;';
        }
        $query_split = preg_split ("/[;]+/", $this->_sql);
        $error = 0;
        foreach ($query_split as $command_line) {
            $command_line = trim( $command_line );
            if ($command_line != '') {
                if (!$this->query($command_line)) {
                    $error = 1;
                    if ($abort_on_error) {
                        return $this->_cursor;
                    }
                }
            }
        }
        return $error ? false : true;
    }

    /**
     * Diagnostic function
     */
    function explain() {
        if (!($cur = $this->query("EXPLAIN ".$this->_sql))) return null;
        $headline = $header = $body = '';
        $buf = '<table cellspacing="1" cellpadding="2" border="0" bgcolor="#000000" align="center">';
        $buf .= $this->getQuery("EXPLAIN ".$this->_sql);
        while ($row = mysql_fetch_assoc($cur)) {
            $body .= "<tr>";
            foreach ($row as $k=>$v) {
                if ($headline == '') $header .= "<th bgcolor=\"#ffffff\">$k</th>";
                $body .= "<td bgcolor=\"#ffffff\">$v</td>";
            }
            $headline = $header;
            $body .= "</tr>";
        }
        $buf .= "<tr>$headline</tr>$body</table><br />&nbsp;";
        mysql_free_result( $cur );
        return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
    }
    /**
     * @return int The number of rows returned from the most recent query - SELECT only
     */
    function getNumRows( $cur=null ) {
        return mysql_num_rows( $cur ? $cur : $this->_cursor );
    }

    /**
     * @return int The number of rows affected by the most recent query - INSERT, UPDATE, DELETE
     */
    function getAffectedRows(  ) {
        return mysql_affected_rows( $this->_resource );
    }

    /**
     * Load an array of retrieved database objects or values
     * @param int Database cursor
     * @param string The field name of a primary key
     * @return array If <var>key</var> is empty as sequential list of returned records.
     * If <var>key</var> is not empty then the returned array is indexed by the value
     * the database key.  Returns <var>null</var> if the query fails.
     */
    private function &retrieveResults ($key='', $max=0, $result_type='row') {
        $results = array();
        $sql_function = 'mysql_fetch_'.$result_type;
        if ($cur = $this->query()) {
            while ($row = $sql_function($cur)) {
                if ($key != '') {
                    if ( is_array($row) ) {
                        $results[$row[$key]] = $row;
                    } else {
                        $results[$row->$key] = $row;
                    }
                } else {
                    $results[] = $row;
                }
                if ($max AND count($results) >= $max) break;
            }
            mysql_free_result($cur);
        }
        return $results;
    }
    /**
     * This method loads the first field of the first row returned by the query.
     *
     * @return The value returned in the query or null if the query failed.
     */
    function loadResult($sql="") {
        if($sql!="")
            $this->setQuery($sql);
        $results =& $this->retrieveResults('', 1, 'row');
        if (count($results)) return $results[0][0];
        else return null;
    }

    /**
     * Load an array of single field results into an array
     */
    private function loadResultArray($numinarray = 0) {
        $results =& $this->retrieveResults('', 0, 'row');
        $values = array();
        foreach ($results as $result) $values[] = $result[$numinarray];
        if (count($values)) return $values;
        else return null;
    }
    /**
     * Load a assoc list of database rows
     * @param string The field name of a primary key
     * @return array If <var>key</var> is empty as sequential list of returned records.
     */
    function loadAssocList($sql="", $key='' ) {
        if($sql!="")
            $this->setQuery($sql);
        $results =& $this->retrieveResults($key, 0, 'assoc');
        if (count($results)) return $results;
        else return null;
    }
    /**
     * Copy the named array content into the object as properties
     * only existing properties of object are filled. when undefined in hash, properties wont be deleted
     * @param array the input array
     * @param obj byref the object to fill of any class
     * @param string
     * @param boolean
     */
    private function mosBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
        if (!is_array($array) OR !is_object($obj)) return false;
        if ($prefix == null) $prefix = '';
        foreach (get_object_vars($obj) as $k => $v) {
            if( substr( $k, 0, 1 ) != '_' AND strpos($ignore, $k) === false) {
                if (isset($array[$prefix.$k])) {
                    $obj->$k = ($checkSlashes AND get_magic_quotes_gpc()) ? $this->mosStripslashes( $array[$prefix.$k] ) : $array[$prefix.$k];
                }
            }
        }
        return true;
    }

    /**
     * Strip slashes from strings or arrays of strings
     * @param value the input string or array
     */
    private function mosStripslashes(&$value) {
        if (is_string($value)) $ret = stripslashes($value);
        else {
            if (is_array($value)) {
                $ret = array();
                while (list($key,$val) = each($value)) {
                    $ret[$key] = $this->mosStripslashes($val);
                } // while
            } else $ret = $value;
        } // if
        return $ret;
    } // mosStripSlashes

    /**
     * This global function loads the first row of a query into an object
     *
     * If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
     * If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
     * @param string The SQL query
     * @param object The address of variable
     */
    private function loadObject( &$object ) {
        if ($object != null) {
            $results =& $this->retrieveResults('', 1, 'assoc');
            if (count($results)) {
                $this->mosBindArrayToObject($results[0], $object, null, null, false);
                return true;
            }
        }
        else {
            $results =& $this->retrieveResults('', 1, 'object');
            if (count($results)) {
                $object = $results[0];
                return true;
            }
            else $object = null;
        }
        return false;
    }
    /**
     * Load a list of database objects
     * @param string The field name of a primary key
     * @return array If <var>key</var> is empty as sequential list of returned records.
     * If <var>key</var> is not empty then the returned array is indexed by the value
     * the database key.  Returns <var>null</var> if the query fails.
     */
    function loadObjectList($sql="", $key='' ) {
        if($sql!="")
            $this->setQuery($sql);
        $results =& $this->retrieveResults($key, 0, 'object');
        if (count($results)) return $results;
        else return null;
    }
    /**
     * @return The first row of the query as assoc.
     */
    function loadAssocRow() {
        $results =& $this->retrieveResults('', 1, 'assoc');
        if (count($results)) return $results[0];
        else return null;
    }
    /**
     * @return The first row of the query as assoc list as object.
     */
    function loadObjectRow($sql=null) {
        if($sql)
            $this->setQuery($sql);
        $results =& $this->retrieveResults('', 1, 'object');
        if (count($results)) return $results[0];
        else return null;
    }
    /**
     * Load a list of database rows (numeric column indexing)
     * @param string The field name of a primary key
     * @return array If <var>key</var> is empty as sequential list of returned records.
     * If <var>key</var> is not empty then the returned array is indexed by the value
     * the database key.  Returns <var>null</var> if the query fails.
     */
    function loadRowList( $key='' ) {
        $results =& $this->retrieveResults('', 0, 'row');
        if (count($results)) return $results;
        else return null;
    }

    /**
     * @param boolean If TRUE, displays the last SQL statement sent to the database
     * @return string A standised error message
     */
    function stderr( $showSQL = false ) {
        return "DB function failed with error number $this->_errorNum"
                ."<br /><font color=\"red\">$this->_errorMsg</font>"
                .($showSQL ? "<br />SQL = <pre>$this->_sql</pre>" : '');
    }

    function insertid() {
        return mysql_insert_id();
    }

    function getVersion() {
        return mysql_get_server_info();
    }

    /**
     * Fudge method for ADOdb compatibility
     */
    function GenID( $foo1=null, $foo2=null ) {
        return '0';
    }
    /**
     * @return array A list of all the tables in the database
     */
    function getTableList() {
        $this->setQuery( 'SHOW tables' );
        $this->query();
        return $this->loadResultArray();
    }
    /**
     * @param array A list of table names
     * @return array A list the create SQL for the tables
     */
    function getTableCreate( $tables ) {
        $result = array();

        foreach ($tables as $tblval) {
            $this->setQuery( 'SHOW CREATE table ' . $tblval );
            $this->query();
            $result[$tblval] = $this->loadResultArray( 1 );
        }

        return $result;
    }
    /**
     * @param array A list of table names
     * @return array An array of fields by table
     */
    function getTableFields( $tables ) {
        $result = array();

        foreach ($tables as $tblval) {
            $this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
            $this->query();
            $fields = $this->loadObjectList();
            foreach ($fields as $field) {
                $result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
            }
        }

        return $result;
    }

    function displayLogged () {
        echo count($this->_log).' queries executed';
        echo '<pre>';
        foreach ($this->_log as $k=>$sql) {
            echo $k+1 . "\n" . $sql . '<hr />';
        }
    }

    /* Helper method - maybe should go into database itself */
    function doSQL ($sql) {
        $this->setQuery($sql);
        if (!$this->query()) {
            echo "<script> alert('".$this->getErrorMsg()."'); window.history.go(-1); </script>\n";
            exit();
        }
    }

    function insert($table,$values) {
        $this->setQuery( 'SHOW FIELDS FROM ' . $table );
        $this->query();
        $campos = $this->loadObjectList();
        

        $sql="insert into $table(".implode(",",array_keys($fields)).") values(";

        $sql=")";
    }
    function update($table,$fields,$criteria) {

    }
    function delete($table,$criteria) {

    }
}