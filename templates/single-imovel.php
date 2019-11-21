<?php

get_header(); ?>
<script type="text/javascript">
//window.jQuery || document.write('\x3Cscript src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">\x3C/script>');
</script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/galleria/1.5.7/galleria.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/galleria/1.5.7/themes/classic/galleria.classic.min.js"></script>
<script type="text/javascript">
Galleria.run('.galleria', {
  height: 0.5625,
  /*
   * This is a good place to add other options,
   * like transition: 'fade',
   * or autoplay: true,
   * but don't forget the comma at the end.
   * See: http://galleria.io/docs/options/
   */
  dataConfig: function(image) {
    return { image: jQuery(image).attr('src'), big: jQuery(image).parent().attr('href') };
  }
});
</script>
<div id="main-content" class="main-content">

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <?php
                // Start the Loop.
                while ( have_posts() ) : the_post();
                ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                        <header class="entry-header">
                            <?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
                        </header>
                        <?php endif; ?>
                        <?php
                                $venda = get_post_meta($post->ID, 'venda', true);
                                $locacao = get_post_meta($post->ID, 'locacao', true);
                                $temporada = get_post_meta($post->ID, 'temporada', true);
                                $valor_venda = get_post_meta($post->ID, 'valor_venda', true);
                                $valor_locacao = get_post_meta($post->ID, 'valor_locacao', true);
                                $valor_temporada = get_post_meta($post->ID, 'valor_temporada', true);
                                $codigo = get_post_meta($post->ID, 'codigo', true);
                                $area_total = get_post_meta($post->ID, 'area_total', true);
                                $area_util = get_post_meta($post->ID, 'area_util', true);
                                $dorms = get_post_meta($post->ID, 'dormitorios', true); 
                                $suites = get_post_meta($post->ID, 'suites', true);
                                $banheiros = get_post_meta($post->ID, 'banheiros', true);
                                $vagas = get_post_meta($post->ID, 'garagens', true);
                            ?> 
                        <div class="entry-content">
                             <?php if (!is_null($valor_venda) && $venda > 0) { 
                                if(get_post_meta($post->ID, 'valor_venda_visivel', true) == '1') {?>
                                    <h4><i class="fas fa-money-bill-wave"></i> Valor (Venda): <strong><?php echo $valor_venda; ?></strong></h4>
                                <?php } else { ?> 
                                    <h4><i class="fas fa-money-bill-wave"></i> Valor (Venda): <strong>Consulte</strong></h4>
                                <?php }
                            } ?>

                            <?php if (!is_null($valor_locacao) && $locacao > 0) { 
                                if(get_post_meta($post->ID, 'valor_locacao_visivel', true) == '1') {?>
                                    <h4><i class="fas fa-money-bill-wave"></i> Valor (Aluguel): <strong><?php echo $valor_locacao; ?></strong></h4>
                                <?php } else { ?> 
                                    <h4><i class="fas fa-money-bill-wave"></i> Valor (Aluguel): <strong>Consulte</strong></h4>
                                <?php }
                            } ?>

                            <?php if (!is_null($valor_temporada) && $temporada > 0) { 
                                if(get_post_meta($post->ID, 'valor_temporada_visivel', true) == '1') {?>
                                    <h4><i class="fas fa-money-bill-wave"></i> Valor (Temporada/Diária): <strong><?php echo $valor_temporada; ?></strong></h4>
                                <?php } else { ?> 
                                    <h4><i class="fas fa-money-bill-wave"></i> Valor (Temporada/Diária): <strong>Consulte</strong></h4>
                                <?php }
                            } ?>


                            <h3>Detalhes</h3>
                            <ul>
                                                       
                            <li><i class="fas fa-barcode"></i> Ref.: <strong><?php echo $codigo; ?></strong></li>
                            <?php if (!is_null($area_total) && $area_total > 0) { ?>
                                <li><i class="fas fa-vector-square"></i> Área Total: <strong><?php echo $area_total; ?></strong></li>
                            <?php } ?> 
                            <?php if (!is_null($area_util) && $area_util > 0) { ?>
                                <li><i class="fas fa-vector-square"></i> Área Útil: <strong><?php echo $area_util; ?></strong></li>
                            <?php } ?> 
                            <?php if (!is_null($dorms) && $dorms > 0) { ?>
                            <li><i class="fas fa-bed"></i> Quartos: <strong><?php echo $dorms; ?></strong></li>
                            <?php } ?> 
                            <?php if (!is_null($suites) && $suites > 0) { ?>
                            <li><i class="fas fa-bed"></i> Suítes: <strong><?php echo $suites; ?></strong></li>
                            <?php } ?> 
                            <?php if (!is_null($banheiros) && $banheiros > 0) { ?>
                            <li><i class="fas fa-bath"></i> Banheiros: <strong><?php echo $banheiros ?></strong></li>
                            <?php } ?> 
                            <?php if (!is_null($vagas) && $vagas > 0) { ?>
                            <li><i class="fas fa-car"></i> Vagas: <strong><?php echo $vagas; ?></strong></li>
                            <?php } ?> 
                        </ul>
                        </div>

                        <?php 
                            $images = get_post_meta($post->ID, 'galeria_imagens', false); 
                            if(!is_null($images)):
                        ?>
                        <div class="entry-content">
                             <h3>Fotos</h3>
                            <div class="galleria">
                                <?php foreach($images as $image): ?>
                                <img src="<?php echo $image; ?>" />

                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <h3>Descrição</h3>
                            <?php
                            the_content(
                                sprintf(
                                    wp_kses(
                                        /* translators: %s: Name of current post. Only visible to screen readers */
                                        __( 'Continuar lendo<span class="screen-reader-text"> "%s"</span>', 'jetimob' ),
                                        array(
                                            'span' => array(
                                                'class' => array(),
                                            ),
                                        )
                                    ),
                                    get_the_title()
                                )
                            );

                            wp_link_pages(
                                array(
                                    'before' => '<div class="page-links">' . __( 'Pages:', 'jetimob' ),
                                    'after'  => '</div>',
                                )
                            );
                            ?>


                        </div><!-- .entry-content -->

                        <div class="entry-content">
                            <h3>Características</h3>
                            <?php 
                                $caracteristicas = get_post_meta($post->ID, 'caracteristicas'); 
                                echo '<ul>';
                                foreach($caracteristicas[0] as $caracteristica){
                                    if(!empty($caracteristica['caracteristica_titulo']) && !empty($caracteristica['caracteristica_valor'])){
                                        echo '<li>'.$caracteristica['caracteristica_titulo'].': '.$caracteristica['caracteristica_valor'].'</li>';
                                    }
                                }
                            ?>

                        </div>

                        <?php 
                            $imovel_terms = get_the_term_list( $post->ID, 'imovel_comodidades', '<ul class="styles"><li>', '</li><li>', '</li></ul>' ); 
                            if(count_chars($imovel_terms) > 0) {  
                        ?>
                        <div class="entry-content">
                            <h3>Comodidades do Imóvel</h3>
                            <?php 
                                echo $imovel_terms;
                            ?>
                        </div>
                        <?php } ?>

                        <?php 
                            $condominio_terms = get_the_term_list( $post->ID, 'condominio_comodidades', '<ul class="styles"><li>', '</li><li>', '</li></ul>' ); 
                            if(count_chars($condominio_terms) > 0) {  
                        ?>
                        <div class="entry-content">
                            <h3>Comodidades do Condomínio</h3>
                            <?php 
                                echo $condominio_terms;
                            ?>
                        </div>
                        <?php } ?>

                        <?php
                            $options = get_site_option('jetimob_option_name'); 
                            $maps_api = $options['gmaps']; 
                            $geopos_visivel = get_post_meta($post->ID, 'geoposicionamento_visivel', true);
                            $lat = get_post_meta($post->ID, 'latitude', true);
                            $long = get_post_meta($post->ID, 'longitude', true);
                            //if($geopos_visivel > 0){ 
                        ?>

                        
                                <?php if($geopos_visivel == 1){ ?>
                                <div class="entry-content">
                                    <h3>Localização</h3> 
                                        <div id="map" style="height:450px;"></div>
                                        <script>
                                          function initMap() {
                                            var myLatLng = {lat: <?php echo $lat;?>, lng: <?php echo $long; ?>};

                                            var map = new google.maps.Map(document.getElementById('map'), {
                                              zoom: 15,
                                              center: myLatLng
                                            });

                                            var marker = new google.maps.Marker({
                                              position: myLatLng,
                                              map: map,
                                              //title: 'Hello World!'
                                            });
                                          }
                                          google.maps.event.addDomListener(window, 'load', initMap
                                            );
                                        </script>
                                    </div>
                                   
                                <?php } ?>
                                <?php if($geopos_visivel == 2){ ?>
                                    <div class="entry-content">
                                        <h3>Localização</h3>
                                        <div id="map" style="height:450px;"></div>
                                        <script>
                                          function initMap() {
                                            var myLatLng = {lat: <?php echo $lat;?>, lng: <?php echo $long; ?>};

                                            var map = new google.maps.Map(document.getElementById('map'), {
                                              zoom: 15,
                                              center: myLatLng
                                            });

                                            var cityCircle = new google.maps.Circle({
                                                strokeColor: '#FF0000',
                                                strokeOpacity: 0.8,
                                                strokeWeight: 2,
                                                fillColor: '#FF0000',
                                                fillOpacity: 0.35,
                                                map: map,
                                                center: map.center,
                                                radius: 500
                                              });
                                          }
                                          google.maps.event.addDomListener(window, 'load', initMap
                                            );
                                        </script>
                                    </div>
                                <?php } ?>
                        </div>
                        <?php //} ?>
                        <?php
                        /*
                        <div class="entry-content">
                            <h3>Imóveis Semelhantes</h3>
                        </div>
                        */
                        ?>

                        <footer class="entry-footer">
                            Publicado por <?php the_author_posts_link(); ?> em <?php the_time('F jS, Y'); ?> - <?php the_category(', '); ?> <?php edit_post_link(__('{Editar}'), ''); ?>
                        </footer><!-- .entry-footer -->

                        <?php if ( ! is_singular( 'attachment' ) ) : ?>
                            <?php get_template_part( 'template-parts/post/author', 'bio' ); ?>
                        <?php endif; ?>

                    </article><!-- #post-<?php the_ID(); ?> -->
                                <?php

                                    endwhile;
                                ?>
        </div><!-- #content -->
    </div><!-- #primary -->
</div><!-- #main-content -->

<?php
//get_sidebar();
get_footer();