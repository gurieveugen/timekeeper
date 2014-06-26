jQuery(document).ready(function(){
	// =========================================================
	// Add new item to table
	// =========================================================
	jQuery('.add-table-item').click(function(e){
		var table   = jQuery(this).parent().parent().parent().parent();		
		var last_id = parseInt(table.data('lastId'))+1; 
		var tr      = '<tr id="' + table.attr('id') + '-row-' + last_id  + '">';
		
		for (var i = 0; i < new_row.length; i++) 
		{	
			tr += window.new_row[i].split('__i__').join(last_id);			
		}
		tr += '</tr>';
		
		table.find('tbody .footer').before(tr);	
		table.data('lastId', last_id);
		e.preventDefault();
	});	
});

/**
 * Rmove Table row
 */
function removeRow(obj, e)
{
	var row_id = '#' + jQuery(obj).data('rowId');	
	if(confirm('You realy want remove this item?'))
	{
		jQuery(row_id).remove();
	}
	e.preventDefault();
}