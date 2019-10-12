#TODO - criação de child theme para Houzez



É necessário alterar a forma como as imagens são chamadas para o slideshow de imóveis.



DE:

rwmb_meta()



PARA:

get_post_meta()



É necessário alterar a inserção destas imagens, a função get_post_meta retornará a lista de imagens em um array, bastando chamar cada um em um loop foreach, inserindo manualmente cada imagem com a tag <img>.



Ex:

$properties_img = get_post_meta( $post->ID, 'fave_property_images', true );



	foreach( $properties_img as $prop_image_url ) {

	 	echo '<div class="item" style="background-size:contain,100%;background-image: url('.esc_url( $prop_image_url ).')">';                                      



        echo '<a class="popup-trigger banner-link" href="#">';



        echo '</a>';



        echo '</div>';



    }







Dentro da pasta do tema, pode ser necessário substituir um trecho do código do arquivo property-details/property-details.php.

Substitua:

	foreach( $additional_features as $ad_del ):

        echo '<li><strong>'.esc_attr( $ad_del['fave_additional_feature_title'] ).':</strong> '.esc_attr( $ad_del['fave_additional_feature_value'] ).'</li>';

    endforeach;



Por:

	foreach( $additional_features as $ad_del ):



        if(!empty($ad_del['fave_additional_feature_title']) || !empty($ad_del['fave_additional_feature_value']) ){



        echo '<li><strong>'.esc_attr( $ad_del['fave_additional_feature_title'] ).':</strong> '.esc_attr( $ad_del['fave_additional_feature_value'] ).'</li>';



    }



    endforeach;



No mesmo arquivo, adicione os seguintes trechos de código:

Entre as linhas 10 e 23, insira a seguinte linha:

    $suites = get_post_meta( get_the_ID(), 'fave_property_suites', true );


Entre as linhas 25 e 34, coloque a linha abaixo:


    !empty( $suites ) ||

A seguinte parte de código deve ser inserida entre as linhas 56 e 90:

	if( !empty( $suites ) && $hide_detail_prop_fields['suites'] != 1 ) {

                echo '<li><strong>'.esc_html__( 'Suítes:', 'houzez').'</strong> '.esc_attr( $suites ).'</li>';

         }