<?php 

define('WP_USE_THEMES', false);
(string)$caminho = dirname(__FILE__);
require_once( $caminho.'/../../../wp-blog-header.php' );
$jetimob_options = get_option( 'jetimob_option_name' ); 
$theme = $jetimob_options['theme'];
require('themes/'.$theme.'/fields.php');
global $user_ID, $wpdb;

$headers = array('http'=>array('method'=>'GET','header'=>'Content: type=application/json \r\n'.'$agent \r\n'.'$hash'));

$context=stream_context_create($headers);
$arquivo = $caminho.'/jetimob.json';
$str = file_get_contents($arquivo,FILE_USE_INCLUDE_PATH,$context);

$str1=utf8_encode($str);

$str1=json_decode($str1, true);

//echo $str1;

foreach($str1 as $key=>$value)

{

$manter[] = $value['codigo'];

}

$str2 = $str1;

foreach($str1 as $key=>$value) {

$check = get_posts(array(

	   'post_type' => 'imovel', 

	   'post_status' => 'published',       

	   'meta_query' => array(      

	      array(

	         'key'     => $field_codigo,

	         'value'   => $manter,

	         'compare' => 'NOT IN'

	      )      

	   ),

	));	



date_default_timezone_set('America/Sao_Paulo');

$postid = $post->ID;
$codigo = $value['codigo'];
	foreach ($check as $post) {

		wp_delete_post( $post->ID, $force_delete );
		$date = date('d/m/Y h:i:s a', time());
		echo $date.' - deletado post '.$postid.' - Código:'.$codigo.PHP_EOL;

	}

		



		

}

unset($check);
unset($postid);
unset($codigo);

?>