$(document).ready(function(){

//    // Setup default ajax request
//    $.ajaxSetup({
//        url: "DBData.php",
//        type: "POST",
//        success: function(data, textStatus, jqXHR){
//            $('.debugOutput').html(jqXHR.responseText);
//        }
//    });
//
//    //
//    var ajaxOutput = $('.ajaxOutput');
//
//    // Shows tables
//    $.ajax({
//        data: {
//            action: "showTables",
//            DBname: 'creatrio'
//        },
//        success: function(data, textStatus, jqXHR){
//            $('.selectTable').html( jqXHR.responseText );   // Shows SELECT with tables
//            $('select.tables').change(function(){           // On selection table:
//                var $_this = $(this);
//
//                var tableName = $_this.val();
//                if(tableName == 'selectTable'){             // If not table name - empty HTML
//                    ajaxOutput.html('');
//                } else {                                    // If table name selected - show the table
//                    showTable( tableName );
//                }
//
//            }); // $('select.tables').change
//
//
//        } // success:
//    }); // $.ajax
//
//    // 1. Update cell data
//    // 2. Create matrix of cells

});

function showTable( tableName ){
    $.ajax({
        data: {
            action: "showFields",
            tableName: tableName
        },
        success: function(data, textStatus, jqXHR){
            $('.ajaxOutput').html(jqXHR.responseText);

            $('input[name=updateCell]').on('click', function(){
                updateCellRow( $(this).parent().parent() );
            });
        }
    });
}

// Updates cell record data
function updateCellRow( element ){
    var updateCellData = {
        sky_cell: element.find('input.sky_cell').val(),
        middle_cell: element.find('input.middle_cell').val(),
        ground_cell: element.find('input.ground_cell').val()
    };

    $.ajax({
        data:{
            action: "updateCellData",
            tableName: 'ct_cells_',
            cellID: element.attr('data-row_id'),
            dataArr: updateCellData
        },
        success: function(){
            // TOD: Check that returned data is success!
            element.css('background', 'green');
            element.stop().animate({ backgroundColor: "#fff" }, 'fast');
        }
    });
}