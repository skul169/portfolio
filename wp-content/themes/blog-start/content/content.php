
	<?php if ( is_singular( get_post_type() ) ) : // If viewing a single post. ?>
	
	<article <?php hybrid_attr( 'post' ); ?>>
		
		<header class="entry-header">

			<h1 <?php hybrid_attr( 'entry-title' ); ?>><?php single_post_title(); ?></h1>
			

			<div class="entry-byline">
				
			</div><!-- .entry-byline -->

		</header><!-- .entry-header -->

		<div <?php hybrid_attr( 'entry-content' ); ?>>
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">

			<time <?php hybrid_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
			<?php esc_html_e('by', 'blog-start' ); ?>
			<span <?php hybrid_attr( 'entry-author' ); ?>><?php the_author_posts_link(); ?></span>
				
				<?php edit_post_link(); ?>	
				<?php hybrid_post_terms( array( 'taxonomy' => 'category', 'text' => esc_html__( 'Categories:	 %s', 'blog-start' ), 'before' => '<br />' ) ); ?>
			<?php hybrid_post_terms( array( 'taxonomy' => 'post_tag', 'text' => esc_html__( 'Taggs:	 %s', 'blog-start' ), 'before' => '<br />' ) ); ?>
		</footer><!-- .entry-footer -->
	</article>	
	
	<?php else : // If not viewing a single post. ?>
	<article class="colum" <?php hybrid_attr( 'post' ); ?>>

		<header class="entry-header-archive">
			<?php hybrid_post_terms( array( 'taxonomy' => 'category' ) ); ?>
			<?php the_title( '<h2 ' . hybrid_get_attr( 'entry-title' ) . '><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">', '</a></h2>' ); ?>
			<footer class="entry-footer">
			<time <?php hybrid_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
			|
			<span <?php hybrid_attr( 'entry-author' ); ?>><?php the_author_posts_link(); ?></span>
	
			</footer>

		</header><!-- .entry-header -->
			<?php get_the_image(); ?>
		<div <?php hybrid_attr( 'entry-summary' ); ?>>
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	
	<?php endif; // End single post check. ?>

</article><!-- .entry -->
