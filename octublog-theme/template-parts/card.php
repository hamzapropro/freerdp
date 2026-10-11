<?php
/**
 * Article card. Args: post (WP_Post), quote (bool), heading (tag name).
 *
 * @package Octublog
 */

$ob_post    = $args['post'] ?? get_post();
$ob_cat     = ob_post_category( $ob_post );
$ob_rub     = ob_rubrique( $ob_cat );
$ob_author  = ob_post_author( $ob_post );
$ob_heading = tag_escape( $args['heading'] ?? 'h3' );
$ob_title   = get_the_title( $ob_post );

if ( ! empty( $args['quote'] ) ) :
	?>
	<a class="ob-card ob-card--quote" href="<?php echo esc_url( get_permalink( $ob_post ) ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
		<span class="ob-card__mark" aria-hidden="true">«</span>
		<span class="ob-card__quote-body">
			<span class="ob-card__quote"><?php echo esc_html( ob_summary( $ob_post, 22 ) ); ?></span>
			<<?php echo $ob_heading; // phpcs:ignore ?> class="ob-card__qtitle"><?php echo esc_html( $ob_title ); ?></<?php echo $ob_heading; // phpcs:ignore ?>>
		</span>
		<span class="ob-card__meta">
			<?php echo ob_avatar( $ob_author, 32, 'ob-avatar--ring' ); // phpcs:ignore ?>
			<strong><?php echo esc_html( $ob_author['name'] ); ?></strong>
		</span>
	</a>
	<?php
	return;
endif;
?>
<a class="ob-card" href="<?php echo esc_url( get_permalink( $ob_post ) ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
	<span class="ob-card__media"><?php echo ob_thumb( $ob_post ); // phpcs:ignore ?></span>
	<span class="ob-card__body">
		<?php if ( $ob_cat ) : ?>
			<span class="ob-pill"><?php echo esc_html( $ob_cat->name ); ?></span>
		<?php endif; ?>
		<<?php echo $ob_heading; // phpcs:ignore ?> class="ob-card__title"><?php echo esc_html( $ob_title ); ?></<?php echo $ob_heading; // phpcs:ignore ?>>
		<span class="ob-card__meta">
			<?php echo ob_avatar( $ob_author, 32 ); // phpcs:ignore ?>
			<strong><?php echo esc_html( $ob_author['name'] ); ?></strong>
			<span aria-hidden="true">·</span>
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $ob_post ) ); ?>"><?php echo esc_html( ob_date( $ob_post ) ); ?></time>
		</span>
	</span>
</a>
