<?php
/*

    *** MySQL functions ***

    Vitaly Shot: 16.5.2011:19.18

*/

class SQL
{

    static $_host = "";             //Host name
    static $_user = "";             //User name
    static $_pass = "";             //Password
    static $_DB_name = 0;           //DB name
    static $_CID = 0;               //Global Connection ID 
    static $_working_time = 0;      //Whole queries time
    static $_query_counter = 0;     //Queries counter
    static $_queries_arr;           //Queries array
    static $_debug = false;

    /*
    *  Outputs debug information
    */
    static function Debug( ){
        if(self::$_debug)
            echo '<pre>'.print_r(debug_backtrace(), true).'</pre>';
    }

    /* 
    *  If Connect = true, immediately connect DB
    *  - $config - connection information array
    *  - $connect - connects to DB if TRUE
    */ 
    static function Set($config, $connect = true, $str = 'str')
    {
        self::Debug( );
//        if(self::$_debug){
//            echo 'SQL::Set( $config, $connect ) - executed. <br/>$config: ';
//            echo '<pre>'.print_r($config, true).'</pre>';
//            echo '<br/>$connect: '.$connect.'<br/>';
//        }

        if (self::$_CID > 0) mysql_close(self::$_CID);      // Close old connection if exists
        
        self::$_host = $config['db']['host'];
        self::$_user = $config['db']['user'];
        self::$_pass = $config['db']['pass'];
        self::$_DB_name = $config['db']['name'];
        self::$_CID  = 0;
        self::$_working_time = 0;
        self::$_query_counter = 0;
        self::$_queries_arr = array();
        
        if ($connect) self::Connect();
        
        return true;
    }
    
    /*
    *  DB connection function - returns 'true' if everything successeful
    */ 
    static function Connect() 
    {
        self::Debug();

        if (self::$_CID > 0) return true;   // If already connected
        
        self::$_CID = mysql_connect (self::$_host, self::$_user, self::$_pass) or self::Error("Can't connect DB.");     // Connect DB or print error
        
        if (!mysql_query("SET NAMES utf8", self::$_CID)) self::Error("Error set collation.");                           // Set UTF8 or print error
            
        if (!mysql_select_db(self::$_DB_name, self::$_CID)) self::Error("Can't set DB ".self::$_DB_name.".");           // Select DB or print error
         
        return true;
    }     
  
    /*
    *  Prints error
    *  - $message - message to print
    *  - $exit - exists program if TRUE
    */
    static function Error($message="", $exit = true) 
    {
        if(self::$_debug) echo 'SQL::Error() - executed<br/>';
        if ($exit) {
            exit(htmlspecialchars($message). " Error: \n". mysql_error());
        } 
        else {
            echo htmlspecialchars($message). " Error: <br />". mysql_error();
        }    
    }
       
    /*
    *  DB Query 
    *  - $query - query string
    *  - $need_fetch_array - fetch result to array if TRUE
    *  - $ID_array -
    *  - $one_ record - 
    */
    static function Query($query = "", $need_fetch_array = true, $ID_array = false, $one_record = false) 
    {
        if(self::$_debug) echo 'SQL::Query( "'.$query.'" ) - executed<br/>';
        if (!$query) return false;
        if (!self::$_CID) self::Connect();
        
        $start = microtime_n();
        
        $result = mysql_query($query, self::$_CID);
        
        $time = microtime_n() - $start;
        
        // Statistic record
        self::$_query_counter++;
        self::$_queries_arr[] = array($query, $time);
        self::$_working_time += $time;
        
        if (!$result) {
            self::Error("Query error1: ".$query."<br />");
            return false;
        }

//        echo '<pre>' . print_r($result, true) . '</pre>';
        
        if (!$need_fetch_array) return $result;
        
        if (@mysql_num_rows($result) > 0) {
            $ar = mysql_fetch_array($result);        
            do {
                if (!$ID_array) {
                    $res[] = $ar;    
                } 
                else {
                    $res[$ar[$ID_array]] = $ar;    
                }
            }
            
            while ($ar = mysql_fetch_array($result));
            if ($one_record) return $res[0]; else return $res;
        } else{
            echo 'Response: empty.'."<br/>";
            return false;
        }

    }



    /*
    *  Inserts record
    * - $table - table to insert to
    * - $array - array of 'key' => 'value', 'key' - table field name, 'value' - value to insert to field
    * - $return_id - returns ID of new record if true
    */
    static function Insert($table, $array, $return_id = false)
    {
        if (!isset($array) or empty($array)) return false;
        if (!isset($table) or empty($table)) return false;
    
        $c = 0;
        $keys = "";
        $values = "";
        foreach ($array as $key => $val) 
        {
            $c++;
            $keys .= "`".$key."`";
            if (!get_magic_quotes_gpc()) { $val = addslashes(trim($val)); }     // If magic quotes didn't set in php.ini - set them here
            $values .= "'".$val."'";
            if ($c < count($array)) {$keys .= ","; $values .= ",";}             // Add ',' if not last value
        }
        
        if (self::Query("INSERT INTO `".$table."` (".$keys.") VALUES(".$values.")", false)) {
             if ($return_id) {
                 return mysql_insert_id(self::$_CID);
             }
            return true;
        } else {
            self::Error("Query error2: ".$query."<br />");
        }
        return false;
    }
    
