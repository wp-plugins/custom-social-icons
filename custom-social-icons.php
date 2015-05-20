<?php
/*
Plugin Name: Custom Social Icons
Plugin URI: http://websensepro.com
Description: Easily upload your own social icon, set your social URL in vertical and horizontal style, no need to code no technical knowledge required.
Version: 1.0
Author: Bilal Naseer
Author URI: http://websensepro.com
License: GPL2
*/

if( !defined('ABSPATH') ) die('-1');
$upload_dir = wp_upload_dir();
//print_r($upload_dir);
$baseDir = $upload_dir['basedir'].'/';
$baseURL = $upload_dir['baseurl'].'';
$pluginsURI = plugins_url('/custom-social-icons/');

function generateRandomCode($length)
{
	$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$url = "";
	while ($i <= $length) {
		$url .= $chars{mt_rand(0,strlen($chars))};
		$i++;
	}
	return $url;
}

function csi_my_script() {
	global $pluginsURI;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-sortable');
	wp_register_script('csi_js', $pluginsURI . 'js/csi.js', array(), '1.0' );
	wp_enqueue_script( 'csi_js' );	
	
	wp_register_style('csi_css', $pluginsURI . 'css/csi.css', array(), '1.0' );
	wp_enqueue_style( 'csi_css' );	
}

function csi_admin_enqueue() {
	//if ($hook!='custom-social-icon_page_csi_social_icon_add') return; //$hook
	global $pluginsURI;
	wp_enqueue_media();
	wp_register_script('csi_admin_js', $pluginsURI . 'js/csi_admin.js', array(), '1.0' );
	wp_enqueue_script( 'csi_admin_js' );	
}

if( isset($_GET['page']) ) {
	if( $_GET['page']=='csi_social_icon_add' ) {
		add_action('admin_enqueue_scripts', 'csi_admin_enqueue' );
	}
}

add_action('init', 'csi_my_script');
add_action('wp_ajax_update-social-icon-order', 'csi_save_ajax_order' );
add_action('admin_menu', 'csi_add_menu_pages');

function csi_add_menu_pages() {
	add_menu_page('Custom Social Icon', 'Custom Social Icon', 'manage_options', 'csi_social_icon_page', 'csi_social_icon_page_fn',plugins_url('/images/scc-sc.png', __FILE__) );
	
	add_submenu_page('csi_social_icon_page', 'Manage Icons', 'Manage Icons', 'manage_options', 'csi_social_icon_page', 'csi_social_icon_page_fn');
	
	add_submenu_page('csi_social_icon_page', 'Add Icons', 'Add Icons', 'manage_options', 'csi_social_icon_add', 'csi_social_icon_add_fn');
	
	add_submenu_page('csi_social_icon_page', 'Sort Icons', 'Sort Icons', 'manage_options', 'csi_social_icon_sort', 'csi_social_icon_sort_fn');
	
	add_submenu_page('csi_social_icon_page', 'Options', 'Options', 'manage_options', 'csi_social_icon_option', 'csi_social_icon_option_fn');
	
	add_action( 'admin_init', 'register_csi_settings' );
	
}

function register_csi_settings() {
	register_setting( 'csi-settings-group', 'csi-width' );
	register_setting( 'csi-settings-group', 'csi-height' );
	register_setting( 'csi-settings-group', 'csi-margin' );
	register_setting( 'csi-settings-group', 'csi-row-count' );
	register_setting( 'csi-settings-group', 'csi-vertical-horizontal' );
	register_setting( 'csi-settings-group', 'csi-text-align' );
}

