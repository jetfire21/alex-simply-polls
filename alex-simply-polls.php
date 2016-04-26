<?php
/*
Plugin Name: Simply Polls
Plugin URI: http://shebalov.ru/plugins/alex-simply-polls.zip
Description: Simply Polls is a simple plugin to create a poll easily and quickly. You can use our polls on sidebars, posts and pages
Version: 1.0
Author: Alexey Shebalov
Author URI: http://shebalov.ru
License: GPLv2 or later
Text Domain: simply_polls
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require dirname(__FILE__)."/alex_helpers.php";

register_activation_hook( __FILE__, 'alex_activ_plugin' );
register_deactivation_hook( __FILE__, 'alex_deactiv_plugin' );

### Create Text Domain For Translations
add_action('plugins_loaded', 'polls_textdomain');
function polls_textdomain(){
	load_plugin_textdomain( 'simply_polls', false, dirname( plugin_basename( __FILE__ ) )."/lang" ); 
}

function alex_activ_plugin(){

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();  //DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci usually DEFAULT CHARSET=utf8

	$sql = "CREATE TABLE IF NOT EXISTS $wpdb->table_name (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `title` varchar(255) NOT NULL,
	  `content` text NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB $charset_collate AUTO_INCREMENT=1" ;

	$sql2 =	"CREATE TABLE IF NOT EXISTS $wpdb->table2_name (
	  `answ_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `id_quest` int(10) unsigned NOT NULL,
	  `answ` varchar(200) NOT NULL,
	  `votes` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`answ_id`)
	) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=1" ;

	$sql3 = "CREATE TABLE IF NOT EXISTS $wpdb->table3_name  (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `ip` varchar(15) NOT NULL,
	  `id_poll` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  $charset_collate AUTO_INCREMENT=1" ;

	$wpdb->query($sql);
	$wpdb->query($sql2);
	$wpdb->query($sql3);

}

function alex_deactiv_plugin(){

}


add_action( 'wp_enqueue_scripts', 'alex_frontend_css' );

function alex_frontend_css(){

	  wp_register_style( "frontend_css", plugins_url("/css/alex_frontend.css",__FILE__) );
	  wp_enqueue_style( 'frontend_css' );
 }

function register_alex_menu_page(){
	add_menu_page(  __( 'name_plugin', 'simply_polls' ), __( 'name_plugin', 'simply_polls' ), 'manage_options', 'slug_alex_polls','cb_alex_polls_options','
dashicons-editor-alignleft' );
	add_action("admin_enqueue_scripts", "alex_admin_css");
	function alex_admin_css($hook){
		//$hook - адрес текущей страницы
		// echo $hook." xcvb";
		if( $hook != "toplevel_page_slug_alex_polls") return;
		wp_enqueue_style( 'alex-admin', plugins_url("/css/alex_admin.css",__FILE__) );

		//load js in footer
		wp_enqueue_script('alex-admin-js',plugins_url("/js/alex_admin.js",__FILE__),array('jquery'),false,true);

		wp_localize_script( 'alex-admin-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ),  'admin_nonce' => wp_create_nonce('admin_nonce')) );
		wp_localize_script( 'alex-admin-js', 'dataL10n', array( 'del_poll' =>__( 'del_poll', 'simply_polls' ), 'l_answer' =>__( 'l_answer', 'simply_polls' ), 'conf_del_poll' => __( 'conf_del_poll', 'simply_polls'  ), 'conf_del_answ' => __( 'conf_del_answ', 'simply_polls'  ), 'success_del_poll' => __( 'success_del_poll', 'simply_polls'  ), 'success_del_answ' => __( 'success_del_answ', 'simply_polls'  ) ) );
			
	}
}
add_action( 'admin_menu', 'register_alex_menu_page' );


function cb_alex_polls_options(){

	### Check User is administrator
	if( !current_user_can('manage_options') ){
		exit;
	}

	$base_page = 'admin.php?page=slug_alex_polls';

     $mode =  ( $_GET['mode'] ) ? trim( $_GET['mode'] ) : '';
     $poll_id =	( $_GET['id'] > 0 ) ? (int)$_GET['id'] : 0; 

	switch ($mode) {
	case 'add':
		?>
		<div class='wrap'>
		<div id="for_message"><h1><?php echo __( 'add_poll', 'simply_polls' );?> </h1>
		<?php 
		 // print_r($_POST);
		 if($_POST['add']){
			 $title = trim($_POST['title']);
			 if( !empty($title)) {
				echo '<div id="message" class="updated fade"><p>'.__( 'success_add_poll', 'simply_polls' ).'</p></div>';
			}else{
				echo '<div id="message" class="updated fade error"><p>'.__( 'add_warn_req_field', 'simply_polls' ).' "'. __( 'title_poll', 'simply_polls' ).'"</p></div>';
			 }
		}
		?>
		 </div>

	     <form method="post" action="<?php echo $base_page;?>&amp;mode=add">
		<table class="form-table table1">
		<tr>
			<th scope="row"><label for="title"><?php _e( 'title_poll', 'simply_polls' );?></label></th>
			<td><input maxlength="200" name="title" type="text" id="title" class="regular-text" /></td>
			<td></td>
		</tr>
		<tr>
			<th scope="row"><label for="content"><?php _e( 'custom_text', 'simply_polls' );?></label></th>
			<td><textarea rows="1" cols="45" name="content" id="excerpt" class="regular-text" /></textarea></td>
			<td></td>
		</tr>
			<tr class="answ" id="poll_answ_1">
				<th scope="row"><label for="answ1"><?php _e( 'l_answer', 'simply_polls' );?> 1</label></th>
				<td><input name="answ1" type="text" id="answ1" class="regular-text" /></td>
				<td><input type="submit" onclick="del_answ_html(1); return false;" name="del_answ" class="button" value="<?php _e( 'del_poll', 'simply_polls' );?>"></td>
			</tr>
			<tr class="answ" id="poll_answ_2"> 
				<th scope="row"><label for="answ2"><?php _e( 'l_answer', 'simply_polls' );?> 2</label></th>
				<td><input name="answ2" type="text" id="answ2" class="regular-text" /></td>
				<td><input type="submit" onclick="del_answ_html(2); return false;" name="del_answ" class="button" value="<?php _e( 'del_poll', 'simply_polls' );?>"></td>
			</tr>
		</table>
		<table class="form-table">
			<tr>
				<th scope="row"> </th>
				<td><input type="submit" name="add_answ" id="add_answ_html" class="button" value="<?php _e( 'add_answer', 'simply_polls' );?>"></td>
			</tr>
		</table>
		
		<p class="submit">
		<input type="submit" name="add" id="submit" class="button button-primary" value="<?php _e( 'add', 'simply_polls' );?>">
		<input type="submit" name="cancel" class="button action" value="<?php _e( 'cancel', 'simply_polls' );?>" onclick="window.history.go(-1);">
		</p>
		</form>	
		</div>	
    <?php
    	if($_POST['add']) {
    		
    		// print_r($_POST);
    		$title = trim($_POST['title']);
    		$content = trim($_POST['content']);
    		$count_fields = count($_POST);
    		foreach ($_POST as $k => $v) {
    			$v = trim($v);
    			if( !empty($v) ){
    				$answs[$k] = $v; 
    			}
    		}
    		unset($answs['title']);
    		unset($answs['content']);
    		unset($answs['add']);
    		// echo "<pre>";
    		// print_r($answs);
    		// echo "</pre>";
    		if( !empty($title) ){
    			$add_poll = add_poll($title, $content);
    		}else{
    			$error['title'] = 1;
    		}
    		add_answs($answs);
    		
    	}
	break;
	case 'results':
		$html = '<div class="wrap">';
		$html .= "<h1>". __( 'results', 'simply_polls' )."</h1>";
		$title = get_one_poll_title($poll_id);
		$answers = get_one_poll_answers($poll_id);
		if(!empty($title)){

			$html .= "<div class='poll-alex' data-poll-id='".$poll_id."'>";
			$html .= "<h3>".$title[0]['title']."</h3>";
			if( $title[0]['content'] ) $html .= "<p class='dop-text'>".$title[0]['content']."</p>";

			if( !empty($answers) ){

				$count_votes = 0;
				foreach ($answers as $k => $v) {
					$count_votes = $count_votes + $v['votes'];
				}
				if($count_votes > 0) {
					foreach ($answers as $k => $v) {			
						$percent = round( (100/$count_votes) * $v['votes'] );
						$html .= "<p>".$v['answ']." - (".$percent." %, ".$v['votes']." ".__('votes', 'simply_polls')." )</p>";
						if($v['votes']>0) $html .= "<p class='vote-bar' style='width:".$percent."%;'> </p>";
					}
					$html .= "<p class='total-vote'>".__('total_vote', 'simply_polls')." - ".$count_votes."</p>";			
				}else{
					$html .= "<p>".__('no_vote', 'simply_polls')."</p>";
					// $html .= "<p>В этом опросе пока никто не голосовал</p>";
					 // votes for this pol
				}			
			}
			$html .= '</div> <p><input type="submit" name="cancel" class="button action" value="'.__( 'back', 'simply_polls' ).'" onclick="window.history.go(-1);"></p>';
		}
		$html .= "</div>";

		 echo $html;
		
		break;
	case 'edit':
    		if($_POST['edit']) {
	    		// print_r($_POST);

	    		$title = trim($_POST['title']);
	    		$content = trim($_POST['content']);
		    	edit_poll($poll_id, $title, $content);

	    		// $count_fields = count($_POST);
	    		foreach ($_POST as $k => $v) {
	    			$k = preg_replace("/^answ-/i", "", $k);
	    			$v = trim($v);
	    			if( !empty($v) ){
	    				$answs[$k] = $v; 
	    			}  			
	    		}
	    		unset($answs['title']);
	    		unset($answs['content']);
	    		unset($answs['edit']);
	    		// echo "<pre>";
	    		// print_r($answs);
	    		// echo "</pre>";
	    		
				edit_answs($poll_id, $answs);
	    	}
		  
			$title = get_one_poll_title($poll_id);
			$answers = get_one_poll_answers($poll_id);
	?>
	 <div class='wrap'>
	  <div id="for_message"><h1><?php _e( 'edit_poll', 'simply_polls' );?></h1></div>
       <form method="post" action="<?php echo $base_page;?>&amp;mode=edit&amp;id=<?php echo $poll_id;?>">
		<table class="form-table table1">
		<tr>
			<th scope="row"><label for="title"><?php _e( 'title_poll', 'simply_polls' );?></label></th>
			<td><input name="title" type="text" id="title" maxlength="200" class="regular-text" value="<?php if($title[0]['title']) echo $title[0]['title'];?>" /></td>
			<td> </td>
		</tr>
		<tr>
			<th scope="row"><label for="content"><?php _e( 'custom_text', 'simply_polls' );?></label></th>
			<td><textarea rows="1" cols="45" name="content" id="excerpt" class="regular-text" /><?php if($title[0]['content']) echo $title[0]['content'];?></textarea></td>
			<td> </td>
		</tr>
		<?php if($answers):?>
			<?php $i = 1; ?>
			<?php foreach ($answers as $k => $v):?>
				<tr class="answ" id="del_answ_<?php if($v['answ_id']) { echo $v['answ_id']; } else { echo $i; } ?>">
					<th scope="row"><label for="answ1"><?php _e( 'l_answer', 'simply_polls' );?> <?php echo $i;?></label></th>
					<td><input name="answ<?php if($v['answ_id']) { echo '-'.$v['answ_id']; } else { echo $i; } ?>" type="text" id="answ<?php echo $i;?>" class="regular-text" value="<?php echo $v['answ'];?>" /></td>
					<td><input type="submit" onclick="poll_answ(<?php echo $poll_id;?>, <?php echo $v['answ_id'];?>); return false;" name="del_answ" class="button" value="<?php _e( 'del_poll', 'simply_polls' );?>"></td>
				</tr>
			<?php $i++;?>
			<?php endforeach;?>
		<?php endif;?>
		</table>

		<table class="form-table">
			<tr>
				<th scope="row"> </th>
				<td><input type="submit" name="add_answ" id="add_answ" class="button" value="<?php _e( 'add_answer', 'simply_polls' );?>"></td>
			</tr>
		</table>
		
		<p class="submit">
		<input type="submit" name="edit" id="submit" class="button button-primary" value="<?php _e( 'save', 'simply_polls' );?>">
		<input type="submit" name="cancel" class="button action" value="<?php _e( 'cancel', 'simply_polls' );?>" onclick="window.history.go(-1);">
		</p>
		</form>	
		</div>	
    <?php
		
		break;
	default:
			echo "<div class='wrap'>
			<div id='for_message'><h1>".__( 'polls', 'simply_polls' )."</h1><a href='$base_page&amp;mode=add' class='page-title-action'>".__( 'add_poll', 'simply_polls' )."</a></div>
			<div class='short-poll'>".__( 'how_add_code', 'simply_polls' )."</div>";
			// Для добавления опроса используйте шорткод <span>[alex_poll id=\"1\"]</span> где id - номер опроса	

			$pagination_params = pagination_params();
			$all_polls = get_all_polls();
			?>
			<?php if($all_polls):?>		
					<table class="wp-list-table widefat fixed striped posts">
						<thead>
							<tr>
								<th class="manage-column column-author">ID</th>
								<th><?php _e( 'title_poll', 'simply_polls' )?></th>
								<th class="manage-column column-author"><?php _e( 'total_vote', 'simply_polls' )?></th>
								<th class="manage-column column-author"> </th>
								<th class="manage-column column-author"> </th>
								<th class="manage-column column-author"> </th>
							</tr>
						</thead>
						<tbody id="the-list">					
							<?php foreach ($all_polls as $poll):?>
							<?php
								$answers = get_one_poll_answers($poll['id']);
								// print_r($answers);
								$count_votes = 0;
								foreach ($answers as $k => $v) {
									$count_votes = $count_votes + $v['votes'];
								}
							?>
							<tr id="delete_poll_<?php echo $poll['id'];?>">
								<td class="author column-author"><?php echo $poll['id'];?></td>
								<td><?php echo $poll['title'];?></td>
								<td class="author column-author"><?php echo $count_votes;?></td>
								<td class="author column-author"><a href="<?php echo $base_page;?>&amp;mode=results&amp;id=<?php echo $poll['id'];?>"><?php _e( 'results', 'simply_polls' )?></a></td>
								<td class="author column-author"><a href="<?php echo $base_page;?>&amp;mode=edit&amp;id=<?php echo $poll['id'];?>"><?php _e( 'edit', 'simply_polls' )?></a></td>
								<td class="author column-author"><a href="#del_poll" onclick="delete_poll(<?php echo $poll['id'];?>);" ><?php _e( 'del_poll', 'simply_polls' )?></a</td>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				<?php if( $pagination_params['count_pages'] > 1 ): ?>
				<div class="pagination">
					<?php echo pagination($pagination_params['page'], $pagination_params['count_pages']); ?>
				</div>
				<?php endif; ?>
				
			<?php else:?>
				 <!-- <p>Опросы еще не созданы</p>		 -->
				 <p><?php _e( 'no_polls', 'simply_polls' );?></p>		
			<?php endif;?>

			<?php
		    echo "</div></div>";	
	}

}

// Handler ajax data on admin side
add_action( 'wp_ajax_admin-poll', 'my_action_callback' );
function my_action_callback() {

		if ( !wp_verify_nonce( $nonce, 'admin_nonce' ) ) exit;

		global $wpdb;
		if($_POST['poll_id'] > 0) {
		   del_one_poll( $_POST['poll_id'] );
		 }
		wp_die();

}

add_action( 'wp_ajax_admin-del-answ', 'cb_del_one_answ' );
function cb_del_one_answ() {

		if ( !wp_verify_nonce( $nonce, 'admin_nonce' ) ) exit;

		global $wpdb;
		if( (int)$_POST['answ_id'] > 0 and (int)$_POST['poll_id'] > 0 ) {
		   del_one_answ( $_POST['poll_id'], $_POST['answ_id'] );
		 }
		wp_die();

}


/* **********  фронтенд ************ */