    /*
    *  Replace record
    * - $table - table to replace in
    * - $array - array of 'key' => 'value', 'key' - table field name, 'value' - value to replace
    */
    static function Replace($table, $array)
    {
        if (!isset($array) or empty($array)) return false;
        if (!isset($table) or empty($table)) return false;
    
        $c = 0;
        $keys = "";
        $values = "";
        foreach ($array as $key=>$val)
        {
            $c++;
            $keys .= "`".$key."`";
            if (!get_magic_quotes_gpc()) { $val = addslashes(trim($val)); }
            $values .= "'".$val."'";
            if ($c < count($array)) {$keys .= ","; $values .= ",";}
        }
        
        if (self::Query("REPLACE INTO `".$table."` (".$keys.") VALUES(".$values.")", false))
        {
            return true;
        }
        else
        {
            self::Error("Query error3: ".$query."<br />");
        }
        return false;
    }    

    /*
     * Updates row data in table
     * $table_name - Table name to update in.
     * $record_id - Record ID to update.
     * $data_arr - Pairs $field=>$value of data to update.
     * $id_field_name - ID field name, default = "ID"
     */
    static function update_record( $table_name, $data_arr, $record_id, $id_field_name="ID" ){
        if ( !isset( $table_name ) or empty( $table_name ) ) {
            SQL::Error('Can\'t update record_id $record_id in table $table, table name empty or not set');
        }
        if ( !isset( $data_arr ) or empty( $data_arr ) ) {
            SQL::Error('Can\'t update record_id $record_id in table $table, data_arr empty or not set');
        }

        $count = 0;
        $keys = "";
        foreach ( $data_arr as $key=>$val ) {
            $count++;
            if ( !get_magic_quotes_gpc() ) { $val = addslashes(trim($val)); }
            $keys .= "`".$key."`='".$val."'";
            if ( $count < count($data_arr) ) { $keys .= ","; }
        }
        if ( self::Query("UPDATE `{$table_name}` SET {$keys} WHERE `{$id_field_name}` = {$record_id}" )) {
//            UPDATE  `creatrio`.`ct_cells` SET  `sky_cell` =  '1',`middle_cell` =  '2',`ground_cell` =  '3' WHERE  `ct_cells`.`ID` =12 LIMIT 1 ;
            return true;
        } else {
            self::Error("Query error4: <br/>");
        }
    }

    /*
    *  Updates record
    * - $table - table to update in
    * - $array - array of 'key' => 'value', 'key' - table field name, 'value' - value to update
    * - $where - if it is a number, update record $where_field with this number, otherwise update record without $where
    * - $where_field - field to search $where in
    */
    static function Update($table, $array, $where = false, $where_field = "id")
    {
        if (!isset($array) or empty($array)) return false;
        if (!isset($table) or empty($table)) return false;
        
        if (is_int($where))   // If $where is number (integer not a string)
        { 
            $where = "WHERE `{$where_field}`='{$where}'";                                   // Что это на хер за `{$where_field}`
        } 
        else
        { 
            $where="WHERE " . $where; 
        } 
                
        $c = 0;
        $keys = "";
        foreach ($array as $key=>$val)
        {
            $c++;
            if (!get_magic_quotes_gpc()) { $val = addslashes(trim($val)); }
            $keys .= "`".$key."`='".$val."'";
            if ($c < count($array)) {$keys .= ",";}
        }
        if (self::Query("UPDATE `".$table."` SET ".$keys." ".$where, false))
        {
            return true;
        } else
        {
            self::Error("Query error5: ".$query."<br />");
        }
    }
    
    /*
    *  Deletes record
    * - $table - table to delete in
    * - $where - if it is a number, delete record $where_field whith this number, otherwise delete record whithout $where
    * - $where_field - field to search $where in
    */
    static function Delete($table, $where = false, $where_field = "id")
    {
        if (is_int($where)) 
        { 
            $where="WHERE `{$where_field}`='{$where}'"; 
        } 
        else
        { 
            $where="WHERE " . $where; 
        } 
        
        if (self::Query("DELETE FROM `".$table."` ".$where, false))
        {
            return true;
        } 
        else
        {
            self::Error("Query error6: ".$query."<br />");
        }
    }
 
 
// Еще не проверенные не понятные для чего функции   
    //Создаёт ассоциированный массив по запросу, используя поле $key для ключей и поле $value для значений
    static function GetArray($SQL, $key="id", $value="name", $array_val = false)
    {
        $res = array();
        if ($result = self::Query($SQL, false))
            if (@mysql_num_rows($result) > 0)
            {    
                $ar = mysql_fetch_array($result);        
                do
                {    
                    if ($array_val) 
                    { 
                        if (!isset($res[$ar[$key]])) $res[$ar[$key]] = array($ar[$value]); else    $res[$ar[$key]][] = $ar[$value];
                    } else
                    {
                        $res[$ar[$key]] = $ar[$value];    
                    }
                }
                while ($ar = mysql_fetch_array($result));        
            }
        return $res;
    }

}

?>