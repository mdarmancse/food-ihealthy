<?php if (!empty($restaurants)) {
	foreach ($restaurants as $key => $value) { ?>
		<div class="col-lg-6">
			<div class="restaurant-box">
				<div class="popular-rest-box">
					<div class="popular-rest-img">
						<a href="<?php echo base_url() . 'restaurant/restaurant-detail/' . $value['restaurant_slug']; ?>"><img src="<?php echo ($value['image']) ? $value['image'] : default_img; ?>" alt="<?php echo $value['name']; ?>"></a>
						<div class="openclose <?php echo ($value['timings']['closing'] == "Closed" || $value['timings']['closing'] == "close") ? "closed" : ""; ?>"> <?php echo ($value['timings']['closing'] == "Closed" || $value['timings']['closing'] == "close") ? $this->lang->line('closed') : $this->lang->line('open'); ?></div>
						<!-- <?php //echo $value['timings']['closing'];
								?> -->
						<?php echo ($value['rating'] > 0) ? '<strong>' . $value['rating'] . '</strong>' : '<strong class="newres">' . $this->lang->line("new") . '</strong>'; ?>

					</div>
					<div class="popular-rest-content">
						<h3><?php echo $value['name']; ?></h3>
						<div class="popular-rest-text">
							<p class=""><?php echo $value['address']; ?> </p>
							<div class="order-btn">
								<a href="<?php echo base_url() . 'restaurant/restaurant-detail/' . $value['restaurant_slug']; ?>" class="btn"><?php echo $this->lang->line('order') ?></a>
							</div>
						</div>
						<div class="popular-rest-text">
							<p class="call-icon"><?php echo $value['phone_number']; ?> </p>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
	</div>
<?php } else { ?>
	<div class="no-found">
		<h4><?php echo $this->lang->line('no_res_found'); ?></h4>
	</div>
<?php } ?>
