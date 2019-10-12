<?php 
define('WP_USE_THEMES', false);
date_default_timezone_set('America/Sao_Paulo');
(string)$caminho = dirname(__FILE__);
require_once( $caminho.'/../../../wp-blog-header.php' );
$jetimob_options = get_option( 'jetimob_option_name' ); 
global $user_ID, $wpdb;

function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);
    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}

$headers = array('http'=>array('method'=>'GET','header'=>'Content: type=application/json \r\n'.'$agent \r\n'.'$hash'));
$context=stream_context_create($headers);
$arquivo = $caminho.'/jetimob.json';
$str = file_get_contents($arquivo,FILE_USE_INCLUDE_PATH,$context);
$str1=utf8_encode($str);
$str1=json_decode($str1, true);
foreach($str1 as $key=>$value){
        $check = get_posts(array(
       'post_type' => 'imovel', 
       'post_status' => 'publish',
       'meta_query' => array(
          array(
             'key'     => 'codigo',
             'value'   => $value['codigo'],
             'compare' => '='
          )
       ),
    ));  
    

    foreach ($check as $post){
            //echo 'Atualizando post '.$post->ID;
            //contrato e preço
                    //Tipo de negócio - compra ou venda

            if ($value['contrato'] == 'Compra') {
                $venda = true;
                $aluguel = false;
                $temporada = false;
            }
            elseif ($value['contrato'] == 'Locação') {
                $venda = false;
                $aluguel = true;
                $temporada = false;
            }
            elseif ($value['contrato'] == 'Compra,Locação') {
                $venda = true;
                $aluguel = true;
                $temporada = false;
            }
            elseif ($value['contrato'] == 'Compra,Temporada') {
                $venda = true;
                $aluguel = false;
                $temporada = true;
            }
            elseif ($value['contrato'] == 'Locação,Temporada') {
                $venda = false;
                $aluguel = true;
                $temporada = true;
            }
            elseif ($value['contrato'] == 'Compra,Locação,Temporada') {
                $venda = true;
                $aluguel = true;
                $temporada = true;
            }
            
            

            if($venda === true){
                if (is_null($value['valor_venda'])) {$preco_venda = "Consulte";}
                else { $preco_venda = $value['valor_venda']; }
            }

            if($aluguel === true){
                if (is_null($value['valor_locacao'])) {$preco_locacao = "Consulte";}
                else { $preco_venda = $value['valor_locacao'];}
            }

            if($temporada === true) {
                if (is_null($value['valor_temporada'])) {$preco_temporada = "Consulte";}
                else { $preco_venda = $value['valor_temporada'];}
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
            
            

            

            $update_data = array(

                        'ID'            => $post->ID,

                        'post_title'  => $titulo,

                        //'post_name'       => $slug,

                        'post_date' => $value['updated_at'],

                        'post_modified' => $value['updated_at'],

                        'post_content'  => $observacoes,

                        //'post_author' => 2,

                        

                        );

            $atualiza = wp_update_post($update_data, true);

            if (is_wp_error($atualiza)) {

                        $errors = $atualiza->get_error_messages();

                        foreach ($errors as $error) {

                            echo $error;

                        }

                    }

            update_post_meta($post->ID, 'codigo', $value['codigo']);   

            $estados = array(
                "AC"=>"Acre",
                "AL"=>"Alagoas",
                "AM"=>"Amazonas",
                "AP"=>"Amapá",
                "BA"=>"Bahia",
                "CE"=>"Ceará",
                "DF"=>"Distrito Federal",
                "ES"=>"Espírito Santo",
                "GO"=>"Goiás",
                "MA"=>"Maranhão",
                "MT"=>"Mato Grosso",
                "MS"=>"Mato Grosso do Sul",
                "MG"=>"Minas Gerais",
                "PA"=>"Pará",
                "PB"=>"Paraíba",
                "PR"=>"Paraná",
                "PE"=>"Pernambuco",
                "PI"=>"Piauí",
                "RJ"=>"Rio de Janeiro",
                "RN"=>"Rio Grande do Norte",
                "RO"=>"Rondônia",
                "RS"=>"Rio Grande do Sul",
                "RR"=>"Roraima",
                "SC"=>"Santa Catarina",
                "SE"=>"Sergipe",
                "SP"=>"São Paulo",
                "TO"=>"Tocantins"); 

            $uf = array_search($value['endereco_estado'], $estados);            

             
            $cidade = $value['endereco_cidade'].'/'.$uf;
            $bairro = $value['endereco_bairro'];

            if(!term_exists($cidade,'localizacao')){
                wp_insert_term($cidade, 'localizacao');                
            }

            $get_cidade = get_term_by( 'name', $cidade, 'localizacao' );

            if(!term_exists($bairro,'localizacao')){
                wp_insert_term($bairro, 'localizacao',  array('parent' => $get_cidade->term_id ));
            }


            $get_bairro = get_term_by( 'name', $bairro, 'localizacao' );

            wp_set_object_terms($post->ID, $get_cidade->term_id, 'localizacao');
            wp_set_object_terms($post->ID, $get_bairro->term_id, 'localizacao', true);


            /*
            $bairro = $value['endereco_bairro'].'-'.$value['endereco_cidade'];

            $bairro_so = $value['endereco_bairro'];

            $bairro_comp = array( 'name' => 'Centro', 'slug' => 'centro-santa-maria', 'taxonomy' => 'imovel_area');

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

            $salva_estado = wp_set_object_terms($post->ID, $value['endereco_estado'], 'property_state');

            $salva_cidade = wp_set_object_terms($post->ID, $value['endereco_cidade'], 'property_city');

            $salva_bairro = wp_set_object_terms($post->ID, $get_bairro->term_id, 'property_area');


            $assoc_estado = update_option( '_houzez_property_state_'.$get_estado->term_taxonomy_id, array('parent_country' => 'BR') );

            $assoc_cidade = update_option( '_houzez_property_city_'.$get_cidade->term_taxonomy_id, array('parent_state' => $value['endereco_estado']) );

            $assoc_bairro = update_option( '_houzez_property_area_'.$get_bairro->term_taxonomy_id, array('parent_city' => $value['endereco_cidade']) );

            $query2 = "

            SELECT $wpdb->posts.* 

            FROM $wpdb->posts, $wpdb->postmeta

            WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 

            AND $wpdb->postmeta.meta_key = 'id_corretor'

            AND $wpdb->posts.post_status = 'publish' 

            AND $wpdb->posts.post_type = 'jetimob_corretor' 

            AND $wpdb->postmeta.meta_value = '".$value['id_corretor']."'            

            ";

            $res_cor = $wpdb->get_results($query2);

            $corretor= $res_cor[0]->ID;



            update_post_meta($post->ID, 'fave_agents', $corretor);

            */

            delete_post_meta($post->ID, 'venda', '');
            delete_post_meta($post->ID, 'locacao', '');
            delete_post_meta($post->ID, 'temporada', '');


            update_post_meta($post->ID, 'venda', $venda);  
            update_post_meta($post->ID, 'locacao', $aluguel); 
            update_post_meta($post->ID, 'temporada', $temporada);

            delete_post_meta($post->ID, 'valor_venda', '');
            delete_post_meta($post->ID, 'valor_locacao', '');
            delete_post_meta($post->ID, 'valor_temporada', '');

            update_post_meta($post->ID, 'valor_venda', $preco_venda);  
            update_post_meta($post->ID, 'valor_locacao', $preco_locacao); 
            update_post_meta($post->ID, 'valor_temporada', $preco_temporada);

            update_post_meta($post->ID, 'valor_venda_visivel', $value['valor_venda_visivel']);

            update_post_meta($post->ID, 'valor_locacao_visivel', $value['valor_locacao_visivel']);

            update_post_meta($post->ID, 'pais', $value['endereco_estado']);

            update_post_meta($post->ID, 'estado', $value['endereco_estado']);

            update_post_meta($post->ID, 'cidade', $value['endereco_cidade']);

            update_post_meta($post->ID, 'bairro', $value['endereco_bairro']);

            update_post_meta($post->ID, 'cep', $value['endereco_cep']);

            update_post_meta($post->ID, 'dormitorios', $value['dormitorios']);

            update_post_meta($post->ID, 'banheiros', $value['banheiros']);

            update_post_meta($post->ID, 'garagens', $value['garagens']);

            update_post_meta($post->ID, 'suites', $value['suites']);

            update_post_meta($post->ID, 'area_util', $value['area_util']);

            update_post_meta($post->ID, 'area_privativa', $value['area_privativa']);

            update_post_meta($post->ID, 'area_total', $value['area_total']);

            update_post_meta($post->ID, 'medida', $value['medida']); 

            update_post_meta($post->ID, 'id_condominio', $value['id_condominio'] );

            update_post_meta($post->ID, 'id_corretor', $value['id_corretor'] );

            if ($value['destaque'] == "Em destaque") {

                update_post_meta( $post->ID, 'destaque', 1 );

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
                update_post_meta($post->ID, 'condominio_nome', $value['condominio_nome'] );
                }
            }

            elseif($value['endereco_visivel_no_site'] == "2")

            {

                $end_mapa = $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

                if(!is_null($value['condominio_nome'])) {

                update_post_meta($post->ID, 'condominio_nome', $value['condominio_nome'] );

                }

            }

            elseif($value['endereco_visivel_no_site'] == "3")

            {

                $end_mapa =  $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

                if(!is_null($value['condominio_nome'])) {

                update_post_meta($post->ID, 'condominio_nome', $value['condominio_nome'] );

                }

                if(!is_null($value['andar'])) {

                update_post_meta($post->ID, 'andar', $value['andar'] );

                }

            

            }

            elseif($value['endereco_visivel_no_site'] == "4")

            {

                $end_mapa =  $value['endereco_logradouro'].' '.$value['endereco_numero'].', '.$value['endereco_bairro'].', '.$value['endereco_cidade'].', '.$value['endereco_estado'];

                if(!is_null($value['condominio_nome'])) {

                update_post_meta($post->ID, 'condominio_nome', $value['condominio_nome'] );

                }

                if(!is_null($value['andar'])) {

                update_post_meta($post->ID, 'andar', $value['andar'] );

                }

                if(!is_null($value['endereco_complemento'])) {

                update_post_meta($post->ID, 'endereco_complemento', $value['endereco_complemento'] );

                }   



            }


            update_post_meta($post->ID, 'geoposicionamento_visivel', $value['geoposicionamento_visivel'] );

            if ($value['geoposicionamento_visivel'] == 1) {

                $calcula = true;

                $latitude = $value['latitude'];

                $longitude = $value['longitude'];

                update_post_meta($post->ID, 'mostrar_streetview', true );

                update_post_meta($post->ID, 'mostrar_mapa', true ); 
                update_post_meta($post->ID, 'endereco_mapa', $end_mapa );

            }

            elseif ($value['geoposicionamento_visivel'] == 2) {

                $calcula = true;

                $latitude = $value['latitude'];

                $longitude = $value['longitude'];

                update_post_meta($post->ID, 'mostrar_streetview', 'hide' );

                update_post_meta($post->ID, 'mostrar_mapa', true );     
                update_post_meta($post->ID, 'endereco_mapa', $end_mapa ); 

            }

            else {

                $calcula = false;

                //$latitude = null;

                //$longitude = null;

                update_post_meta($post->ID, 'mostrar_streetview', false );

                update_post_meta($post->ID, 'mostrar_mapa', false ); 

            }


            update_post_meta( $post->ID, 'latitude', $latitude );

            update_post_meta( $post->ID, 'longitude', $longitude );

            $latlong = $latitude.",".$longitude;

            update_post_meta( $post->ID, 'geolocalizacao', $latlong);

            $tipomapa = $value['geoposicionamento_visivel'];

            update_post_meta($post->ID, 'tipo_mapa', $tipomapa);        

            update_post_meta($post->ID, 'endereco_referencia', $value['endereco_referencia'] );

            update_post_meta($post->ID, 'endereco_visivel', $value['endereco_visivel'] );


            if(!is_null($value['endereco_complemento'])) {

                update_post_meta($post->ID, 'endereco_complemento', $value['endereco_complemento'] );

            }
            
            

            wp_set_object_terms($post->ID, $tipo_imovel, 'tipo_imovel');


            
            wp_set_object_terms($post->ID,  explode(',', $value['imovel_comodidades']), 'imovel_comodidades',true);

            wp_set_object_terms($post->ID,  explode(',', $value['condominio_comodidades']), 'condominio_comodidades',true);


            if ($value['status'] == 'Usado') {

                $situacao = null;

            }

            else{ $situacao = $value['situacao'];
             //array_push($situacao, $value['situacao']);

            wp_set_object_terms($post->ID, $value['situacao'], 'situacao');
            }

            if ($value['mobiliado'] == 1) {

            //array_push($situacao, 'Mobiliado');
            //$situacao[] = "Mobiliado";
            wp_set_object_terms($post->ID, "Mobiliado", 'situacao', true);

            }

            elseif ($value['mobiliado'] == 2) {

            //array_push($situacao, 'Semiobiliado');
            wp_set_object_terms($post->ID, "Semimobiliado", 'situacao', true);
            //$situacao[] = "Semimobiliado";

            }


            //wp_set_object_terms($post->ID, $value['condominio_nome'], 'condominio_nome');




            $metanull = null;

            if ($value['status'] != 'Usado') {
                $is_novo = 1;
                $novo = $value['status'];
                $tit_novo = 'Situação';
                wp_set_object_terms($post->ID, $value['status'], 'situacao', true);

            }
            



            /*

             if (!is_null($value['rural_area_aravel'])) {

                $aravel = number_format((float)$value['rural_area_aravel'], 2, '.', '').' '.$value['medida'];

                $tit_aravel = 'Área Arável';

                $is_aravel = 1;

            }
            */

            update_post_meta($post->ID, 'condominio', $value['valor_condominio']);        
            update_post_meta($post->ID, 'condominio_visivel', $value['valor_condominio_visivel']);    

            

            

            if ($value['valor_iptu_visivel'] == "1") {

                $iptu = 'R$ '.$value['valor_iptu'];

                $tit_iptu = 'IPTU';

                $is_iptu = 1;   
                
                

            }       
            update_post_meta($post->ID, 'iptu', $value['valor_iptu']);
            update_post_meta($post->ID, 'iptu_visivel', $value['valor_iptu_visivel']);
                    

                    

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
                    wp_set_object_terms($post->ID, 'Financiável', 'financiamento');

                }

                elseif ($value['financiavel'] == 2) {
                    $tit_financiavel = 'Financiável';
                    $financiavel = 'Minha Casa Minha Vida';
                    $is_financiavel = 1;                
                    wp_set_object_terms($post->ID, 'Minha Casa Minha Vida', 'financiamento');
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
                $largura = $value['terreno_largura'];
                $tit_largura = 'Largura do terreno';

            }   


            if (!is_null($value['terreno_comprimento'])) {

                $is_comprimento = 1;
                $comprimento = $value['terreno_comprimento'];
                $tit_comprimento = 'Comprimento do terreno'

            }

            

            



            update_post_meta($post->ID, 'caracteristicas',null);
            $t = update_post_meta($post->ID, 'caracteristicas', array(
                
                isset ($is_novo) ? array('caracteristica_titulo' => $tit_novo, 'caracteristica_valor' => $novo) : $metanull,

                isset ($is_entrega) ? array('caracteristica_titulo' => $tit_entrega, 'caracteristica_valor' => $entrega) : $metanull,

                isset ($is_areaprivativa) ? array('caracteristica_titulo' => $tit_areaprivativa, 'caracteristica_valor' => $areaprivativa) : $metanull, 

                isset ($is_areatotal) ? array('caracteristica_titulo' => $tit_areatotal, 'caracteristica_valor' => $areatotal) : $metanull,

                isset ($is_largura) ? array('caracteristica_titulo' => $tit_largura, 'caracteristica_valor' => $largura) : $metanull,

                isset ($is_comprimento) ? array('caracteristica_titulo' => $tit_comprimento, 'caracteristica_valor' => $comprimento) : $metanull,  

                isset ($is_construcao) ? array('caracteristica_titulo' => 'Tipo de Construção', 'caracteristica_valor' => $value['tipo_construcao']) : $metanull,

                isset ($is_aravel) ? array('caracteristica_titulo' => $tit_aravel, 'caracteristica_valor' => $aravel) : $metanull, 

                isset ($is_mar) ? array('caracteristica_titulo' => $tit_mar, 'caracteristica_valor' => $mar) : $metanull,

                isset ($is_posicao) ? array('caracteristica_titulo' => $tit_posicao, 'caracteristica_valor' => $posicao) : $metanull,

                isset ($is_posicaosolar) ? array('caracteristica_titulo' => $tit_posicaosolar, 'caracteristica_valor' => $posicaosolar) : $metanull,

                isset ($is_piso) ? array('caracteristica_titulo' => $tit_piso, 'caracteristica_valor' => $piso) : $metanull,

                isset ($is_condominio) ? array('caracteristica_titulo' => $tit_condominio, 'caracteristica_valor' => $condominio) : $metanull, 

                isset ($is_iptu) ? array('caracteristica_titulo' => $tit_iptu, 'caracteristica_valor' => $iptu) : $metanull, 

                isset ($is_mob) ? array('caracteristica_titulo' => $tit_mobiliado, 'caracteristica_valor' => $mobiliado) : $metanull, 

                isset ($is_financiavel) ? array('caracteristica_titulo' => $tit_financiavel, 'caracteristica_valor' => $financiavel) : $metanull,

                isset ($is_altopadrao) ? array('caracteristica_titulo' => $tit_altopadrao, 'caracteristica_valor' => $altopadrao) : $metanull,

                isset ($is_exclusivo) ? array('caracteristica_titulo' => $tit_exclusivo, 'caracteristica_valor' => $exclusivo) : $metanull,

                isset ($is_cfechado) ? array('caracteristica_titulo' => $tit_cfechado, 'caracteristica_valor' => $cfechado) : $metanull,
                
                ) 

            );



            delete_post_meta($post->ID, 'galeria_imagens', '');

            delete_post_meta($post->ID, 'slider', '');

            delete_post_meta($post->ID, 'imagem_slider', ''); 

            delete_post_meta($post->ID, 'fifu_image_url', ''); 



            $imagens = $value['imagens'];

            if (!is_null($imagens)) {

                //$ibagens = $value['imagens'][0]['link'];

                foreach ($imagens as $imagem) {

                    $galeria[] = $imagem['link'];

                    add_post_meta($post->ID, 'galeria_imagens', $imagem['link']);                    

                }

                add_post_meta($post->ID, 'slider', 'yes');

                add_post_meta($post->ID, 'imagem_slider', $galeria[0]);

                add_post_meta($post->ID, 'fifu_image_url', $galeria[0]);

                update_post_meta($post->ID, '_thumbnail_id', '-1');

                //Generate_Featured_Image( $galeria[0],   $post->ID );        

             } 



            /*

            delete_post_meta($post->ID, 'fave_floor_plans_enable', ''); 

            delete_post_meta($post->ID, 'floor_plans', ''); 

            $plantas = $value['plantas'];

            if (!is_null($plantas)) {

                foreach($plantas as $key=>$planta) {

                    if(is_null($planta['titulo'])){ $planta['titulo'] = "Planta";}

                    $addplanta[] = array('fave_plan_title' => $planta['titulo'], 'fave_plan_image' => $planta['link'] ); 

                    

                } 

                add_post_meta($post->ID, 'fave_floor_plans_enable', 'enable');

                add_post_meta($post->ID, 'floor_plans', $addplanta);  

            }

           */  

            delete_post_meta($post->ID, 'tour_virtual', '');



            if (!is_null($value['tour360'])) {

                add_post_meta($post->ID, 'tour_virtual', $value['tour360'][0]);

                        

            }



            delete_post_meta($post->ID, 'video', '');
            if (!is_null($value['videos'])) {
                add_post_meta($post->ID, 'video', $value['videos'][0]['link']); 
                unset($verifica_video);
            }

            //wp_set_object_terms($post->ID, $rotulos, 'property_label');        


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

            unset($venda);
            unset($aluguel);
            unset($temporada);

            //unset($post->ID);
            $date = date('d/m/Y h:i:s a', time());
            echo $date .' - post atualizado - '.$post->ID.' - Código:'.$value['codigo'];
            echo PHP_EOL;
            unset($date);
    }
}
?>