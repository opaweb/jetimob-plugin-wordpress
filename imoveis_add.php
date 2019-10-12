<?php 

define('WP_USE_THEMES', false);
(string)$caminho = dirname(__FILE__);
require_once( $caminho.'/../../../wp-blog-header.php' );
$jetimob_options = get_option( 'jetimob_option_name' ); 


$headers = array('http'=>array('method'=>'GET','header'=>'Content: type=application/json \r\n'.'$agent \r\n'.'$hash'));

$context=stream_context_create($headers);


$arquivo = $caminho.'/jetimob.json';

$str = file_get_contents($arquivo,FILE_USE_INCLUDE_PATH,$context);

$str1=utf8_encode($str);

$str1=json_decode($str1, true);

//echo $str1;

foreach($str1 as $key=>$value)

{



//Campo observações

		if ($value['observacoes'] == null) {

			$observacoes = 'Em breve mais detalhes sobre este imóvel.';

		}

		else {

			$observacoes = $value['observacoes'];

		}


		//título

		if ($value['tipo'] == "Terreno" || $value['tipo'] == "Box" ) {

			$tit_tipo = $value['tipo'];			

		}
		else { 
			$tit_tipo = $value['subtipo'];
		}
		
		if (is_null($value['endereco_bairro'])){
		$titulo = $tit_tipo.', '.$value['endereco_cidade'];
		}

		else {
			$titulo = $tit_tipo.', '.$value['endereco_bairro'];
		}
		
		
		if(!is_null($value['condominio_nome'])){
			$slug = $value['codigo'].' '.$tit_tipo.' '.$value['condominio_nome'].' '.$value['endereco_cidade'];
		}
		else {
			$slug = $value['codigo'].' '.$tit_tipo.' '.$value['endereco_cidade'];	
		}

        

        $querystr = "

		    SELECT $wpdb->posts.* 

		    FROM $wpdb->posts, $wpdb->postmeta

		    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 

		    AND $wpdb->postmeta.meta_key = 'codigo'

		    AND $wpdb->posts.post_status = 'publish' 

		    AND $wpdb->posts.post_type = 'imovel' 

		    AND $wpdb->postmeta.meta_value = '".$value['codigo']."'	        

 		";

 		$results = $wpdb->get_results($querystr);

 		//var_dump($results);

 		if (count($results)> 0) {

 			$existe = 1; 			

 			}

 		

 		else {

 			$existe = 0;

 			$import_data = array(

		    'post_type' => 'imovel',

		    'post_status'	=> 'publish',

		    'post_title'	=> $titulo,

		    'post_name'		=> $slug,

		    'post_date'		=> '1970-01-01',

		    'post_content'  => $observacoes,

		    'post_author'	=> 1,

		    

		    );

 			$new_post = wp_insert_post($import_data, true);

 			if(!is_wp_error($new_post)){

			  //the post is valid

			}else{

			  //there was an error in the post insertion, 

			  echo $new_post->get_error_message();

			}

			$postid = $new_post;

			update_post_meta($postid, 'codigo', $value['codigo']);

       	

 		}

 		date_default_timezone_set('America/Sao_Paulo');

 		$date = date('d/m/Y h:i:s a', time());
		
 		if($existe = 0) { echo $date.' - Inclusão - Imóvel: '.$value['codigo'].' '.PHP_EOL;}

 		elseif ($existe = 1) {echo $date.' - Existente - Imóvel: '.$value['codigo'].' '.PHP_EOL;}

 		else {}



		unset($observacoes);

		unset($slug);

		unset($titulo);

		//unset($postid);

}
?>