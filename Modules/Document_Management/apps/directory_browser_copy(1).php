<style>
.main-folder li {
	padding:10px;
}
.sub-folder {
	display: none;
}
.sub-folder li {
	color:blue;
}
</style>
<?php 
$path = '../../../Data/Document_Management/'.$_POST['department']."/".$_POST['subfolder'];
 
function listFolderFiles($dir)
{
    $ffs = scandir($dir);
    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);
    unset($ffs[array_search('index.php', $ffs, true)]);

    if (count($ffs) < 1)
        return;

    echo '<ol class="main-folder">';
    $n=0;
    foreach($ffs as $ff)
    {
    	$n++;
?>    	
        <li onclick="openThis('<?php echo $n; ?>')"><i class="fa-solid fa-folder color-orange"></i> <?php echo $ff; ?>

<?php if(is_dir($dir.'/')) { ?>
        	<ul class="sub-folder" id="subfolder<?php echo $n; ?>">
	        	<li><?php 
	        		if(is_file($dir.'/'.$ff)) {
		        		listFolderFiles($dir.'/'.$ff);
		        	}
		        /*	else if(is_dir($dir.'/')) {
		        		listFolderFiles($dir.'/'.$ff);
		        	} */
	        	?></li>
        	</ul>
        </li>
<?php
        }
    }
    echo '</ol>';
}
listFolderFiles($path); 
?>
<script>
function openThis(params)
{
	$('#subfolder' + params).show();
}
</script>
