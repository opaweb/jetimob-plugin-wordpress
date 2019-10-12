<?php
 $gallery = get_post_meta( get_the_ID(), REALIA_PROPERTY_PREFIX . 'gallery', true ); 
 $gg =  maybe_unserialize($gallery);
?>


<?php if ( ! empty( $gallery ) ) : ?>
    <div class="property-gallery">
        <div class="property-gallery-preview">           
            <img src="<?php echo $gg[0]; ?>">                
            

            <?php $is_sticky = get_post_meta( get_the_ID(), REALIA_PROPERTY_PREFIX . 'sticky', true ); ?>
            <?php $is_featured = get_post_meta( get_the_ID(), REALIA_PROPERTY_PREFIX . 'featured', true ); ?>
            <?php $is_reduced = get_post_meta( get_the_ID(), REALIA_PROPERTY_PREFIX . 'reduced', true ); ?>

            <?php if ( $is_featured && $is_reduced ) : ?>
                <span class="property-badge"><?php echo __( 'Featured', 'realia' ); ?> / <?php echo __( 'Reduced', 'realia' ); ?></span>
            <?php elseif ( $is_featured ) : ?>
                <span class="property-badge"><?php echo __( 'Featured', 'realia' ); ?></span>
            <?php elseif ( $is_reduced ) : ?>
                <span class="property-badge"><?php echo __( 'Reduced', 'realia' ); ?></span>
            <?php endif; ?>

            <?php if ( $is_sticky ) : ?>
                <span class="property-badge property-badge-sticky"><?php echo __( 'TOP', 'realia' ); ?></span>
            <?php endif; ?>
        </div>

        <ul class="property-gallery-index">
            <?php $index = 0; ?>
            <?php foreach ( $gg as $id => $src ) : ?>
                <li <?php echo ( 0 == $index ) ? 'class="active"' : ''; ?>>
                    <a rel="<?php echo esc_url( $src ); ?>"><?php echo __( 'Show', 'realia' ); ?></a>
                    <?php $index++; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div><!-- /.property-gallery -->
<?php endif; ?>