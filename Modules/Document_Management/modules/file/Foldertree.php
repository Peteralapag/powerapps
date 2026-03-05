<?php
class treeview {

	private $files;
	private $folder;
	
	function __construct( $path ) {
		
		$files = array();	
		
		if( file_exists( $path)) {
			if( $path[ strlen( $path ) - 1 ] ==  '/' )
				$this->folder = $path;
			else
				$this->folder = $path . '/';
			
			$this->dir = opendir( $path );
			while(( $file = readdir( $this->dir ) ) != false )
				$this->files[] = $file;
			closedir( $this->dir );
		}
	}

	function create_tree( )
	{
		if( count( $this->files ) > 2 ) { /* First 2 entries are . and ..  -skip them */
			natcasesort( $this->files );
			$list = '<ul class="filetree" style="display: none;">';
			// Group folders first
			foreach( $this->files as $file )
			{
				if( file_exists( $this->folder . $file ) && $file != '.' && $file != '..' && $file != 'index.php' && is_dir( $this->folder . $file ))
				{
					$list .= '
						<li class="folder collapsed" onclick="openFolder(\''.htmlentities( $this->folder . $file ).'\')">
							<a href="#" rel="' . htmlentities( $this->folder . $file ) . '/">' . htmlentities( $file ) . '</a>
						</li>
					';
				}
			}
			// Group all files
			foreach( $this->files as $file )
			{
				if( file_exists( $this->folder . $file ) && $file != '.' && $file != '..' && $file != 'index.php' && !is_dir( $this->folder . $file ))
				{
					$ext = preg_replace('/^.*\./', '', $file);
					$list .= '
						<li class="file ext_' . $ext . '" onclick="openFile(\''.htmlentities( $file ).'\',\''.htmlentities( $this->folder ).'\')">
							<a href="#" rel="' . htmlentities( $this->folder . $file ) . '">' . htmlentities( $file ) . '</a>
						</li>
					';
				}
			}
			$list .= '</ul>';	
			return $list;
		}
	}
}
$path = urldecode( $_REQUEST['dir'] );
$tree = new treeview( $path );
echo $tree->create_tree();
?>

<script>
function openFolder(params)
{
	$('#fullpath').val(params);
}
function openFile(filename,path)
{
	var module = $('#department').val();
	var subfolder = $('#subfolder').val();
	$('#modalicon').html('<i class="fa-solid fa-file-pdf color-red"></i>');
	$('#modaltitle').html('File information - Properties');	
	$.post("../Modules/Document_Management/apps/file_info.php", { module: module, path: path, filename: filename, subfolder: subfolder },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function CheckAccess(access)
{
	$.post("./Actions/check_access.php", { access: access },
		function(data) {
		console.log(data);
		sessionStorage.setItem("access", data);
		$('#res').html(data);
	});
}
</script>