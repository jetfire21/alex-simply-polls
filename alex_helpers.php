<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
$wpdb->table_name = $wpdb->prefix .  "plg_sp_polls";
$wpdb->table2_name = $wpdb->prefix . "plg_sp_answs"; 
$wpdb->table3_name = $wpdb->prefix . "plg_sp_ip";  

function pagination_params(){
	global $wpdb;

	$perpage = 10;
	// кол-во записей
	// $table_name = $wpdb->prefix . "plg_simply_polls"; 
	$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name");
	// необходимое кол-во страниц
	$count_pages = ceil($count / $perpage);
	// минимум 1 страница
	if( !$count_pages ) $count_pages = 1;
	// получение текущей страницы
	if( isset($_GET['paged']) ){
		$page = (int)$_GET['paged'];
		if( $page < 1 ) $page = 1;
	}else{
		$page = 1;
	}
	// если запрошенная страница больше максимума
	if( $page > $count_pages ) $page = $count_pages;
	// начальная позиция для запроса
	$start_pos = ($page - 1) * $perpage;

	$pagination_params = array(
		'page' => $page,
		'count' => $count,
		'count_pages' => $count_pages,
		'perpage' => $perpage,
		'start_pos' => $start_pos
	);
	return $pagination_params;
}


function pagination($page, $count_pages){
	// << < 3 4 5 6 7 > >>
	$back = null; // ссылка НАЗАД
	$forward = null; // ссылка ВПЕРЕД
	$startpage = null; // ссылка В НАЧАЛО
	$endpage = null; // ссылка В КОНЕЦ
	$page2left = null; // вторая страница слева
	$page1left = null; // первая страница слева
	$page2right = null; // вторая страница справа
	$page1right = null; // первая страница справа

	$uri = "?";
	if( $_SERVER['QUERY_STRING'] ){
		foreach ($_GET as $key => $value) {
			if( $key != 'paged' ) $uri .= "{$key}=$value&amp;";
		}
	}

	if( $page > 1 ){
		$back = "<a class='nav-link' href='{$uri}paged=" .($page-1). "'>".__( 'back', 'simply_polls' )."</a>";
	}
	if( $page < $count_pages ){
		$forward = "<a class='nav-link' href='{$uri}paged=" .($page+1). "'>".__( 'next', 'simply_polls' )."</a>";
	}
	if( $page > 3 ){
		$startpage = "<a class='nav-link' href='{$uri}paged=1'>В начало</a>";
	}
	if( $page < ($count_pages - 2) ){
		$endpage = "<a class='nav-link' href='{$uri}paged={$count_pages}'>В конец</a>";
	}
	if( $page - 2 > 0 ){
		$page2left = "<a class='nav-link' href='{$uri}paged=" .($page-2). "'>" .($page-2). "</a>";
	}
	if( $page - 1 > 0 ){
		$page1left = "<a class='nav-link' href='{$uri}paged=" .($page-1). "'>" .($page-1). "</a>";
	}
	if( $page + 1 <= $count_pages ){
		$page1right = "<a class='nav-link' href='{$uri}paged=" .($page+1). "'>" .($page+1). "</a>";
	}
	if( $page + 2 <= $count_pages ){
		$page2right = "<a class='nav-link' href='{$uri}paged=" .($page+2). "'>" .($page+2). "</a>";
	}

	return $startpage.$back.$page2left.$page1left.'<a class="active-page">'.$page.'</a>'.$page1right.$page2right.$forward.$endpage;
}

function get_all_polls(){
	global $wpdb;
	$pagination_params = pagination_params();
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name LIMIT {$pagination_params['start_pos']}, {$pagination_params['perpage']}", ARRAY_A);
}

function add_poll($title, $content){
	global $wpdb;
	$pagination_params = pagination_params();

	$add_poll = $wpdb->insert( 
			$wpdb->table_name, 
			array( 
				'title' => $title, 
				'content' => $content 
			), 
			array( 
				'%s', 
				'%s' 
			) 
		);
	return $add_poll;
}

function add_answs($answs){
	global $wpdb;
	$last_id = $wpdb->get_var("SELECT MAX(`id`) FROM $wpdb->table_name");

	foreach ($answs as $k => $v) {
		$wpdb->insert( 
				$wpdb->table2_name, 
				array( 
					'id_quest' => $last_id, 
					'answ' => $v 
				), 
				array( 
					'%d', 
					'%s' 
				) 
			);
	};
}

function del_one_poll($id){
	global $wpdb;

	$wpdb->delete( $wpdb->table_name, array( 'id' => $id ), array("%d") );
	$wpdb->delete( $wpdb->table2_name, array( 'id_quest' => $id ), array("%d") );
}


// Таблица: p1zxv_plg_polls_answs

// CREATE TABLE IF NOT EXISTS `p1zxv_plg_polls_answs` (
//   `answ_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//   `id_quest` int(10) unsigned NOT NULL,
//   `answ` varchar(200) NOT NULL,
//   `votes` int(10) unsigned NOT NULL,
//   PRIMARY KEY (`answ_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

function get_one_poll_title($id){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name WHERE id=$id", ARRAY_A);
}

function get_one_poll_answers($id){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table2_name WHERE id_quest=$id", ARRAY_A);
}

function edit_poll($id, $title, $content){
	global $wpdb;
	$wpdb->update(
	    $wpdb->table_name,
		    array(
		        'title' => $title,
		        'content' => $content
		    ),
		    array(
		        'id' => $id,

		    ),
		    array(
		        '%s',
		        '%s'
		    ),
		    array(
		        '%d'
		    )
		);
}

function edit_answs($poll_id, $answs){
	global $wpdb;
	foreach($answs as $k => $v) {
		if( !is_int($k)) {
		   	$wpdb->insert( 
				$wpdb->table2_name, 
				array( 
					'id_quest' => $poll_id, 
					'answ' => $v 
				), 
				array( 
					'%d', 
					'%s' 
				) 
			);
		}else{
			$wpdb->query($wpdb->prepare("UPDATE $wpdb->table2_name SET answ = '%s' WHERE id_quest ='%d' AND answ_id = '%d'", $v, $poll_id, $k));
		}
	}
}

function del_one_answ($poll_id, $answ_id){
	global $wpdb;
	$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->table2_name WHERE id_quest ='%d' AND answ_id = '%d'", $poll_id, $answ_id));
}

function add_vote($poll_id, $answ_id){
	global $wpdb;
    $votes = $wpdb->get_var($wpdb->prepare("SELECT votes FROM $wpdb->table2_name  WHERE id_quest ='%d' AND answ_id = '%d'", $poll_id, $answ_id));
    $votes++;
	$wpdb->query($wpdb->prepare("UPDATE $wpdb->table2_name SET votes = '%d' WHERE id_quest ='%d' AND answ_id = '%d'", $votes, $poll_id, $answ_id));
}

function add_ip($ip, $poll_id){
	global $wpdb;
	$add_poll = $wpdb->insert( 
		$wpdb->table3_name, 
		array( 
			'ip' => $ip,
			'id_poll' => $poll_id
		), 
		array( 
			'%s',
			'%d'
		) 
	);
}

function check_ip($ip, $poll_id){
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare("SELECT ip FROM $wpdb->table3_name  WHERE ip='%s' AND id_poll='$poll_id'", $ip, $poll_id) );
}
