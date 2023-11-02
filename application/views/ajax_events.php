<?php if (!empty($restaurants)) {
	foreach ($restaurants as $key => $value) { ?>
		<div class="col-sm-12 col-md-6 col-lg-3">
			<div class="popular-rest-box">
				<a href="<?php echo base_url().'restaurant/event-booking-detail/'.$value['restaurant_slug'];?>">
					<div class="popular-rest-img">
						<img src="<?php echo ($value['image'])?$value['image']:default_img;?>" alt="<?php echo $value['name']; ?>">
						<?php echo ($value['ratings'] > 0)?'<strong>'.$value['ratings'].'</strong>':'<strong class="newres">'. $this->lang->line("new") .'</strong>'; ?> 
						<div class="openclose-btn">
							<div class="openclose <?php echo ($value['timings']['closing'] == "Closed")?"closed":""; ?>"> <?php echo ($value['timings']['closing'] == "Closed")?$this->lang->line('closed'):$this->lang->line('open'); ?> </div>
							<!--<?php //echo $value['timings']['closing']; ?>-->
						</div>
					</div>
					<div class="popular-rest-content">
						<h3><?php echo $value['name']; ?></h3>
						<div class="popular-rest-text">
							<p class="address-icon"><?php echo $value['address']; ?> </p>
						</div>
					</div>
				</a>
			</div>
		</div>
	<?php } ?>
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div class="pagination" id="#pagination"><?php echo $PaginationLinks; ?></div>
	</div>
<?php } 
else { ?>
	<div class="col-sm-12 col-md-6 col-lg-4">
		<h5><?php echo $this->lang->line('no_res_found') ?></h5>
	</div>
<?php }?>
	