<?php /* Template Name: resultados da busca */ ?>

<?php
//get_header();
?>


	<section id="primary" class="content-area">
		<main id="main" class="site-main">

			<header class="page-header">
				<h1 class="page-title">Resultados da Busca</h1>
			</header><!-- .page-header -->

			<?php
			// Start the Loop.
            // WP_Query arguments
            $tax_query = array(
                    array(
                        'taxonomy'         => 'tipo_imovel',
                        'terms'            => $_REQUEST['tipo_imovel'],
                        'field'            => 'slug',
                    ),
                    array(
                        'taxonomy'         => 'localizacao',
                        'terms'            => $_REQUEST['localizacao'],
                        'field'            => 'slug',
                        'include_children' => true,
                    ),
                    array(
                        'taxonomy'         => 'comodidade_imovel',
                        'terms'            => $_REQUEST['comodidade_imovel'],
                        'field'            => 'slug',
                        'operator'         => 'AND',
                    ),
                    array(
                        'taxonomy'         => 'comodidade_condominio',
                        'terms'            => $_REQUEST['comodidade_condominio'],
                        'field'            => 'slug',
                        'operator'         => 'AND',
                    ),
                );

            $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'dormitorios',
                        'value'   => $_REQUEST['dormitorios'],
                        'compare' => '=',
                        'type'    => 'NUMERIC',
                    ),
                    array(
                        'key'     => 'suites',
                        'value'   => $_REQUEST['suites'],
                        'compare' => '=',
                        'type'    => 'NUMERIC',
                    ),
                    array(
                        'key'     => 'banheiros',
                        'value'   => $_REQUEST['banheiros'],
                        'compare' => '=',
                        'type'    => 'NUMERIC',
                    ),
                    array(
                        'key'     => 'vagas',
                        'value'   => $_REQUEST['vagas'],
                        'compare' => '=',
                        'type'    => 'NUMERIC',
                    ),
                    array(
                        'key'     => 'valor',
                        'value'   => array($_REQUEST['valor_min'],$_REQUEST['valor_max']),
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    ),
                );

            $args = array(
                'post_type'              => array( 'imovel' ),
                'post_status'            => array( 'published' ),
                'nopaging'               => false,
                'posts_per_page'         => '10',
                'order'                  => 'DESC',
                'orderby'                => 'id',
                'tax_query'              => $tax_query,
                'meta_query'             => $meta_query,
            );

            // The Query
            $query = new WP_Query( $args );
            if ( have_posts() ) : 
			while ( have_posts() ) :
				the_post();
                var_dump($post);
				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				//get_template_part( 'template-parts/content/content', 'excerpt' );
				//the_excerpt();

				// End the loop.

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
		
				<article id="post-1" class="status-publish format-standard has-post-thumbnail hentry entry">
					<header class="entry-header">
						<h2 class="entry-title"><a href="<?php echo get_post_permalink(); ?>" rel="bookmark"><?php echo the_title(); ?></a></h2>	</header><!-- .entry-header -->

						<div class="imovel_container" style="display: inline">

							<figure class="post-thumbnail" style="width: 20%; float:left;">
								<a class="post-thumbnail-inner" href="<?php echo get_post_permalink(); ?>" aria-hidden="true" tabindex="-1">
									<?php echo get_the_post_thumbnail(); ?></a>
							</figure>

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

						
							

						</div>
					

					<footer class="entry-footer"></footer><!-- .entry-footer -->
				</article><!-- #post-1 -->


			<?php
			endwhile;

			// Previous/next page navigation.
			the_posts_navigation();

			// If no content, include the "No posts found" template.
		else :
			echo 'Nada encontrado';

		endif;
		?>
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
//get_footer();