<?php

include_once("../lib/ez_sql_core.php");
include_once("../lib/ez_sql_mysql.php");

$db = new ezSQL_mysql('puwwacom_admin','Jopa@gmail.com','puwwacom_game1','box586.bluehost.com');

if(isset($_POST['action'])){
    $output = '';
    switch ($_POST['action']){
        case 'showTables':  // Returns table names from DB
            $tables = $db->get_results("SHOW TABLES",ARRAY_N);

            if(is_null($tables)){

            }

            echo var_dump($tables);
            die();
            $output .= '<select class="tables"><option value="selectTable" selected="selected">Select table</option>';

            foreach($tables as $table)
            {
                $output .= '<option value="'.$table[0].'">' . $table[0] . '</option>';
            }

            $output .= '</select>';
            break;

        case 'showFields':
            $results = $db->get_results("SELECT * FROM ".$_POST['tableName']."");

            if($results)
            {
                $titles = '<tr class=""><td>ID</td><td>X</td><td>Y</td><td>Sky cell</td><td>Middle cell</td><td>Ground cell</td><td>Action</td></tr>';

                $output .= '<table class="rows">'.$titles;

                foreach( $results as $result )
                {
                    $output .= '<tr data-row_id="'.$result->ID.'">';
                    $output .= '<td>' . $result->ID . '</td>';
                    $output .= '<td>' . $result->x_coor . '</td>';
                    $output .= '<td>' . $result->y_coor . '</td>';
                    $output .= '<td><input type="text" class="sky_cell" value="' . $result->sky_cell . '" /></td>';
                    $output .= '<td><input type="text" class="middle_cell" value="' . $result->middle_cell . '" /></td>';
                    $output .= '<td><input type="text" class="ground_cell" value="' . $result->ground_cell . '" /></td>';
                    $output .= '<td><input name="updateCell" type="submit" value="Update" data-id="'.$result->ID.'" /></td>';
                    $output .= '</tr>';
                }

                $output .= '</table>';
            }
            break;

        case 'updateCellData':

            $data_len = count($_POST['dataArr']);
            $count = 0;
            $data_string = '';
            foreach( $_POST['dataArr'] as $key=>$val){
                $count++;
                $comma = ($count < $data_len) ? "," : "";
                $data_string .= $key . " = " . $val .$comma;
            }

            $result = $db->query("UPDATE ct_cells SET " . $data_string . " WHERE id = " . $_POST['cellID'] . "");

            echo $result;
            break;

        case 'createCells':
            break;
        default:
            $output .= '<ul>';
            $output .= '<li>createDB: N/A</li>';
            $output .= '<li>createTable: N/A</li>';
            $output .= '<li>showTables: DBname</li>';
            $output .= '<li>showFields: tableName</li>';
            $output .= '</ul>';
            break;
    }
    echo $output;
}