function csi_social_icon_option_fn() {
	
	$csi_width = get_option('csi-width');
	$csi_height = get_option('csi-height');
	$csi_margin = get_option('csi-margin');
	$csi_rows = get_option('csi-row-count');
	$vorh = get_option('csi-vertical-horizontal');
	$text_align = get_option('csi-text-align');
	
	$vertical ='';
	$horizontal ='';
	if($vorh=='vertical') $vertical = 'checked="checked"';
	if($vorh=='horizontal') $horizontal = 'checked="checked"';
	
	$center ='';
	$left ='';
	$right ='';
	if($text_align=='center') $center = 'checked="checked"';
	if($text_align=='left') $left = 'checked="checked"';
	if($text_align=='right') $right = 'checked="checked"';
	
	?>
	<div class="wrap">
	<h2>Social Icon Options</h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'csi-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Icon Width</th>
			<td><input type="text" name="csi-width" id="csi-width" class="small-text" value="<?php echo $csi_width?>" />px</td>
			</tr>
			<tr valign="top">
			<th scope="row">Icon Height</th>
			<td><input type="text" name="csi-height" id="csi-height" class="small-text" value="<?php echo $csi_height?>" />px</td>
			</tr>
			<tr valign="top">
			<th scope="row">Icon Margin <em><small>(Gap between each icon)</small></em></th>
			<td><input type="text" name="csi-margin" id="csi-margin" class="small-text" value="<?php echo $csi_margin?>" />px</td>
			</tr>

			<?php /*?><tr valign="top">
			<th scope="row">Number of Rows</th>
			<td><input type="text" name="csi-row-count" id="csi-row-count" class="small-text" value="<?php echo $csi_rows?>" /></td>
			</tr><?php */?>
			
			<tr valign="top">
			<th scope="row">Display Icon</th>
			<td>
				<input <?php echo $horizontal ?> type="radio" name="csi-vertical-horizontal" id="horizontal" value="horizontal" />&nbsp;<label for="horizontal">Horizontally</label><br />
				<input <?php echo $vertical ?> type="radio" name="csi-vertical-horizontal" id="vertical" value="vertical" />&nbsp;<label for="vertical">Vertically</label></td>
			</tr>
            
            <tr valign="top">
			<th scope="row">Icon Alignment</th>
			<td>
				<input <?php echo $center ?> type="radio" name="csi-text-align" id="center" value="center" />&nbsp;<label for="center">Center</label><br />
				<input <?php echo $left ?> type="radio" name="csi-text-align" id="left" value="left" />&nbsp;<label for="left">Left</label><br />
				<input <?php echo $right ?> type="radio" name="csi-text-align" id="right" value="right" />&nbsp;<label for="right">Right</label></td>
			</tr>
		</table>
		
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
	</div>
	<?php 
}

function csi_db_install () {
   global $wpdb;
   global $csi_db_version;
   
	$upload_dir = wp_upload_dir();

	/*$srcdir   = ABSPATH.'wp-content/plugins/custom-social-icons/images/icon/';
	$targetdir = $upload_dir['basedir'].'/';
	
	$files = scandir($srcdir);
	foreach($files as $fname) 
	{
		if($fname=='.')continue;
		if($fname=='..')continue;
		copy($srcdir.$fname, $targetdir.$fname);
	}*/

   $table_name = $wpdb->prefix . "ws_social_icon";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
	$sql2 = "CREATE TABLE `$table_name` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT, 
	`title` VARCHAR(255) NULL, 
	`url` VARCHAR(255) NOT NULL, 
	`image_url` VARCHAR(255) NOT NULL, 
	`sortorder` INT NOT NULL DEFAULT '0', 
	`date_upload` VARCHAR(100) NULL, 
	`target` tinyint(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)) ENGINE = InnoDB;";
	
	/*
	INSERT INTO `$table_name` (`title`, `url`, `image_url`, `sortorder`, `date_upload`, `target`) VALUES
	('facebook', 'http://facebook.com/your-fan-page', '1368459524_facebook.png', 1, '1368459524', 1),
	('twitter', 'http://twitter/username', '1368459556_twitter.png', 2, '1368459556', 1),
	('flickr', 'http://flickr.com/?username', '1368459641_flicker.png', 3, '1368459641', 1),
	('linkedin', 'http://linkedin.com', '1368459699_in.png', 4, '1368459699', 1),
	('youtube', 'http://youtube.com/user', '1368459724_youtube.png', 5, '1368459724', 1);	
	*/
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql2);
	
	add_option( 'csi-width', '32');
	add_option( 'csi-height', '32');
	add_option( 'csi-margin', '4');
	add_option( 'csi-row-count', '1');
	add_option( 'csi-vertical-horizontal', 'horizontal');
	add_option( 'csi-text-align', 'center');
  }
}