function cb_alex_poll( $atts ) {

   //[alex_poll id="1"]

	// подключение js скриптов во фронтенде,только на страницах где есть шорткод [alex_poll]

	wp_enqueue_script( 'frontend-js', plugins_url( 'js/alex_frontend.js', __FILE__ ), array('jquery'),'', true );
	wp_localize_script( 'frontend-js', 'ajax_obj', array('url' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('vote-nonce'),'loader' => plugins_url( 'preloader.gif', __FILE__ )));
   
   $ip = $_SERVER['REMOTE_ADDR'];
   $poll_id = (int)$atts['id'];
   $check_ip = check_ip($ip, $poll_id);

   if(!$check_ip) {
			$title = get_one_poll_title($poll_id);
			// print_r($title);
			$answers = get_one_poll_answers($poll_id);
			// print_r($answers);
			if(!empty($title)){
				$html = "<div id='alex-poll-wrap' class='poll_id_".$poll_id."'>";
				$html .= "<div class='poll-alex'>";
				$html .= "<h3>".$title[0]['title']."</h3>";
			    if( $title[0]['content'] ) $html .= "<p class='dop-text'>".$title[0]['content']."</p>";
			 }
			if( !empty($answers) ){
				$html .= "<form action='' method='post'>";
				foreach ($answers as $k => $v) {
					$html .= "<p><input type='radio' name='answ' value='".$v['answ_id']."'/>".$v['answ']."</p>";
				}
				$html .= '<input type="submit"  id="add_vote" onclick="add_vote_user('.$poll_id.'); return false;" value="'.__('btn_vote', 'simply_polls').'" />
				</form>';
				$html .= "</div>";
				$html .= "</div>";
			}
			return $html;

	}else{
			// echo "<p>Вы уже голосовали!</p>";
			$title = get_one_poll_title($poll_id);
			$answers = get_one_poll_answers($poll_id);
			if(!empty($title)){
				$html = "<div class='poll-alex' data-poll-id='".$poll_id."'>";
				$html .= "<h3>".$title[0]['title']."</h3>";
				if( $title[0]['content'] ) $html .= "<p class='dop-text'>".$title[0]['content']."</p>";
			}

			if( !empty($answers) ){

				$count_votes = 0;
				foreach ($answers as $k => $v) {
					$count_votes = $count_votes + $v['votes'];
				}

				foreach ($answers as $k => $v) {			
					$percent = round( (100/$count_votes) * $v['votes'] );
					$html .= "<p>".$v['answ']." - (".$percent." %, ".$v['votes']." ".__('votes', 'simply_polls')." )</p>";
					if($v['votes']>0) $html .= "<p class='vote-bar' style='width:".$percent."%;'> </p>";
				}
				$html .= "<p class='total-vote'>".__('total_vote', 'simply_polls')." - ".$count_votes."</p>";
				$html .= "</div>";

			}

	 		return $html;	
	}
}

