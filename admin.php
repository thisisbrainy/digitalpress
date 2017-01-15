<script>
var clientToken = '<?php echo Braintree_ClientToken::generate(); ?>';
</script>

<?php if(!empty($_GET['dp-message'])): ?>

	<div class="notice"><?php echo htmlspecialchars($_GET['dp-message']); ?></div>

<?php endif; ?>

<div id="pressify">

	<div class="pressify-content">

		<div class="dp-dashboard">

			<div class="dp-dashboard-col">

				<?php if(current_user_can('manage_options')): ?>
				<div class="dp-dashboard-col-nav">
					<div class="dp-right">
						<a href="javascript:;" id="dp-purchase-runtime-dropdown"><i class="fa fa-eur"></i> Purchase</a>
						<a href="https://digitalpress.co/help/what-is-runtime/" target="_blank" class="help"><i class="fa fa-question"></i></a>
					</div>
				</div>

				<div class="dp-purchase-runtime-dropdown">
			
						<h3>Purchase Runtime</h3>

						<div class="payment-form">
								
							<div class="pf-row">
								<div class="label">Runtime days</div>
								<input class="value" type="number" min="30" value="30">
							</div>

							<div class="pf-row">
								<div class="label">Total cost</div>
								<div class="value" style="background:#fff;">€ <span data-pf-value>7.5</span></div>
							</div>

							<h3 style="margin-top: 40px;">Credit Card Information</h3>

							<p>We do not store any credit card information.</p>

							<form id="checkout" method="post" class="form" action="https://digitalpress.co/?dp=purchase-runtime">
								<div id="payment-form" style="margin-bottom:10px;"></div>
								<input type="hidden" name="payment-method-nonce">
								<input type="hidden" name="back-to" value="https://digitalpress.co<?php echo $_SERVER['REQUEST_URI']; ?>">
								<input type="hidden" name="dp-runtime-days" value="30">
								<input type="hidden" name="site-id" value="<?php echo get_current_blog_id(); ?>">
								<input type="submit" class="dp-btn" value="Purchase">
							</form>

						</div>

					</div>
				<?php endif; ?>

				<h4>Runtime</h4>

				<div class="dp-dashboard-col-no" data-dashboard-view="runtime">0</div>

				<p>days left</p>

			</div>

			<div class="dp-dashboard-col">

				<?php if(current_user_can('manage_options')): ?>
				<div class="dp-dashboard-col-nav">
					<div class="dp-right">
						<a href="javascript:;" id="dp-purchase-storage-dropdown"><i class="fa fa-eur"></i> Purchase</a>
					</div>
				</div>
				<?php endif; ?>

				<h4>Space</h4>

				<div class="dp-dashboard-col-no" data-dashboard-view="space">100%</div>

				<p>of <?php echo get_space_allowed() * (1/1024); ?>GB left</p>

			</div>

			<div class="dp-dashboard-col">

				<h4>Content</h4>

				<div class="dp-dashboard-col-no"><?php echo number_format(wp_count_posts()->publish + wp_count_posts('page')->publish); ?></div>

				<p>published posts and pages</p>

			</div>

			<div class="dp-dashboard-col" id="views-col">

				<div class="dp-dashboard-col-nav">
					<div class="dp-right">
						<a href="javascript:;" id="dp-dashboard-views-last-month">Last month</a>
						<a href="javascript:;" id="dp-dashboard-views-this-month" class="is-active">This month</a>
					</div>
				</div>

				<h4>Views</h4>

				<div class="dp-dashboard-col-no" data-dashboard-view-name="this-month" data-dashboard-view="views">0</div>
				<div class="dp-dashboard-col-no hidden" data-dashboard-view-name="last-month" data-dashboard-view="previous_views">0</div>

				<p>clicks <span data-dashboard-view-name="this-month">this</span><span class="hidden" data-dashboard-view-name="last-month">last</span> month</p>

			</div>

			<div class="dp-dashboard-col" id="people-col">

				<div class="dp-dashboard-col-nav">
					<div class="dp-right">
						<a href="javascript:;" id="dp-dashboard-people-last-month">Last month</a>
						<a href="javascript:;" id="dp-dashboard-people-this-month" class="is-active">This month</a>
					</div>
				</div>

				<h4>People</h4>

				<div class="dp-dashboard-col-no" data-dashboard-view-name="this-month" data-dashboard-view="people">0</div>
				<div class="dp-dashboard-col-no hidden" data-dashboard-view-name="last-month" data-dashboard-view="previous_people">0</div>

				<p>unique visits <span data-dashboard-view-name="this-month">this</span><span class="hidden" data-dashboard-view-name="last-month">last</span> month</p>

			</div>

			<div class="dp-dashboard-col dp-dashboard-col-blog">

				<h4>DigitalPress Blog</h4>

				<div class="dp-dashboard-col-no"><a target="_blank" href="<?php echo dp_main_site_latest_post()->url; ?>"><?php echo dp_main_site_latest_post()->title; ?></a></div>

				<p><?php echo date('F jS, Y', strtotime(dp_main_site_latest_post()->date)); ?></p>

			</div>

		</div>

	</div> <!-- // pressify-content -->

</div>