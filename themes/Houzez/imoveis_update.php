<?php 

foreach($str1 as $key=>$value)
	

{
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
	

		if($value['updated_at'] == $post->post_date) { echo 'OK - '.$post->ID; echo PHP_EOL;

	}


		else { 



		$postid = $post->ID;

		//echo 'Atualizando post '.$postid;

		//contrato e preço

				//Tipo de negócio - compra ou venda

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
		$contrato = "Venda";
	}
	elseif ($value['contrato'] == 'Locação') {
		$contrato = "Aluguel";
	}
	elseif ($value['contrato'] == 'Compra,Locação') {
		$contrato = "Venda,Aluguel";
	}
		

		//$contrato = str_replace('Compra', 'Venda', $value['contrato']);

		//$contrato = str_replace('Locação', 'Aluguel', $value['contrato']);



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

			

 		$bairro = $value['endereco_bairro'].'-'.$value['endereco_cidade'];

 		$bairro_so = $value['endereco_bairro'];

 		$bairro_comp = array( 'name' => 'Centro', 'slug' => 'centro-santa-maria', 'taxonomy' => 'property_area');

 	 	$def_estado = wp_update_term('' , 'property_state', $value['endereco_estado']);

 		$def_cidade = wp_update_term(null , 'property_city', $value['endereco_cidade']);

 		$your_term = get_term_by( 'slug', $bairro, 'property_area' );

		if ( false !== $your_term ) {

		    $def_bairro = wp_update_term($your_term->term_id , 'property_area', $bairro_comp);

		}

		else {

			$def_bairro = wp_insert_term($value['endereco_bairro'], 'property_area', array( 'slug' => $bairro));

		}

 		$get_estado = get_term_by( 'name', $value['endereco_estado'], 'property_state' );

 		$get_cidade = get_term_by( 'name', $value['endereco_cidade'], 'property_city' );

 		$get_bairro = get_term_by( 'slug', $bairro, 'property_area' );

 		$salva_estado = wp_set_object_terms($postid, $value['endereco_estado'], 'property_state');

 		$salva_cidade = wp_set_object_terms($postid, $value['endereco_cidade'], 'property_city');

 		$salva_bairro = wp_set_object_terms($postid, $get_bairro->term_id, 'property_area');





 		$assoc_estado = update_option( '_houzez_property_state_'.$get_estado->term_taxonomy_id, array('parent_country' => 'BR') );

 		$assoc_cidade = update_option( '_houzez_property_city_'.$get_cidade->term_taxonomy_id, array('parent_state' => $value['endereco_estado']) );

 		$assoc_bairro = update_option( '_houzez_property_area_'.$get_bairro->term_taxonomy_id, array('parent_city' => $value['endereco_cidade']) );





 		update_post_meta($postid, 'fave_property_id', $value['codigo']);

 		update_post_meta($postid, 'fave_agent_display_option', 'agent_info');



 		 	$query2 = "

		    SELECT $wpdb->posts.* 

		    FROM $wpdb->posts, $wpdb->postmeta

		    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 

		    AND $wpdb->postmeta.meta_key = 'fave_agent_id'

		    AND $wpdb->posts.post_status = 'publish' 

		    AND $wpdb->posts.post_type = 'houzez_agent' 

		    AND $wpdb->postmeta.meta_value = '".$value['id_corretor']."'	        

 		";

 		$res_cor = $wpdb->get_results($query2);

 		$corretor= $res_cor[0]->ID;



 		update_post_meta($postid, 'fave_agents', $corretor);



 		update_post_meta($postid, 'fave_property_price', $preco_imovel);

 		update_post_meta($postid, 'fave_property_sec_price', $preco_imovel2);

 		update_post_meta($postid, 'fave_property_price_postfix', $preco_apos); 		

 		update_post_meta($postid, 'valor_venda', $value['valor_venda']);

 		update_post_meta($postid, 'valor_locacao', $value['valor_locacao']);

 		update_post_meta($postid, 'valor_venda_visivel', $value['valor_venda_visivel']);

 		update_post_meta($postid, 'valor_locacao_visivel', $value['valor_locacao_visivel']);

 		update_post_meta($postid, 'fave_property_country', 'BR');

 		update_post_meta($postid, 'fave_property_state', $value['endereco_estado']);

 		update_post_meta($postid, 'fave_property_city', $value['endereco_cidade']);

 		update_post_meta($postid, 'fave_property_area', $value['endereco_bairro']);

 		update_post_meta($postid, 'fave_property_zip', $value['endereco_cep']);

 		update_post_meta($postid, 'fave_property_bedrooms', $value['dormitorios']);

 		update_post_meta($postid, 'fave_property_bathrooms', $value['banheiros']);

 		update_post_meta($postid, 'fave_property_garages', $value['garagens']);

 		update_post_meta($postid, 'fave_property_suites', $value['suites']);

 		update_post_meta($postid, 'fave_property_bedrooms', $value['dormitorios']);

 		update_post_meta($postid, 'fave_property_size', $value['area_util']);

 		update_post_meta($postid, 'fave_property_size_prefix', 'm²');	

 		update_post_meta($postid, 'fave_additional_features_enable', 'enable' );

 		update_post_meta($postid, 'id_condominio', $value['id_condominio'] );

 		//update_post_meta($postid, 'id_corretor', $value['id_corretor'] );

 		if ($value['destaque'] == "Em destaque") {

			update_post_meta( $postid, 'fave_featured', 1 );

		}
		elseif ($value['destaque'] == "Lançamento") {
			//wp_set_object_terms($postid, "Lançamento", 'property_status', true);	
			//$rotulos[] = "Lançamento";				

		}



		
		//visualização no mapa

		if($value['endereco_visivel_no_site'] == "-2")

		{

			$end_mapa = $value['endereco_estado'];

		}

		elseif($value['endereco_visivel_no_site'] == "-1")

		{

			$end_mapa = $value['endereco_cidade'].', '.$value['endereco_estado'];

		}

		elseif($value['endereco_visivel_no_site'] == "0")

		{

			$end_mapa = $value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];
		}

		elseif($value['endereco_visivel_no_site'] == "1")

		{

			$end_mapa = $value['endereco_logradouro'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

			if(!is_null($value['condominio_nome'])) {

			update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

			}	

		}

		elseif($value['endereco_visivel_no_site'] == "2")

		{

			$end_mapa = $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

			if(!is_null($value['condominio_nome'])) {

			update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

			}

		}

		elseif($value['endereco_visivel_no_site'] == "3")

		{

			$end_mapa =  $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

			if(!is_null($value['condominio_nome'])) {

			update_post_meta($postid, 'condominio_nome', $value['condominio_nome'] );

			}

			if(!is_null($value['andar'])) {

			update_post_meta($postid, 'andar', $value['andar'] );

			}

		

		}

		elseif($value['endereco_visivel_no_site'] == "4")

		{

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

			update_post_meta($postid, 'fave_property_map_street_view', 'show' );

			update_post_meta($postid, 'fave_property_map', 1 );	
			update_post_meta($postid, 'fave_property_map_address', $end_mapa );

		}

		elseif ($value['geoposicionamento_visivel'] == 2) {

			$calcula = true;

			$latitude = $value['latitude'];

			$longitude = $value['longitude'];

			update_post_meta($postid, 'fave_property_map_street_view', 'hide' );

			update_post_meta($postid, 'fave_property_map', 1 );		
			update_post_meta($postid, 'fave_property_map_address', $end_mapa );	

		}

		else {

			$calcula = false;

			//$latitude = null;

			//$longitude = null;

			update_post_meta($postid, 'fave_property_map_street_view', 'hide' );

			update_post_meta($postid, 'fave_property_map', 0 );	

		}


		update_post_meta( $postid, 'houzez_geolocation_lat', $latitude );

		update_post_meta( $postid, 'houzez_geolocation_long', $longitude );

		$latlong = $latitude.",".$longitude;

		update_post_meta( $postid, 'fave_property_location', $latlong);

		$tipomapa = $value['geoposicionamento_visivel'];

      	update_post_meta($post->ID, 'tipo_mapa', $tipomapa);


		

		update_post_meta($postid, 'endereco_referencia', $value['endereco_referencia'] );

		update_post_meta($postid, 'endereco_visivel', $value['endereco_visivel'] );

	

		



		update_post_meta($postid, 'endereco_referencia', $value['endereco_referencia'] );

		update_post_meta($postid, 'endereco_visivel', $value['endereco_visivel'] );

	

		



		if(!is_null($value['endereco_complemento'])) {

			update_post_meta($postid, 'endereco_complemento', $value['endereco_complemento'] );

			}



 		wp_set_object_terms($postid, explode(',', $contrato), 'property_status');

 		if ($value['status'] == "Em construção") {
			wp_set_object_terms($postid, "Lançamento", 'property_status', true);						

		}	

 		wp_set_object_terms($postid, $tipo_imovel, 'property_type');
 		
  		wp_set_object_terms($postid,  explode(',', $value['imovel_comodidades']), 'property_feature',true);

 		wp_set_object_terms($postid,  explode(',', $value['condominio_comodidades']), 'cond_feature',true);

 		if ($value['status'] == 'Usado') {

 			$situacao = null;

 		}

 		else{ $situacao = $value['situacao'];
 		 //array_push($situacao, $value['situacao']);

 		wp_set_object_terms($postid, $value['situacao'], 'situacao');
 		}

 		if ($value['mobiliado'] == 1) {

        //array_push($situacao, 'Mobiliado');
        //$situacao[] = "Mobiliado";
        wp_set_object_terms($postid, "Mobiliado", 'situacao', true);

      }

      elseif ($value['mobiliado'] == 2) {

      	//array_push($situacao, 'Semiobiliado');
      	wp_set_object_terms($postid, "Semimobiliado", 'situacao', true);
        //$situacao[] = "Semimobiliado";

      }

      //var_dump($situacao);
 		//wp_set_object_terms($postid,   $situacao, 'situacao');

 		wp_set_object_terms($postid, $value['condominio_nome'], 'condominio_nome');




 		//$metanull = array('fave_additional_feature_title' => null, 'fave_additional_feature_value' => null);
 		$metanull = null;

 		if ($value['status'] != 'Usado') {
		 	$is_novo = 1;
 			$novo = $value['status'];
 			$tit_novo = 'Situação';
 			wp_set_object_terms($postid, $value['status'], 'situacao', true);

 		}
 		


 		if (!is_null($value['area_privativa'])) {

 			$areaprivativa = number_format((float)$value['area_privativa'], 2, '.', '').' m²';

 			$tit_areaprivativa = 'Área Privativa';	

 			$is_areaprivativa = 1;		

 		}

 		



 		if (!is_null($value['area_total'])) {

 			$areatotal = number_format((float)$value['area_total'], 2, '.', '').' m²';

 			$tit_areatotal = 'Área Total';

 			$is_areatotal = 1;

 		}

 		

 		 if (!is_null($value['rural_area_aravel'])) {

 			$aravel = number_format((float)$value['rural_area_aravel'], 2, '.', '').' '.$value['medida'];

 			$tit_aravel = 'Área Arável';

 			$is_aravel = 1;

 		}



 		$tit_condominio = 'Condomínio';

 		if ($value['valor_condominio_visivel'] == "1") {

 			$tit_condominio = 'Condomínio';

 			$condominio = 'R$ '.$value['valor_condominio'];

 			$is_condominio = 1; 
 				

 		}	

 		update_post_meta($postid, 'condominio', $value['valor_condominio']);		
 		update_post_meta($postid, 'condominio_visivel', $value['valor_condominio_visivel']); 	

 		

 		

 		if ($value['valor_iptu_visivel'] == "1") {

 			$iptu = 'R$ '.$value['valor_iptu'];

 			$tit_iptu = 'IPTU';

 			$is_iptu = 1; 	
 			
 			

 		} 		
		update_post_meta($postid, 'iptu', $value['valor_iptu']);
		update_post_meta($postid, 'iptu_visivel', $value['valor_iptu_visivel']);
 				

		 		

 		$value['mobiliado'] = $mobilia;



 		if ($value['mobiliado'] == 0 || ! is_null($value['mobiliado'])) {

		    $tit_mobiliado = null;

 				$mobiliado = null;

		} elseif ($value['mobiliado'] != 0) {

		    	$is_mob = 1;

 				$tit_mobiliado = 'Mobiliado';

 				if ($value['mobiliado'] == 1){

 					$mobiliado = 'Sim';

 				} 	

 				elseif ($value['mobiliado'] == 2){

 					$mobiliado = 'Semimobiliado';

 				} 			

		} 

			if ($value['financiavel'] == 1) {

				$financiavel = "Sim";
				$tit_financiavel = 'Financiável';
				$is_financiavel = 1;				
				wp_set_object_terms($postid, 'Financiável', 'financiamento');

			}

			elseif ($value['financiavel'] == 2) {
				$tit_financiavel = 'Financiável';
				$financiavel = 'Minha Casa Minha Vida';
				$is_financiavel = 1;				
				wp_set_object_terms($postid, 'Minha Casa Minha Vida', 'financiamento');
				$rotulos[] = "Minha Casa Minha Vida";

			}			

		

	 if (!is_null($value['distancia_mar'])) {

 			$mar = $value['distancia_mar'];

 			$tit_mar = 'Distância do mar';

 			$is_mar = 1; 			

 		} 

 		if (!is_null($value['posicao'])) {

 			$posicao = $value['posicao'];

 			$tit_posicao = 'Posição';

 			$is_posicao = 1; 			

 		} 

 		if (!is_null($value['posicaosolar'])) {

 			$posicaosolar = $value['posicaosolar'];

 			$tit_posicaosolar = 'Posição Solar';

 			$is_posicaosolar = 1; 			

 		} 

 		if (!is_null($value['piso'])) {

 			$piso = $value['piso'];

 			$tit_piso = 'Piso';

 			$is_piso = 1; 			

 		} 



		if ($value['alto_padrao'] == 1) {

 			$altopadrao = 'Sim';

 			$tit_altopadrao = 'Alto Padrão';

 			$is_altopadrao = 1; 

 			$rotulos[] = 'Prime';			

 		}

 		elseif ($value['alto_padrao'] != 1) {

 			$altopadrao = '';

 			$tit_altopadrao = '';

 		}


		if ($value['exclusividade'] == 1) {

 			$exclusivo = 'Sim';

 			$tit_exclusivo = 'Exclusividade';

 			$is_exclusivo = 1;

 			$rotulos[] = $tit_exclusivo;			

 		}

 		elseif ($value['exclusividade'] != 1) {

 			$exclusivo = '';

 			$tit_exclusivo = '';

 		}



 		if ($value['condominio_fechado'] == 1) {

 			$cfechado = 'Sim';

 			$tit_cfechado = 'Cond. Fechado';

 			$is_cfechado = 1;

 					

 		}

 		elseif ($value['condominio_fechado'] != 1) {

 			$cfechado = '';

 			$tit_cfechado = '';

 		}

 		if(!is_null($value['entrega_ano']) && !is_null($value['entrega_mes'])){

 			$entrega = $value['entrega_mes'].'/'.$value['entrega_ano'];

 			$is_entrega = 1;

 			$tit_entrega = "Entrega";

 		}

 		//else { $tit_entrega = null; }



 		/*if (!is_null($value['situacao'])) {

 			$is_situacao = 1;

 		}*/



 		if (!is_null($value['tipo_construcao'])) {

 			$is_construcao = 1;

 		} 		

 		if (!is_null($value['terreno_largura'])) {

 			$is_largura = 1;
 			$tit_largura = "Largura";
 			$largura = $value['terreno_largura']."m";

 		} 	
 		else { $tit_largura = null; }

 		if (!is_null($value['terreno_comprimento'])) {

 			$is_comprimento = 1;
 			$tit_comprimento = "Comprimento";
 			$comprimento = $value['terreno_comprimento']."m";

 		} 
 		else { $tit_comprimento = null; }

 		

 		

 	

 		update_post_meta($postid, 'additional_features',null);
 		$t = update_post_meta($postid, 'additional_features', array(
 			
 			isset ($is_novo) ? array('fave_additional_feature_title' => $tit_novo, 'fave_additional_feature_value' => $novo) : $metanull,

 			isset ($is_entrega) ? array('fave_additional_feature_title' => $tit_entrega, 'fave_additional_feature_value' => $entrega) : $metanull,

 			isset ($is_areaprivativa) ? array('fave_additional_feature_title' => $tit_areaprivativa, 'fave_additional_feature_value' => $areaprivativa) : $metanull, 

 			isset ($is_areatotal) ? array('fave_additional_feature_title' => $tit_areatotal, 'fave_additional_feature_value' => $areatotal) : $metanull,

 			isset ($is_largura) ? array('fave_additional_feature_title' => $tit_largura, 'fave_additional_feature_value' => $largura) : $metanull,

 			isset ($is_comprimento) ? array('fave_additional_feature_title' => $tit_comprimento, 'fave_additional_feature_value' => $comprimento) : $metanull,  

 			isset ($is_construcao) ? array('fave_additional_feature_title' => 'Tipo de Construção', 'fave_additional_feature_value' => $value['tipo_construcao']) : $metanull,

 			isset ($is_aravel) ? array('fave_additional_feature_title' => $tit_aravel, 'fave_additional_feature_value' => $aravel) : $metanull, 

 			isset ($is_mar) ? array('fave_additional_feature_title' => $tit_mar, 'fave_additional_feature_value' => $mar) : $metanull,

 			isset ($is_posicao) ? array('fave_additional_feature_title' => $tit_posicao, 'fave_additional_feature_value' => $posicao) : $metanull,

 			isset ($is_posicaosolar) ? array('fave_additional_feature_title' => $tit_posicaosolar, 'fave_additional_feature_value' => $posicaosolar) : $metanull,

 			isset ($is_piso) ? array('fave_additional_feature_title' => $tit_piso, 'fave_additional_feature_value' => $piso) : $metanull,

 			isset ($is_condominio) ? array('fave_additional_feature_title' => $tit_condominio, 'fave_additional_feature_value' => $condominio) : $metanull, 

 			isset ($is_iptu) ? array('fave_additional_feature_title' => $tit_iptu, 'fave_additional_feature_value' => $iptu) : $metanull, 

 			isset ($is_mob) ? array('fave_additional_feature_title' => $tit_mobiliado, 'fave_additional_feature_value' => $mobiliado) : $metanull, 

 			isset ($is_financiavel) ? array('fave_additional_feature_title' => $tit_financiavel, 'fave_additional_feature_value' => $financiavel) : $metanull,

 			isset ($is_altopadrao) ? array('fave_additional_feature_title' => $tit_altopadrao, 'fave_additional_feature_value' => $altopadrao) : $metanull,

 			isset ($is_exclusivo) ? array('fave_additional_feature_title' => $tit_exclusivo, 'fave_additional_feature_value' => $exclusivo) : $metanull,

 			isset ($is_cfechado) ? array('fave_additional_feature_title' => $tit_cfechado, 'fave_additional_feature_value' => $cfechado) : $metanull,
 			
 			) 

 		);



 		delete_post_meta($postid, 'fave_property_images', '');

 		delete_post_meta($postid, 'fave_prop_homeslider', '');

 		delete_post_meta($postid, 'fave_prop_slider_image', ''); 



 		$imagens = $value['imagens'];

 		if (!is_null($imagens)) {

 			//$ibagens = $value['imagens'][0]['link'];

 		 	foreach ($imagens as $imagem) {

		 		$galeria[] = $imagem['link'];

		 		add_post_meta($postid, 'fave_property_images', $imagem['link']); 			 		

		 	}

	 		add_post_meta($postid, 'fave_prop_homeslider', 'yes');

	 		add_post_meta($postid, 'fave_prop_slider_image', $galeria[0]);

	 		add_post_meta($postid, 'fifu_image_url', $galeria[0]);

	 		update_post_meta($postid, '_thumbnail_id', '-1');

	 		//Generate_Featured_Image( $galeria[0],   $postid ); 		

 		 } 





 	delete_post_meta($postid, 'fave_floor_plans_enable', ''); 

 	delete_post_meta($postid, 'floor_plans', ''); 

 	$plantas = $value['plantas'];

 	if (!is_null($plantas)) {

 		foreach($plantas as $key=>$planta) {

 			if(is_null($planta['titulo'])){ $planta['titulo'] = "Planta";}

	       	$addplanta[] = array('fave_plan_title' => $planta['titulo'], 'fave_plan_image' => $planta['link'] ); 

	       	

	    } 

	    add_post_meta($postid, 'fave_floor_plans_enable', 'enable');

		add_post_meta($postid, 'floor_plans', $addplanta);  

 	}

     

    delete_post_meta($postid, 'fave_virtual_tour', '');



    if (!is_null($value['tour360'])) {

		add_post_meta($postid, 'fave_virtual_tour', $value['tour360'][0]);

				

	}



    delete_post_meta($postid, 'fave_video_url', '');

    

    if (!is_null($value['videos'])) {

		add_post_meta($postid, 'fave_video_url', $value['videos'][0]['link']); 		

			

		unset($verifica_video);



 	}



 	wp_set_object_terms($postid, $rotulos, 'property_label');

 		

	

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