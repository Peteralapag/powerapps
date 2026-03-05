<?php
	$path = $_POST['path'];
	$department = $_POST['department'];
?>
<link rel="stylesheet" href="../Modules/Document_Management/modules/file/css/filetree.css" type="text/css" >
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" >
$(document).ready( function()
{
	var rooter = '<?php echo $path; ?>';
	$( '#container' ).html( '<ul class="filetree start"><li class="wait">' + 'Generating Tree...' + '<li></ul>' );
	getfilelist( $('#container') , '../../../../Data/Document_Management/' + rooter );
	
	function getfilelist( cont, root ) {
	
		$( cont ).addClass( 'wait' );
			
		$.post( '../../../../Modules/Document_Management/modules/file/Foldertree.php', { dir: root }, function( data ) {
	
			$( cont ).find( '.start' ).html( '' );
			$( cont ).removeClass( 'wait' ).append( data );
			if( 'Sample' == root ) 
				$( cont ).find('UL:hidden').show();
			else 
				$( cont ).find('UL:hidden').slideDown({ duration: 500, easing: null });
			
		});
	}
	
	$( '#container' ).on('click', 'LI A', function()
	{
		var entry = $(this).parent();	
		if( entry.hasClass('folder') ) {
			if( entry.hasClass('collapsed') ) {
						
				entry.find('UL').remove();
				getfilelist( entry, escape( $(this).attr('rel') ));
				entry.removeClass('collapsed').addClass('expanded');
			}
			else {
				
				entry.find('UL').slideUp({ duration: 500, easing: null });
				entry.removeClass('expanded').addClass('collapsed');
			}
		} else {
			$( '#selected_file' ).text( "File:  " + $(this).attr( 'rel' ));
		}
	return false;
	});
	
});
</script>
<div id="container"> </div>
<!-- div id="selected_file"></div -->
