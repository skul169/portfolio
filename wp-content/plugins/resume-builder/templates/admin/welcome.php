<div id="rbuilder-welcome-screen">
	<div class="wrap about-wrap">
		<h1><?php echo sprintf( __( 'Thanks for using %s.', 'resume-builder'), 'Resume Builder' ); ?></h1>
		<div class="about-text">
			<?php echo sprintf(__('If this is your first time using %s, head over to the %s page for some initial configuration. If you just recently updated, you can find out what\'s new below. Go and %s!','resume-builder'),'Resume Builder','<a href="' . untrailingslashit( admin_url() ) . '/admin.php?page=rbuilder_settings">' . esc_html__( 'Settings', 'resume-builder' ) . '</a>', '<a href="' . untrailingslashit( admin_url() ) . '/post-new.php?post_type=rb_resume">' . esc_html__( 'create a resume', 'resume-builder' ) . '</a>' ); ?>
		</div>
		<div class="rbuilder-badge">
			<img src="<?php echo apply_filters( 'rbuilder_welcome_badge_img', RBUILDER_URL . '/assets/admin/images/badge.png' ); ?>">
		</div>

		<div id="welcome-panel" class="welcome-panel">

			<img src="<?php echo apply_filters( 'rbuilder_welcome_banner_img', RBUILDER_URL . '/assets/admin/images/welcome-banner.jpg' ); ?>" class="rbuilder-welcome-banner">

			<div class="welcome-panel-content">
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h3><?php esc_html_e( 'Quick Links', 'resume-builder' ); ?></h3>
						<ul>
							<li><i class="far fa-pencil fa-fw"></i>&nbsp;&nbsp;<a href="<?php echo admin_url('post-new.php?post_type=rb_resume'); ?>"><?php esc_html_e('Create a Resume','resume-builder'); ?></a></li>
							<li><i class="far fa-cog fa-fw"></i>&nbsp;&nbsp;<a href="<?php echo admin_url('admin.php?page=rbuilder_settings'); ?>"><?php esc_html_e('Plugin Settings','resume-builder'); ?></a></li>
							<li><i class="far fa-link fa-fw"></i>&nbsp;&nbsp;<a href="https://demos.boxystudio.com/resume-builder/shortcodes/" target="_blank"><?php esc_html_e( 'Shortcode Examples','resume-builder' ); ?></a></li>
							<li><i class="far fa-link fa-fw"></i>&nbsp;&nbsp;<a href="https://demos.boxystudio.com/resume-builder/widgets/" target="_blank"><?php esc_html_e( 'Widget Examples','resume-builder' ); ?></a></li>
						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<?php do_action( 'rbuilder_welcome_before_changelog' ); ?>
						<?php echo Resume_Builder_Functions::parse_readme_changelog(); ?>
						<?php do_action( 'rbuilder_welcome_after_changelog' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
