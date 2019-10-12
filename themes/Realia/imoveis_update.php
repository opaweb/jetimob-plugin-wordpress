<?php 

/*
define('WP_USE_THEMES', false);
date_default_timezone_set('America/Sao_Paulo');
(string)$caminho = dirname(__FILE__);
//require_once( $caminho.'/../../../../wp-blog-header.php' );
//$jetimob_options = get_option( 'jetimob_option_name' ); 
//$theme = $jetimob_options['theme'];
//require('fields.php');
global $user_ID, $wpdb;

*/


foreach($str1 as $key=>$value) {
 		$check = get_posts(array(

	   'post_type' => 'property', 

	   'post_status' => 'published',       

	   'meta_query' => array(      

	      array(

	         'key'     => $field_codigo,

	         'value'   => $value['codigo'],

	         'compare' => '='

	      	)      

		   ),
		));	


	

		foreach ($check as $post) {	

			if($value['updated_at'] == $post->post_date) { 
				echo 'OK - '.$post->ID; echo PHP_EOL;
			}


		else { 


			$postid = $post->ID;

			if (is_null($value['valor_venda']) && is_null($value['valor_locacao'])) {

				$preco_imovel = "Consulte";

			}

			elseif(!is_null($value['valor_venda']) && !is_null($value['valor_locacao'])){

				$preco_imovel = $value['valor_venda'];

				$preco_imovel2 = $value['valor_locacao'];

				$preco_apos = "Aluguel";			

			}

			elseif(!is_null($value['valor_venda']) && is_null($value['valor_locacao'])){

				$preco_imovel = $value['valor_venda'];

			}

			elseif(is_null($value['valor_venda']) && !is_null($value['valor_locacao'])){

				$preco_imovel = $value['valor_locacao'];

			}


			if ($value['contrato'] == 'Compra') {
				$contrato = "SALE";
			}
			elseif ($value['contrato'] == 'Locação') {
				$contrato = "RENT";
			}
			elseif ($value['contrato'] == 'Compra,Locação') {
				$contrato = "RENT,SALE";
			}
		


			//tipo de imovel



			if($value['subtipo'] == "Casa" || $value['subtipo'] == "Prédio") {

				$tipo_imovel = $value['subtipo'].' '.$value['tipo'];

			}

			elseif ($value['tipo'] == "Terreno" || $value['tipo'] == "Box" ) {

				$tipo_imovel = $value['tipo'];

			}

			else {

				$tipo_imovel = $value['subtipo'];

			}		

        

			$update_data = array(

 					'ID'			=> $postid,

				    //'post_title'	=> $titulo,

				    //'post_name'		=> $slug,

				    'post_date'	=> $value['updated_at'],

				    'post_modified'	=> $value['updated_at'],

				    //'post_content'  => $observacoes,

				    //'post_author'	=> 2,

				    

				    );

			$atualiza = wp_update_post($update_data, true);

			if (is_wp_error($atualiza)) {

					$errors = $atualiza->get_error_messages();

					foreach ($errors as $error) {

						echo $error;

					}

			}

			wp_set_object_terms($postid, $tipo_imovel, 'property_types');

	 		//Definições de cidade e bairro
			$cad_cidade = $value['endereco_cidade'];
			$cad_bairro = $value['endereco_bairro'];
			if($value['endereco_cidade'] == null) { $cad_cidade = "Não Informado"; }
			if($value['endereco_bairro'] == null) { $cad_bairro = "Não Informado"; }
	
			// Verifica se a cidade existe
			$cidade_term = term_exists( $cad_cidade, 'locations', 0 );

			// Se a cidade não está cadastrada, cria
			if ( !$cidade_term ) {
			    $cidade_term = wp_insert_term( $cad_cidade, 'locations', array( 'parent' => 0 ) );
			}

			// Verifica se o bairro existe
			$bairro_term = term_exists( $cad_bairro, 'locations', $cidade_term['term_taxonomy_id'] );

			// Se o bairro nao está cadastrado, cria
			if ( !$bairro_term ) {
			    $bairro_term = wp_insert_term( $cad_bairro, 'locations', array( 'parent' => $cidade_term['term_taxonomy_id'] ) );
			}

			wp_set_object_terms($postid, array($cad_cidade, $cad_bairro), 'locations');


	 		//update_post_meta($postid, 'property_id', $value['codigo']);

	 		update_post_meta($postid, 'property_year_built', $value['entrega_ano']);

	 		update_post_meta($postid, 'property_entrega_mes', $value['entrega_mes']);	 		
	
 			update_post_meta($postid, 'property_price', $preco_imovel);

 			update_post_meta($postid, 'property_sec_price', $preco_imovel2);

 			//update_post_meta($postid, 'fave_property_price_postfix', $preco_apos); 		

 			update_post_meta($postid, 'valor_venda', $value['valor_venda']);

 			update_post_meta($postid, 'valor_locacao', $value['valor_locacao']);

 			update_post_meta($postid, 'valor_temporada', $value['valor_temporada']);

 			update_post_meta($postid, 'valor_venda_visivel', $value['valor_venda_visivel']);

	 		update_post_meta($postid, 'valor_locacao_visivel', $value['valor_locacao_visivel']);

	 		update_post_meta($postid, 'valor_temporada_visivel', $value['valor_temporada_visivel']);

	 		update_post_meta($postid, 'property_pais', 'BR');

	 		update_post_meta($postid, 'property_estado', $value['endereco_estado']);

	 		update_post_meta($postid, 'property_cidade', $value['endereco_cidade']);

	 		update_post_meta($postid, 'property_bairro', $value['endereco_bairro']);

	 		update_post_meta($postid, 'property_zip', $value['endereco_cep']);

	 		update_post_meta($postid, 'property_beds', $value['dormitorios']);

	 		update_post_meta($postid, 'property_baths', $value['banheiros']);

	 		update_post_meta($postid, 'property_garages', $value['garagens']);

	 		update_post_meta($postid, 'property_suites', $value['suites']);
	 
	 		update_post_meta($postid, 'property_home_area', $value['area_util']);

	 		update_post_meta($postid, 'property_size_prefix', 'm²');	

	 		update_post_meta($postid, 'id_condominio', $value['id_condominio'] );

	 		update_post_meta($postid, 'property_alto_padrao', $value['alto_padrao'] );

	 		update_post_meta($postid, 'property_tipo_construcao', $value['tipo_construcao'] );

	 		update_post_meta($postid, 'property_tipo_piso', $value['tipo_piso'] );

	 		update_post_meta($postid, 'property_posicao', $value['posicao'] );

	 		update_post_meta($postid, 'property_posicao_solar', $value['posicao_solar'] );

	 		//update_post_meta($postid, 'id_corretor', $value['id_corretor'] );

	 		if ($value['destaque'] == "Em destaque") {

				update_post_meta( $postid, 'property_featured', 1 );

			}	

		
			//visualização no mapa

			if($value['endereco_visivel_no_site'] == "-2"){

				$end_mapa = $value['endereco_estado'];

			}

			elseif($value['endereco_visivel_no_site'] == "-1"){

				$end_mapa = $value['endereco_cidade'].', '.$value['endereco_estado'];

			}

			elseif($value['endereco_visivel_no_site'] == "0"){

				$end_mapa = $value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];
			}

			elseif($value['endereco_visivel_no_site'] == "1"){

				$end_mapa = $value['endereco_logradouro'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

				if(!is_null($value['condominio_nome'])) {

					update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

				}	

			}

			elseif($value['endereco_visivel_no_site'] == "2"){

				$end_mapa = $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

				if(!is_null($value['condominio_nome'])) {

					update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

				}

			}

			elseif($value['endereco_visivel_no_site'] == "3"){

				$end_mapa =  $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

				if(!is_null($value['condominio_nome'])) {

					update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

				}

				if(!is_null($value['andar'])) {

					update_post_meta($postid, 'andar', $value['andar'] );

				}		

			}

			elseif($value['endereco_visivel_no_site'] == "4") {

				$end_mapa =  $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

				if(!is_null($value['condominio_nome'])) {

					update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

				}

				if(!is_null($value['andar'])) {

					update_post_meta($postid, 'andar', $value['andar'] );

				}

				if(!is_null($value['endereco_complemento'])) {

					update_post_meta($postid, 'endereco_complemento', $value['endereco_complemento'] );

				}	

			}


			update_post_meta($postid, 'geoposicionamento_visivel', $value['geoposicionamento_visivel'] );

			if ($value['geoposicionamento_visivel'] == 1) {

				$calcula = true;

				$latitude = $value['latitude'];

				$longitude = $value['longitude'];
				
				

			}

			elseif ($value['geoposicionamento_visivel'] == 2) {

				$calcula = true;

				$latitude = $value['latitude'];

				$longitude = $value['longitude'];		

			}

			update_post_meta( $postid, 'property_map_location_latitude', $latitude );

			update_post_meta( $postid, 'property_map_location_longitude', $longitude );

			update_post_meta($postid, 'property_address', $end_mapa );

		
			$tipomapa = $value['geoposicionamento_visivel'];

	      	update_post_meta($post->ID, 'tipo_mapa', $tipomapa);

			

			update_post_meta($postid, 'endereco_referencia', $value['endereco_referencia'] );

			update_post_meta($postid, 'endereco_visivel', $value['endereco_visivel'] );

	

			if(!is_null($value['endereco_complemento'])) {
				update_post_meta($postid, 'property_endereco_complemento', $value['endereco_complemento'] );
			}
 		
	  		wp_set_object_terms($postid,  explode(',', $value['imovel_comodidades']), 'amenities',true);

	 		wp_set_object_terms($postid,  explode(',', $value['condominio_comodidades']), 'property_condominio_comodidades',true);

	 		wp_set_object_terms($postid, $value['situacao'], 'statuses');

	 		update_post_meta($postid, 'property_status', $value['status'] );

	 		update_post_meta($postid, 'valor_condominio', $value['valor_condominio']);	

	 		update_post_meta($postid, 'condominio_visivel', $value['valor_condominio_visivel']); 	
 		

	 		if ($value['valor_iptu_visivel'] == "1") {

	 			$iptu = 'R$ '.$value['valor_iptu'];

	 			$tit_iptu = 'IPTU';

	 			$is_iptu = 1; 	 			
	 			

	 		} 		

			update_post_meta($postid, 'valor_iptu', $value['valor_iptu']);

			update_post_meta($postid, 'iptu_visivel', $value['valor_iptu_visivel']);
 				


 			$value['mobiliado'] = $mobilia;



	 		if ($value['mobiliado'] == 0 || ! is_null($value['mobiliado'])) {

			    $tit_mobiliado = null;

	 				$mobiliado = null;

			} 
			elseif ($value['mobiliado'] != 0) {

			    	$is_mob = 1;

	 				$tit_mobiliado = 'Mobiliado';

	 				if ($value['mobiliado'] == 1){

	 					$mobiliado = 'Sim';

	 				} 	

	 				elseif ($value['mobiliado'] == 2){

	 					$mobiliado = 'Semimobiliado';

	 				} 			

			} 

			update_post_meta($postid, 'property_mobiliado', $mobiliado);

			if ($value['financiavel'] == 1) {

				$financiavel = "Sim";
				$tit_financiavel = 'Financiável';
				$is_financiavel = 1;
			}

			elseif ($value['financiavel'] == 2) {
				$tit_financiavel = 'Financiável';
				$financiavel = 'Minha Casa Minha Vida';
				$is_financiavel = 1;
			}		

			update_post_meta($postid, 'property_financiavel', $financiavel);	

		

	 		if (!is_null($value['distancia_mar'])) {

	 			$mar = $value['distancia_mar'];

	 			$tit_mar = 'Distância do mar';

	 			$is_mar = 1; 

	 			update_post_meta($postid, 'property_distancia mar', $mar);			

	 		} 

			if ($value['exclusividade'] == 1) {

	 			$exclusivo = 'Sim';

	 			$tit_exclusivo = 'Exclusividade';

	 			$is_exclusivo = 1;

	 			update_post_meta($postid, 'property_exclusividade', $exclusivo);				

	 		}


	 		if ($value['condominio_tipo'] == 1) {

	 			$ctipo = 'Sim';

	 			$tit_ctipo = 'Tipo de Condomínio';

	 			$is_ctipo = 1;

	 			update_post_meta($postid, 'property_condominio_tipo', $cfechado);		

	 		}



	 		if ($value['condominio_fechado'] == 1) {

	 			$cfechado = 'Sim';

	 			$tit_cfechado = 'Cond. Fechado';

	 			$is_cfechado = 1;

	 			update_post_meta($postid, 'property_condominio_fechado', $cfechado);		

	 		}


 		
	 		if (!is_null($value['terreno_largura']) && !is_null($value['terreno_comprimento'])) {

	 			$is_largura = 1;
	 			$tit_largura = "Largura";
	 			$largura = $value['terreno_largura'];

	 			$is_comprimento = 1;
	 			$tit_comprimento = "Comprimento";
	 			$comprimento = $value['terreno_comprimento'];

	 			update_post_meta($postid, 'property_lot_dimensions', $largura.' x '.$comprimento);
	 		} 	

	 		update_post_meta($postid, 'property_area_total', $value['area_total']);

	 		update_post_meta($postid, 'property_total_area', $value['rural_area_aravel']);



	 		delete_post_meta($postid, 'property_gallery', '');

	 
	 		delete_post_meta($postid, 'property_thumbnail', ''); 



 			$imagens = $value['imagens'];

	 		if (!is_null($imagens)) {

	 			//$ibagens = $value['imagens'][0]['link'];

	 		 	foreach ($imagens as $imagem) {

			 		$galeria[] = $imagem['link'];			 		

			 	}

			 	add_post_meta($postid, 'property_gallery', maybe_serialize($galeria)); 

		 		add_post_meta($postid, 'property_thumbnail', $galeria[0]);

		 		add_post_meta($postid, 'fifu_image_url', $galeria[0]);

		 		update_post_meta($postid, '_thumbnail_id', '-1');

		 		Generate_Featured_Image( $galeria[0],   $postid ); 		

	 		} 

 			delete_post_meta($postid, 'property_plans', ''); 

 			$plantas = $value['plantas'];

		 	if (!is_null($plantas)) {

		 		foreach($plantas as $key=>$planta) { 			

			       	$addplanta[] = $planta['link'] ;       	

			    
			add_post_meta($postid, 'property_plans', maybe_serialize($addplanta));  

		 		}
		 	}

     

    		delete_post_meta($postid, 'property_tour_360', '');

		    if (!is_null($value['tour360'])) {

				add_post_meta($postid, 'property_tour_360', $value['tour360'][0]);		
			}



    		delete_post_meta($postid, 'property_video', '');

    

		    if (!is_null($value['videos'])) {

				add_post_meta($postid, 'property_video', maybe_serialize($value['videos'][0]['link'])); 	
		 	}

			//unset(var);
			unset($patterns);
			unset($replacements);
			unset($verifica_tour);
			unset($ibagens);
			unset($res_cor);
			unset($corretor);
			unset($rotulos);
			unset($plantas);
			unset($addplanta);
			unset($t);
			unset($is_novo);
			unset($tit_novo);
			unset($novo);
			unset($tit_construcao);
			unset($tit_situacao);
			unset($tit_areaprivativa);
			unset($tit_areatotal);
			unset($tit_aravel);
			unset($tit_mar);
			unset($tit_posicao);
			unset($tit_posicaosolar);
			unset($tit_piso);
			unset($tit_condominio);
			unset($tit_iptu);
			unset($tit_mob);
			unset($tit_financiavel);
			unset($tit_altopadrao);
			unset($tit_exclusivo);
			unset($tit_cfechado);
			unset($tit_entrega);
			unset($construcao);
			unset($situacao);
			unset($areaprivativa);
			unset($areatotal);
			unset($aravel);
			unset($mar);
			unset($posicao);
			unset($posicaosolar);
			unset($piso);
			unset($condominio);
			unset($iptu);
			unset($mob);
			unset($financiavel);
			unset($altopadrao);
			unset($exclusivo);
			unset($cfechado);
			unset($entrega);
			unset($is_construcao);
			unset($is_situacao);
			unset($is_areaprivativa);
			unset($is_areatotal);
			unset($is_aravel);
			unset($is_mar);
			unset($is_posicao);
			unset($is_posicaosolar);
			unset($is_piso);
			unset($is_condominio);
			unset($is_iptu);
			unset($is_mob);
			unset($is_financiavel);
			unset($is_altopadrao);
			unset($is_exclusivo);
			unset($is_cfechado);
			unset($is_entrega);
			unset($end_mapa);
			unset($imovel_destacado);
			unset($contrato);
			unset($tipo_imovel);
			unset($rotulos);
			unset($cfechado);
			unset($mobiliado);
			unset($condominio);
			unset($iptu);
			unset($posicao);
			unset($posicaosolar);
			unset($galeria);
			unset($exclusivo);
			unset($altopadrao);
			unset($mar);
			unset($aravel);
			unset($areatotal);
			unset($areaprivativa);
			unset($bairro);
			unset($preco_imovel);
			unset($preco_imovel2);
			unset($preco_apos);
			unset($valor_locacao);
			unset($valor_venda);
			//unset($observacoes);
			//unset($slug);
			//unset($titulo);
			unset($set_bairro);
			unset($latlong);
			unset($tipomapa);
			unset($largura);
			unset($comprimento);
			//unset($postid);
			$date = date('d/m/Y h:i:s a', time());
			echo $date .' - post atualizado - '.$postid.' - Código:'.$value['codigo'];
			echo PHP_EOL;
			unset($date);

		
		}
	}
}
?>