// создание шорткода для фронтенда части
add_shortcode('alex_poll', 'cb_alex_poll');

add_action('wp_ajax_add-vote', 'alex_handler_ajax');
add_action('wp_ajax_nopriv_add-vote', 'alex_handler_ajax');

function alex_handler_ajax(){

	// print_r($_POST);
	$nonce = $_POST['nonce'];
	// проверяем nonce код, если проверка не пройдена прерываем обработку
	 if ( !wp_verify_nonce( $nonce, 'vote-nonce' ) ) exit;

	$answ_id = (int)$_POST['answ_id'];
	$poll_id = (int)$_POST['poll_id'];
	add_vote($poll_id, $answ_id);
	$ip = $_SERVER['REMOTE_ADDR'];
	add_ip($ip, $poll_id);

	$title = get_one_poll_title($poll_id);
	$answers = get_one_poll_answers($poll_id);
	if(!empty($title)){
		$html = "<div class='poll-alex' data-poll-id='".$poll_id."'>";
		$html .= "<h3>".$title[0]['title']."</h3>";
		if( $title[0]['content'] ) $html .= "<p class='dop-text'>".$title[0]['content']."</p>";
	}

	if( !empty($answers) ){

		$count_votes = 0;
		foreach ($answers as $k => $v) {
			$count_votes = $count_votes + $v['votes'];
		}

		foreach ($answers as $k => $v) {			
			$percent = round( (100/$count_votes) * $v['votes'] );
			$html .= "<p>".$v['answ']." - (".$percent." %, ".$v['votes']." ".__('votes', 'simply_polls')." )</p>";
			if($v['votes']>0) $html .= "<p class='vote-bar' style='width:".$percent."%;'> </p>";
		}
		$html .= "<p class='total-vote'>".__('total_vote', 'simply_polls')." - ".$count_votes."</p>";
		$html .= "</div>";

	}

	 echo $html;	
}

// поддержка шорткова в виджете Текст
add_filter('widget_text', 'do_shortcode');

/* **********  фронтенд ************ */