register_activation_hook(__FILE__,'csi_db_install');

if (isset($_GET['delete'])) {
	
	if ($_GET['id'] != '')
	{
	
		$table_name = $wpdb->prefix . "ws_social_icon";
		$image_file_path = $baseDir; //"../wp-content/uploads/";
		/*$sql = "SELECT * FROM ".$table_name." WHERE id =".$_GET['id'];
		$video_info = $wpdb->get_results($sql);
		
		if (!empty($video_info))
		{
			@unlink($image_file_path.$video_info[0]->image_url);
		}*/
		//$delete = "DELETE FROM ".$table_name." WHERE id = ".$_GET['id']." LIMIT 1";
		//$results = $wpdb->query( $delete );
		
		$wpdb->delete( $table_name, array( 'id' => $_GET['id'] ), array( '%d' ) );
		
		$msg = "Delete Successfully!!!"."<br />";
	}

}

add_action('init', 'ws_process_post');

function ws_process_post(){
	global $wpdb,$err,$msg,$baseDir;
	if ( isset($_POST['submit_button']) && check_admin_referer('ws_insert_icon') ) {
	
		if ($_POST['action'] == 'update')
		{
		
			$err = "";
			$msg = "";
			
			//$image_file_path = "../wp-content/uploads/";
			$image_file_path = $baseDir;
			
			/*if ($_FILES["image_file"]["name"] != "" ){
			
				$extArr = array('jpg','png','gif','jpeg');
				$target_file = $image_file_path . basename($_FILES["image_file"]["name"]);
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$check = getimagesize($_FILES["image_file"]["tmp_name"]);
				if($check === false || !in_array($imageFileType,$extArr)) {
					$err .= "Invalid file type<br />";
				}
				else
				{
					if( $err=='' and $_FILES["image_file"]["size"] < 1024*1024*1 ) {
					
						if ($_FILES["image_file"]["error"] > 0)
						{
							$err .= "Return Code: " . $_FILES["image_file"]["error"] . "<br />";
						}
					  else
						{
						if (file_exists($image_file_path . $_FILES["image_file"]["name"]))
						  {
						  $err .= $_FILES["image_file"]["name"] . " already exists. ";
						  }
						else
						  {
							$image_file_name = time().generateRandomCode(16).'.'.$imageFileType;
							$fstatus = move_uploaded_file($_FILES["image_file"]["tmp_name"], $image_file_path . $image_file_name);
							if ($fstatus == true){
								$msg = "Icon upload successful !"."<br />";
							}
						  }
						}
					  }
					else
					{
						$err .= "Max file size exceded" . "<br />";
					}
				}
			}
			else
			{
				$err .= "Please input image file". "<br />";
			}// end if image file*/
			
			if ($err == '')
			{
				$table_name = $wpdb->prefix . "ws_social_icon";
		
				/*$insert = "INSERT INTO " . $table_name .
				" (title, url, image_url, sortorder, date_upload, target) " .
				"VALUES ('" . 
				sanitize_text_field( $_POST['title']) . "','" . 
				sanitize_text_field( $_POST['url']) . "','" . 
				$_POST['image_file'] . "'," . 
				$_POST['sortorder'] . ",'" . 
				time() . "'," . 
				$_POST['target'] . "" . 
				")";*/
				//$results = $wpdb->query( $insert );
				
				$results = $wpdb->insert( 
					$table_name, 
					array( 
						'title' => sanitize_text_field($_POST['title']), 
						'url' => sanitize_text_field($_POST['url']), 
						'image_url' => sanitize_text_field($_POST['image_file']), 
						'sortorder' => sanitize_text_field($_POST['sortorder']), 
						'date_upload' => time(), 
						'target' => sanitize_text_field($_POST['target']), 
					), 
					array( 
						'%s', 
						'%s',
						'%s', 
						'%d',
						'%s', 
						'%d',
					) 
				);
				
				if (!$results)
					$err .= "Fail to update database" . "<br />";
				else
					$msg .= "Update successful !" . "<br />";
			
			}
		}// end if update
		
		if ( $_POST['action'] == 'edit' and $_POST['id'] != '' )
		{
			$err = "";
			$msg = "";
	
			$url = $_POST['url'];
			$target = $_POST['target'];
			
			//$image_file_path = "../wp-content/uploads/";
			$image_file_path = $baseDir;
			
			$table_name = $wpdb->prefix . "ws_social_icon";
			$sql = "SELECT * FROM ".$table_name." WHERE id =".$_POST['id'];
			$video_info = $wpdb->get_results($sql);
			$image_file_name = $video_info[0]->image_url;
			$update = "";
			
			$type= 1;
			/*if ($_FILES["image_file"]["name"] != ""){
			
				$extArr = array('jpg','png','gif','jpeg');
				$target_file = $image_file_path . basename($_FILES["image_file"]["name"]);
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$check = getimagesize($_FILES["image_file"]["tmp_name"]);
				if($check === false || !in_array($imageFileType,$extArr)) {
					$err .= "Invalid file type<br />";
				}
				else
				{
			
					if( $err=='' && $_FILES["image_file"]["size"] <= 1024*1024*1 )
					  {
						if ($_FILES["image_file"]["error"] > 0)
						{
							$err .= "Return Code: " . $_FILES["image_file"]["error"] . "<br />";
						}
					  else
						{
						if (file_exists($image_file_path . $_FILES["image_file"]["name"]))
						  {
						  $err .= $_FILES["image_file"]["name"] . " already exists. ";
						  }
						else
						  {
							$image_file_name = time().generateRandomCode(16).'.'.$imageFileType;
							$fstatus = move_uploaded_file($_FILES["image_file"]["tmp_name"], $image_file_path . $image_file_name);
							
							if ($fstatus == true){
								$msg = "File Uploaded Successfully!!!".'<br />';
								@unlink($image_file_path.$video_info[0]->image_url);
								$update = "UPDATE " . $table_name . " SET " . 
								"image_url='" .$image_file_name . "'" . 
								" WHERE id=" . $_POST['id'];
								$results1 = $wpdb->query( $update );
							}
						  }
						}
					  }
					else
					{
						$err .= "Invalid file type or max file size exceded";
					}
				}
			}*/
			
			/*$update = "UPDATE " . $table_name . " SET " . 
			"title='" .sanitize_text_field( $_POST['title']) . "'," . 
			"url='" . $url . "'," . 
			"image_url='" . $_POST['image_file'] . "'," . 
			"sortorder=" .$_POST['sortorder'] . "," . 
			"date_upload='" .time(). "'," . 
			"target=$target " .
			" WHERE id=" . $_POST['id'];*/
			
			
			if ($err == '')
			{
				$table_name = $wpdb->prefix . "ws_social_icon";
				//$results3 = $wpdb->query( $update );
				
				$result3 = $wpdb->update( 
					$table_name, 
					array( 
						'title' => sanitize_text_field($_POST['title']),
						'url' => sanitize_text_field($_POST['url']),
						'image_url' => sanitize_text_field($_POST['image_file']),
						'sortorder' => sanitize_text_field($_POST['sortorder']),
						'date_upload' => time(),
						'target' => sanitize_text_field($_POST['target']),
					), 
					array( 'id' => sanitize_text_field($_POST['id']) ), 
					array( 
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
					), 
					array( '%d' ) 
				);		
				
				if (false === $result3){
					$err .= "Update fails !". "<br />";
				}
				else
				{
					$msg = "Update successful !". "<br />";
				}
			}
			
		} // end edit
		
	}
}//ws_process_post end

