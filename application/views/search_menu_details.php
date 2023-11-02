<?php $menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
}
if (!empty($restaurant_details['menu_items']) || !empty($restaurant_details['packages']) || !empty($restaurant_details['categories']))
{
	if (!empty($restaurant_details['categories'])) {?>
		<div class="slider-checkbox-main">
			<div class="pn-ProductNav_Wrapper">
				<button id="pnAdvancerLeft" class="pn-Advancer pn-Advancer_Left" type="button"><i class="iicon-icon-16"></i></button>
				<nav id="pnProductNav" class="pn-ProductNav">
				    <div id="pnProductNavContents" class="pn-ProductNav_Contents">
		    			<?php foreach ($restaurant_details['categories'] as $key => $value) {?>
		    				<div class="slider-checkbox" aria-selected="true">
					    		<label>
					    			<input class="check-menu" type="checkbox" name="checkbox-option" id="checkbox-option-<?php echo $value['category_id']; ?>" onclick="menuSearch(<?php echo $value['category_id']; ?>)">
					    			<span><?php echo $value['name']; ?></span>
					    		</label>
					    	</div>
		    			<?php }?>
						<span id="pnIndicator" class="pn-ProductNav_Indicator"></span>
				    </div>
				</nav>
				<button id="pnAdvancerRight" class="pn-Advancer pn-Advancer_Right" type="button"><i class="iicon-icon-17"></i></button>
			</div>
		</div>
	<?php }?>
	<div class="option-filter-tab">
		<div class="custom-control custom-checkbox">  
			<input type="radio" name="filter_food" class="custom-control-input" id="filter_veg" value="filter_veg" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="filter_veg"><?php echo $this->lang->line('veg') ?></label>
		</div>
		<div class="custom-control custom-checkbox">
			<input type="radio" name="filter_food" class="custom-control-input" id="filter_non_veg" value="filter_non_veg" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="filter_non_veg"><?php echo $this->lang->line('non_veg') ?></label>
		</div>
		<div class="custom-control custom-checkbox">
			<input type="radio" checked="checked" name="filter_food" class="custom-control-input" id="all" value="all" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="all"><?php echo $this->lang->line('view_all') ?></label>
		</div>
		<div class="custom-control custom-checkbox">
		    <input type="radio" checked="checked" name="filter_price" class="custom-control-input" id="filter_high_price" value="filter_high_price" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>)">
		    <label class="custom-control-label" for="filter_high_price"><?php echo $this->lang->line('sort_by_price_low') ?></label>
	  	</div>
		<div class="custom-control custom-checkbox">
			<input type="radio" name="filter_price" class="custom-control-input" id="filter_low_price" value="filter_low_price" onclick="menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>)">
			<label class="custom-control-label" for="filter_low_price"><?php echo $this->lang->line('sort_by_price_high') ?></label>
		</div>
	</div>
	<div id="res_detail_content">
		<?php if (!empty($restaurant_details['menu_items'])) {
	        $popular_count = 0;
	        foreach ($restaurant_details['menu_items'] as $key => $value) {
	            if ($value['popular_item'] == 1) {
	                $popular_count = $popular_count + 1;
	            }
	        }
	        if ($popular_count > 0) { ?>
				<div class="detail-list-box-main">
					<div class="detail-list-title">
						<h3><?php echo $this->lang->line('popular_items') ?></h3>
					</div>
					<?php foreach ($restaurant_details['menu_items'] as $key => $value) {
						if ($value['popular_item'] == 1) { ?>
							<div class="detail-list-box">
							 	<div class="detail-list">
									<div class="detail-list-img">
										<div class="list-img">
											<img src="<?php echo ($value['image']) ? $value['image'] : default_img; ?>">
											<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
										</div>
									</div>
									<div class="detail-list-content">
										<div class="detail-list-text">
											<h4><?php echo $value['name']; ?></h4>
											<p><?php echo $value['menu_detail']; ?></p>
											<strong><?php echo ($value['check_add_ons'] != 1)?'$'.$value['price']:''; ?></strong>
										</div>
										<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
											if ($value['check_add_ons'] == 1) {?>
												<div class="add-btn">
													<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?>  onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)"> <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
													<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
												</div>
											<?php } else {?>
												<div class="add-btn">
													<?php $add = (in_array($value['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($value['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
												</div>
										<?php } } ?>
									</div>
								</div>
							</div>
						<?php }
					}?>
				</div>
			<?php }?>
		<?php }?>
		<?php if (!empty($restaurant_details['categories'])) {
	        foreach ($restaurant_details['categories'] as $key => $value) { ?>
				<div class="detail-list-box-main categories" id="category-<?php echo $value['category_id']; ?>" >
					<div class="detail-list-title">
						<h3><?php echo $value['name']; ?></h3>
					</div>
					<div class="detail-list-box type-food-option">
						<?php if ($restaurant_details[$value['name']]) {
    						foreach ($restaurant_details[$value['name']] as $key => $mvalue) {?>
								<div class="detail-list <?php echo ($mvalue['is_veg'] == 1) ? 'veg' : 'non-veg'; ?>">
									<div class="detail-list-content">
										<div class="detail-list-text">
											<h4><?php echo $mvalue['name']; ?></h4>
											<p><?php echo $mvalue['menu_detail']; ?></p>
											<strong><?php echo ($mvalue['check_add_ons'] != 1)?'$'.$mvalue['price']:''; ?></strong>
										</div>
										<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
											if ($mvalue['check_add_ons'] == 1) {?>
												<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
												<div class="add-btn">
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)"> <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
													<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
												</div>
											<?php } else {?>
												<div class="add-btn">
												<?php $add = (in_array($mvalue['entity_id'], $menu_ids))?'Added':'Add'; ?>
													<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed")?'disabled':''; ?> > <?php echo (in_array($mvalue['entity_id'], $menu_ids))?$this->lang->line('added'):$this->lang->line('add'); ?>  </button>
												</div>
										<?php } } ?>
									</div>
								</div>
							<?php }
						}?>
					</div>
				</div>
			<?php }
		} ?>
	</div>
<?php } 
else {?>
<div class="slider-checkbox-main">
	<div class="detail-list-title">
		<h3><?php echo $this->lang->line('no_results_found') ?></h3>
	</div>
</div>
<?php }?>

<script type="text/javascript">
	menuFilter(<?php echo $restaurant_details['restaurant'][0]['content_id']; ?>);
</script>