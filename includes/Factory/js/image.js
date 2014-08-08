/**
 * Set photo with upload dialog
 * @param object obj 
 */
function setPhoto(obj)
{
	var input_id = jQuery(obj).attr('data-name');
	var input    = jQuery('#' + input_id);
	tb_show('Loading photo', 'media-upload.php?type=image&amp;TB_iframe=true');

	window.send_to_editor = function(html) {
		imgurl = jQuery('img', html).attr('src');
		input.val(imgurl);
		tb_remove();
	}
 	return false;
}