<div class="your-cart-main">
	<div class="your-cart-title">
		<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('your_cart') ?></h3>
		<h6><?php echo count($cart_details['cart_items']); ?> <?php echo $this->lang->line('items') ?></h6>
	</div>
	<?php if (!empty($cart_details['cart_items'])) {
		$sd_calculation = 0;
		$sd_total = 0; ?>
		<div class="add-cart-list-main type-food-option">
			<?php foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
				<div class="add-cart-list">
					<div class="cart-list-content <?php echo ($value['is_veg'] == 1) ? 'veg' : 'non-veg'; ?>">
						<h5><?php echo $value['name']; ?></h5>
						<?php $vat = $value['vat']; ?>
						<?php $sd = $value['sd']; ?>
						<ul class="ul-disc">
							<?php
							if (!empty($value['variation_list'])) {
								foreach ($value['variation_list'] as $kk => $var) {
							?>
									<h6><strong>Variation: </strong><?php echo $var['variation_name']; ?></h6>
									<?php if (!empty($var['addons_category_list'])) {
										foreach ($var['addons_category_list'] as $key => $cat_value) { ?>
											<li>
												<h6><?php echo $cat_value['addons_category']; ?></h6>
											</li>
											<ul class="ul-cir">
												<?php if (!empty($cat_value['addons_list'])) {
													foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
														<li><?php echo $add_value['add_ons_name']; ?> <?php echo $currency_symbol->currency_symbol; ?> <?php echo $add_value['add_ons_price']; ?></li>
												<?php }
												} ?>
											</ul>
							<?php }
									}
								}
							} ?>
							<?php if (!empty($value['addons_category_list'])) {
								foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
									<li>
										<h6><?php echo $cat_value['addons_category']; ?></h6>
									</li>
									<ul class="ul-cir">
										<?php if (!empty($cat_value['addons_list'])) {
											foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
												<li><?php echo $add_value['add_ons_name']; ?> <?php echo $currency_symbol->currency_symbol; ?> <?php echo $add_value['add_ons_price']; ?></li>
										<?php }
										} ?>
									</ul>
							<?php }
							} ?>
						</ul>

					</div>
					<div class="add-cart-item">
						<strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $value['totalPrice']; ?></strong>
						<div class="number">
							<span class="minus" id="minusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
							<input type="text" value="<?php echo $value['quantity']; ?>" class="pointer-none" />
							<span class="plus" id="plusQuantity" onclick="customItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
						</div>

					</div>
					<!-- <div class="add-cart-item">
						<strong>Vat</strong>
						<strong class="price"><?php echo $currency_symbol->currency_symbol; ?> <?php echo $value['vat']; ?></strong>
					</div> -->
				</div>
			<?php
				//$sd_calculation = $sd_calculation + $sd;

				$sd_total = round($value['totalPrice'] * ($sd / 100)) + $sd_total;

				//$sd_calculation = 0;  //cause different item has different sd;
			} ?>
		</div>

		<div class="cart-subtotal">
			<?php $total_vat = round($cart_details['cart_total_price'] * ($vat / 100)); ?>
			<strong><?php echo $this->lang->line('vat') ?></strong>
			<strong class="price"><?php echo $currency_symbol->currency_symbol; ?> <?php echo $total_vat; ?></strong>
		</div>
		<div class="cart-subtotal">

			<strong><?php echo $this->lang->line('sd') ?></strong>
			<strong class="price"><?php echo $currency_symbol->currency_symbol; ?> <?php echo $sd_total; ?></strong>
		</div>
		<div class="cart-subtotal">
			<?php $total_price = $cart_details['cart_total_price'] + $total_vat + $sd_total ?>
			<strong><?php echo $this->lang->line('sub_total') ?></strong>
			<strong class="price"><?php echo $currency_symbol->currency_symbol; ?> <?php echo $total_price; ?></strong>
		</div>

		<div class="continue-btn">
			<a href="<?php echo base_url() . 'checkout'; ?>"><button class="btn"><?php echo $this->lang->line('continue') ?></button></a>
		</div>
	<?php } else { ?>
		<div class="cart-empty text-center">
			<img src="<?php echo base_url(); ?>assets/front/images/empty-cart.png">
			<h6><?php echo $this->lang->line('cart_empty') ?> <br> <?php echo $this->lang->line('add_some_dishes') ?></h6>
		</div>
	<?php } ?>
</div>

<script type="text/javascript">
	var count = '<?php echo count($cart_details['cart_items']); ?>';
	$('#cart_count').html(count);
</script>