function csi_social_icon_sort_fn() {
	global $wpdb,$baseURL;
	
	$csi_width = get_option('csi-width');
	$csi_height = get_option('csi-height');
	
	$image_file_path = $baseURL; //"../wp-content/uploads/";
	$table_name = $wpdb->prefix . "ws_social_icon";
	$sql = "SELECT * FROM ".$table_name." WHERE 1 ORDER BY sortorder";
	$video_info = $wpdb->get_results($sql);

?>
	<div class="wrap">
		<h2>Sort Icon</h2>

		<div id="ajax-response"></div>
		
		<noscript>
			<div class="error message">
				<p><?php _e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'cpt') ?></p>
			</div>
		</noscript>
		
		<div id="order-post-type">
			<ul id="sortable">
			<?php 
			foreach($video_info as $vdoinfo) { 
				if(strpos($vdoinfo->image_url,'/')===false)
					$image_url = $image_file_path.'/'.$vdoinfo->image_url;
				else
					$image_url = $vdoinfo->image_url;
			
			?>
					<li id="item_<?php echo $vdoinfo->id ?>">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr style="background:#f7f7f7">
						<td width="60">&nbsp;<img src="<?php echo $image_url;?>" border="0" width="<?php echo $csi_width ?>" height="<?php echo $csi_height ?>" alt="<?php echo $vdoinfo->title;?>" /></td>
						<td><span><?php echo $vdoinfo->title;?></span></td>
					  </tr>
					</table>
					</li>
			<?php } ?>
			</ul>
			
			<div class="clear"></div>
		</div>
		
		<p class="submit">
			<a href="#" id="save-order" class="button-primary">Update</a>
		</p>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#sortable").sortable({
					tolerance:'intersect',
					cursor:'pointer',
					items:'li',
					placeholder:'placeholder'
				});
				jQuery("#sortable").disableSelection();
				jQuery("#save-order").bind( "click", function() {
					//alert(jQuery("#sortable").sortable("serialize"));
					jQuery.post( ajaxurl, { action:'update-social-icon-order', order:jQuery("#sortable").sortable("serialize") }, function(response) {
						//alert(response);
						jQuery("#ajax-response").html('<div class="message updated fade"><p>Items Order Updated</p></div>');
						jQuery("#ajax-response div").delay(3000).hide("slow");
					});
				});
			});
		</script>
		
	</div>
