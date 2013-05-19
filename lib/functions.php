<?php
    /* functions.php 
    * 
    *   Site's help functions v8.3.11 
    * 
    */

    // Alerts '$str'
    function alert($str = 'Some alert'){
        echo '<script>';
        echo 'alert("'.$str.'");';
        echo '</script>';    
    }

    // Makes reading the output of print_r() in a browser infinitely easier
    function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
    }

    // sort 2d array - http://il2.php.net/manual/en/function.asort.php#74725
    function recordSort($records, $field, $reverse = 0, $defaultSortField = 0){
        if(count($records) != 0){
            $uniqueSortId = 0;
            $hash = array(); $sortedRecords = array(); $tempArr = array(); $indexedArray = array(); $recordArray = array();

            foreach($records as $record) {
                $uniqueSortId++;
                $recordStr = implode("|", $record)."|".$uniqueSortId;
                $recordArray[] = explode("|", $recordStr);
            }

            $primarySortIndex = count($record);
            $records = $recordArray;

            foreach($records as $record) {
                $hash[$record[$primarySortIndex]] = $record[$field];
            }
            uasort($hash, "strnatcasecmp");
            if($reverse)
                $hash = array_reverse($hash, true);

            $valueCount = array_count_values($hash);

            foreach($hash as $primaryKey => $value) {
                $indexedArray[] = $primaryKey;
            }         

            $i = 0;
            foreach($hash as $primaryKey => $value) {
                $i++;
                if($valueCount[$value] > 1) {
                    foreach($records as $record)  {
                        if($primaryKey == $record[$primarySortIndex]) {
                            $tempArr[$record[$defaultSortField]."__".$i] = $record;
                            break;
                        }
                    }

                    $index = array_search($primaryKey, $indexedArray);

                    if( ($i == count($records)) || ($value != $hash[$indexedArray[$index+1]]) )  {
                        uksort($tempArr, "strnatcasecmp");

                        if($reverse)
                            $tempArr = array_reverse($tempArr);

                        foreach($tempArr as $newRecs) {
                            $sortedRecords [] = $newRecs;
                        }

                        $tempArr = array();
                    }
                }
                else {
                    foreach($records as $record)  {
                        if($primaryKey == $record[$primarySortIndex])  {
                            $sortedRecords[] = $record;
                            break;
                        }
                    }
                }
            }
            return $sortedRecords;
        }
        return array();
    }

    /* Reloads page, 
    * 
    *  redirect.php :
    *       echo '<script>';
    echo 'window.location="index.php"';
    echo '</script>';
    * 
    */
    function pageReload(){
        echo '<script>';
        echo 'window.location="redirect.php?page='.$_GET['page'].'";';
        echo '</script>';
    }

    function pageReloadURL($url){
        
        echo '<script>';
        echo 'window.location="redirectedit.php?' . $url . '"';
        echo '</script>';
    }
    // Break line
    function br($times = 1){
        for($i=0; $i<$times; $i++)
            echo "<br>";    
    }

    // Prints colorful backtrace
    function here(){
        array_walk( debug_backtrace(), create_function( '$a,$b', 'print "<br /><b>". basename( $a[\'file\'] ). "</b> &nbsp; <font color=\"red\">{$a[\'line\']}</font> &nbsp; <font color=\"green\">{$a[\'function\']} ()</font> &nbsp; -- ". dirname( $a[\'file\'] ). "/";' ) );
        echo "<br />";
    }  
    
    //Время, прошедшее с запуска ОС в мс
function microtime_n()
    {
        $start_array = explode(" ",microtime());
        $start_time = $start_array[1] + $start_array[0];
        return $start_time;
    }
?>