			<footer class="footer" itemscope itemtype="http://schema.org/WPFooter">

				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="copyright">
								<p>
                                                                    <span class="css-br-tb"><?php _e('Disctionaries : Copyright by Merriam Webster, All right Reserved.', 'iii-dictionary') ?></span>
                                                                    <span><?php _e('Software and graphics : Copy right by Innovative Knowledge, Inc', 'iii-dictionary') ?></span>
                                                                    <span class="css-br-mob"><?php _e('All right Reserved.', 'iii-dictionary') ?></span>
                                                                </p>
								
								<div class="divider"></div>
								<a href="<?php echo site_home_url(); ?>?r=about-us" rel="nofollow" title="Innovative Knowledge">
									<img src="<?php echo get_template_directory_uri(); ?>/library/images/ik-logo.png" alt="">
								</a>
							</div>
						</div>
					</div>
				</div>

			</footer>

		</div>

		<?php add_action('wp_footer', 'print_js_messages') ?>
		<?php wp_footer(); ?>

	</body>

</html> <!-- end of site. what a ride! -->