<?php
}

function csi_save_ajax_order() 
{
	global $wpdb;
	$table_name = $wpdb->prefix . "ws_social_icon";
	parse_str($_POST['order'], $data);
	if (is_array($data))
	foreach($data as $key => $values ) 
	{
	
		if ( $key == 'item' ) 
		{
			foreach( $values as $position => $id ) 
				{
					$wpdb->update( $table_name, array('sortorder' => $position), array('id' => $id) );
				} 
		} 
	
	}
}


function csi_social_icon_add_fn() {

	global $err,$msg,$baseURL;
	
	$csi_width = get_option('csi-width');
	$csi_height = get_option('csi-height');
	//$csi_margin = get_option('csi-margin');

	if (isset($_GET['mode'])) {
		if ( $_GET['mode'] != '' and $_GET['mode'] == 'edit' and  $_GET['id'] != '' )
		{
			$page_title = 'Edit Icon';
			$uptxt = 'Icon';
			
			global $wpdb;
			$table_name = $wpdb->prefix . "ws_social_icon";
			$image_file_path = $baseURL; //"../wp-content/uploads/";
			$sql = "SELECT * FROM ".$table_name." WHERE id =".$_GET['id'];
			$video_info = $wpdb->get_results($sql);
			
			if (!empty($video_info))
			{
				$id = $video_info[0]->id;
				$title = $video_info[0]->title;
				$url = $video_info[0]->url;
				$image_url = $video_info[0]->image_url;
				$sortorder = $video_info[0]->sortorder;
				$target = $video_info[0]->target;
				
				if(strpos($image_url,'/')===false)
					$image_url = $image_file_path.'/'.$image_url;
				else
					$image_url = $image_url;
				
			}
		}
	}
	else
	{
		$page_title = 'Add New Icon';
		$title = "";
		$url = "";
		$image_url = "";
		$blank_img = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
		$sortorder = "0";
		$target = "";
		$uptxt = 'Icon';
	}
?>
<div class="wrap">
<?php
if($msg!='') echo '<div id="message" class="updated fade">'.$msg.'</div>';
if($err!='') echo '<div id="message" class="error fade">'.$err.'</div>';
?>
<h2><?php echo $page_title;?></h2>

<form method="post" enctype="multipart/form-data" action="<?php //echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <?php wp_nonce_field('ws_insert_icon'); ?>
    <table class="form-table">
        <tr valign="top">
			<th scope="row">Title</th>
			<td>
				<input type="text" name="title" id="title" class="regular-text" value="<?php echo $title?>" />
			</td>
        </tr>
		
        <tr valign="top">
			<th scope="row"><?php echo $uptxt;?></th>
			<td>
				<?php //if (isset($_GET['mode'])) { } ?>
				<!--<input type="file" name="image_file" id="image_file" value="" />-->
				<input style="vertical-align:top" type="text" name="image_file" id="image_file" class="regular-text" value="<?php echo $image_url ?>" />
				<input style="vertical-align:top" id="logo_image_button" class="button" type="button" value="Choose Icon" />
				<img style="vertical-align:top" id="logoimg" src="<?php echo $image_url==''?$blank_img:$image_url; ?>" border="0" width="32"  height="32" alt="<?php echo $title?>" /><br />
			</td>
        </tr>
		
        <tr valign="top">
			<th scope="row">URL</th>
			<td><input type="text" name="url" id="url" class="regular-text" value="<?php echo $url?>" /><br /><i>Example: <strong>http://facebook.com/your-fan-page</strong> &ndash; don't forget the <strong><code>http://</code></strong></i></td>
        </tr>
		
        <tr valign="top">
			<th scope="row">Sort Order</th>
			<td>
				<input type="text" name="sortorder" id="sortorder" class="small-text" value="<?php echo $sortorder?>" />
			</td>
        </tr>
		
		<tr valign="top">
			<th scope="row">Target</th>
			<td>
				<input type="radio" name="target" id="new" checked="checked" value="1" />&nbsp;<label for="new">Open new window</label>&nbsp;<br />
				<input type="radio" name="target" id="same" value="0" />&nbsp;<label for="same">Open same window</label>&nbsp;
			</td>
        </tr>		
        
		
    </table>
	
	
	<?php if (isset($_GET['mode']) ) { ?>
	<input type="hidden" name="action" value="edit" />
	<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
	<?php } else {?>
	<input type="hidden" name="action" value="update" />
	<?php } ?>
	
    
    <p class="submit">
    <input type="submit" id="submit_button" name="submit_button" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>


</div>
<?php 
} 

function csi_social_icon_page_fn() {
	
	global $wpdb,$baseURL;
	
	$csi_width = get_option('csi-width');
	$csi_height = get_option('csi-height');
	
	$image_file_path = $baseURL; //"../wp-content/uploads/";
	$table_name = $wpdb->prefix . "ws_social_icon";
	$sql = "SELECT * FROM ".$table_name." WHERE 1 ORDER BY sortorder";
	$video_info = $wpdb->get_results($sql);
	?>
	<div class="wrap">
	<h2>Manage Icons</h2>
	<script type="text/javascript">
	function show_confirm(title, id)
	{
		var rpath1 = "";
		var rpath2 = "";
		var r=confirm('Are you confirm to delete "'+title+'"');
		if (r==true)
		{
			rpath1 = '<?php echo $_SERVER['PHP_SELF'].'?page=csi_social_icon_page'; ?>';
			rpath2 = '&delete=y&id='+id;
			//alert(rpath1+rpath2);
			window.location = rpath1+rpath2;
		}
	}
	</script>
	
	
		<table class="widefat page fixed" cellspacing="0">
		
			<thead>
			<tr valign="top">
				<th class="manage-column column-title" scope="col">Title</th>
				<th class="manage-column column-title" scope="col">URL</th>
				<th class="manage-column column-title" scope="col" width="100">Open In</th>
				<th class="manage-column column-title" scope="col" width="100">Icon</th>
				<th class="manage-column column-title" scope="col" width="50">Order</th>
				<th class="manage-column column-title" scope="col" width="50">Edit</th>
				<th class="manage-column column-title" scope="col" width="50">Delete</th>
			</tr>
			</thead>
			
			<tbody>
			<?php
			foreach($video_info as $vdoinfo) { 
				if(strpos($vdoinfo->image_url,'/')===false)
					$image_url = $image_file_path.'/'.$vdoinfo->image_url;
				else
					$image_url = $vdoinfo->image_url;
			?>
			<tr valign="top">
				<td>
					<?php echo $vdoinfo->title;?>
				</td>
				<td>
					<?php echo $vdoinfo->url;?>
				</td>
				<td>
					<?php echo $vdoinfo->target==1?'New Window':'Same Window' ?>
				</td>
				
				<td>
					<img src="<?php echo $image_url;?>" border="0" width="<?php echo $csi_width ?>" height="<?php echo $csi_height ?>" alt="<?php echo $vdoinfo->title;?>" />
				</td>
	
				<td>
					<?php echo $vdoinfo->sortorder;?>
				</td>
				<td>
					<a href="?page=csi_social_icon_add&mode=edit&id=<?php echo $vdoinfo->id;?>"><strong>Edit</strong></a>
				</td>
				<td>
					<a onclick="show_confirm('<?php echo addslashes($vdoinfo->title)?>','<?php echo $vdoinfo->id;?>');" href="#delete"><strong>Delete</strong></a>
				</td>
				
			</tr>
			<?php }?>
			</tbody>
			<tfoot>
			<tr valign="top">
				<th class="manage-column column-title" scope="col">Title</th>
				<th class="manage-column column-title" scope="col">URL</th>
				<th class="manage-column column-title" scope="col" width="100">Open In</th>
				<th class="manage-column column-title" scope="col" width="100">Icon</th>
				<th class="manage-column column-title" scope="col" width="50">Order</th>
				<th class="manage-column column-title" scope="col" width="50">Edit</th>
				<th class="manage-column column-title" scope="col" width="50">Delete</th>
			</tr>
			</tfoot>
		</table>
	</div>
	<?php
}

function ws_social_icon_table() {

	$csi_width = get_option('csi-width');
	$csi_height = get_option('csi-height');
	$csi_margin = get_option('csi-margin');
	$csi_rows = get_option('csi-row-count');
	$vorh = get_option('csi-vertical-horizontal');

	//$upload_dir = wp_upload_dir(); 
	global $wpdb,$baseURL;
	$table_name = $wpdb->prefix . "ws_social_icon";
	$image_file_path = $baseURL; //$upload_dir['baseurl'];
	$sql = "SELECT * FROM ".$table_name." WHERE image_url<>'' AND url<>'' ORDER BY sortorder";
	$video_info = $wpdb->get_results($sql);
	$icon_count = count($video_info);
	
	$_collectionSize = count($video_info);
	$_rowCount = $csi_rows ? $csi_rows : 1;
	$_columnCount = ceil($_collectionSize/$_rowCount);
	
	if($vorh=='vertical')
		$table_width = $csi_width;
	else
		$table_width = $_columnCount*($csi_width+$csi_margin);
		//$table_width = $icon_count*($csi_width+$csi_margin);
	
	$td_width = $csi_width+$csi_margin;
		
	ob_start();
	echo '<table class="csi-social-icon" style="width:'.$table_width.'px" border="0" cellspacing="0" cellpadding="0">';
	//echo $vorh=='horizontal'?'<tr>':'';
	$i=0;
	foreach($video_info as $icon)
	{ 
	
	if(strpos($icon->image_url,'/')===false)
		$image_url = $image_file_path.'/'.$icon->image_url;
	else
		$image_url = $icon->image_url;
	
	echo $vorh=='vertical'?'<tr>':'';
	if($i++%$_columnCount==0 && $vorh!='vertical' )echo '<tr>';
	?><td style="width:<?php echo $td_width ?>px"><a <?php echo ($icon->target==1)?'target="_blank"':'' ?> title="<?php echo $icon->title ?>" href="<?php echo $icon->url ?>"><img src="<?php echo $image_url?>" border="0" width="<?php echo $csi_width ?>" height="<?php echo $csi_height ?>" alt="<?php echo $icon->title ?>" /></a></td><?php 
	if ( ($i%$_columnCount==0 || $i==$_collectionSize) && $vorh!='vertical' )echo '</tr>';
	echo $vorh=='vertical'?'</tr>':'';
	//$i++;
	}
	//echo $vorh=='horizontal'?'</tr>':'';
	echo '</table>';
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

function format_title($str) {
	$pattern = '/[^a-zA-Z0-9]/';
	return preg_replace($pattern,'-',$str);
}

function ws_social_icon() {

	$csi_width = get_option('csi-width');
	$csi_height = get_option('csi-height');
	$csi_margin = get_option('csi-margin');
	$csi_rows = get_option('csi-row-count');
	$vorh = get_option('csi-vertical-horizontal');
	$text_align = get_option('csi-text-align');

	global $wpdb,$baseURL;
	$table_name = $wpdb->prefix . "ws_social_icon";
	$image_file_path = $baseURL;
	$sql = "SELECT * FROM ".$table_name." WHERE image_url<>'' AND url<>'' ORDER BY sortorder";
	$video_info = $wpdb->get_results($sql);
	$icon_count = count($video_info);
	
	$_collectionSize = count($video_info);
	$_rowCount = $csi_rows ? $csi_rows : 1;
	$_columnCount = ceil($_collectionSize/$_rowCount);
	$li_margin = round($csi_margin/2);
		
	ob_start();
	echo '<ul class="csi-social-icon" style="text-align:'.$text_align.';">';
	$i=0;
	foreach($video_info as $icon)
	{ 
	
	if(strpos($icon->image_url,'/')===false)
		$image_url = $image_file_path.'/'.$icon->image_url;
	else
		$image_url = $icon->image_url;

	?><li class="<?php echo format_title($icon->title); ?>" style=" <?php echo $vorh=='horizontal'?'display:inline-block;':''; ?>"><a <?php echo ($icon->target==1)?'target="_blank"':'' ?> title="<?php echo $icon->title ?>" href="<?php echo $icon->url ?>"><img src="<?php echo $image_url?>" border="0" width="<?php echo $csi_width ?>" height="<?php echo $csi_height ?>" alt="<?php echo $icon->title ?>" style=" <?php echo 'margin:'.$li_margin.'px;'; ?>" /></a></li><?php 
	$i++;
	}
	echo '</ul>';
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

class Cnss_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'csi_widget', // Base ID
			'Custom Social Icon', // Name
			array( 'description' => __( 'Custom Social Icon Widget for sidebar' ) ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo ws_social_icon();
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class Cnss_Widget
add_action( 'widgets_init', create_function( '', 'register_widget( "Cnss_Widget" );' ) );

add_shortcode('ws-social-icon', 'ws_social_icon');