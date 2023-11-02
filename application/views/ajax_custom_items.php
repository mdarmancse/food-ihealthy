<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<!-- Modal Header -->
		<div class="modal-header">
			<h4 class="modal-title"><?php echo ($result[0]) ? $result[0]['items'][0]['name'] : ''; ?></h4>
			<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
		</div>
		<!-- Modal body -->
		<div class="modal-body">
			<form id="custom_items_form">
				<div class="popup-radio-btn-main">
					<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $result[0]['items'][0]['restaurant_id']; ?>">
					<input type="hidden" name="user_id" id="user_id" value="<?php echo ($this->session->userdata('UserID')) ? $this->session->userdata('UserID') : ''; ?>">
					<div class="item-price-label">
						<span><?php echo $this->lang->line('item') ?></span>
						<span><?php echo $this->lang->line('price') ?></span>
					</div>
					<?php if (!$result[0]['items'][0]['has_variation'] || $result[0]['items'][0]['has_variation'] == 0) { ?>
						<?php if (!empty($result[0]['items'][0]['addons_category_list'])) {
							foreach ($result[0]['items'][0]['addons_category_list'] as $key => $value) { ?>

								<div class="radio-btn-box">
									<div class="customizable-title">
										<h5><?php echo $value['addons_category']; ?></h5>
									</div>
									<?php if (!empty($value['addons_list'])) {
										foreach ($value['addons_list'] as $key => $addvalue) { ?>
											<div class="radio-btn-list">
												<label>
													<?php if ($value['is_multiple'] == 1) { ?>
														<input type="checkbox" class="check_addons" name="<?php echo $value['addons_category'] . '-' . $key; ?>" id="<?php echo $addvalue['add_ons_name'] . '-' . $key; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addvalue['add_ons_price']; ?>,<?php echo $value['is_multiple']; ?>)" amount="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>">
													<?php } else { ?>
														<input type="radio" class="radio_addons" name="<?php echo $value['addons_category']; ?>" id="<?php echo $addvalue['add_ons_name'] . '-' . $key; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addvalue['add_ons_price']; ?>,<?php echo $value['is_multiple']; ?>)" amount="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>">
													<?php } ?>
													<span><?php echo $addvalue['add_ons_name']; ?></span>
												</label>
												<span><?php echo $currency_symbol->currency_symbol; ?> <?php echo $addvalue['add_ons_price']; ?></span>
											</div>
									<?php }
									} ?>
								</div>
					<?php }
						}
					}
					?>

					<?php if ($result[0]['items'][0]['has_variation'] && $result[0]['items'][0]['has_variation'] == 1) { ?>
						<input type="hidden" name="has_variation" id="has_variation" value="1">
						<h5><strong>Variations</strong></h5>
						<div class="radio-btn-box">
							<?php
							if (!empty($result[0]['items'][0]['variation_list'])) {

								foreach ($result[0]['items'][0]['variation_list'] as $key => $addvalue) { ?>
									<div class="radio-btn-list">
										<label>

											<input type="radio" class="radio_variations" variation_name="<?php echo $addvalue['variation_name']; ?>" name="<?php echo "variation"; ?>" id="<?php echo "variation-" . $addvalue['variation_id'] ?>" value="<?= $addvalue['variation_id'] ?>" onchange="getItemPrice(this.id,<?php echo $addvalue['variation_price']; ?>,0, 'variation')" amount="<?php echo $addvalue['variation_price']; ?>" variation_id="<?php echo $addvalue['variation_id']; ?>">
											<span><?php echo $addvalue['variation_name']; ?></span>
										</label>
										<span><?php echo $currency_symbol->currency_symbol; ?> <?php echo $addvalue['variation_price']; ?></span>
									</div>
								<?php } ?>

								<h5><strong>Addons</strong></h5>
								<?php foreach ($result[0]['items'][0]['variation_list'] as $v_key => $each_variation) { ?>
									<?php if (!empty($each_variation['addons_category_list'])) {
										foreach ($each_variation['addons_category_list'] as $key => $value) { ?>

											<div class="radio-btn-box var_addon_div variation-<?= $each_variation["variation_id"] ?>" style="display: none;">
												<div class="customizable-title">
													<h5><?php echo $value['addons_category']; ?></h5>
												</div>
												<?php if (!empty($value['addons_list'])) {
													foreach ($value['addons_list'] as $key => $addvalue) { ?>
														<div class="radio-btn-list">
															<label>
																<?php if ($value['is_multiple'] == 1) { ?>
																	<input type="checkbox" class="check_addons var_check" name="<?php echo $value['addons_category'] . '-' . $key; ?>" id="<?php echo $addvalue['add_ons_name'] . '-' . $key; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addvalue['add_ons_price']; ?>,<?php echo $value['is_multiple']; ?>)" amount="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>">
																<?php } else { ?>
																	<input type="radio" class="radio_addons var_radio" name="<?php echo $value['addons_category']; ?>" id="<?php echo $addvalue['add_ons_name'] . '-' . $key; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addvalue['add_ons_price']; ?>,<?php echo $value['is_multiple']; ?>)" amount="<?php echo $addvalue['add_ons_price']; ?>" add_ons_id="<?php echo $addvalue['add_ons_id']; ?>" addons_category_id="<?php echo $value['addons_category_id']; ?>" add_ons_name="<?php echo $addvalue['add_ons_name']; ?>" addonValue='<?php echo json_encode($addvalue); ?>' addons_category="<?php echo $value['addons_category']; ?>">
																<?php } ?>
																<span><?php echo $addvalue['add_ons_name']; ?></span>
															</label>
															<span><?php echo $currency_symbol->currency_symbol; ?> <?php echo $addvalue['add_ons_price']; ?></span>
														</div>
												<?php }
												} ?>
											</div>
									<?php }
									} ?>
								<?php } ?>
							<?php
							} ?>
						</div>
					<?php } ?>
				</div>
				<div class="popup-total-main">
					<div class="popup-total">
						<h2><?php echo $this->lang->line('total') ?></h2>
					</div>
					<div class="total-price">
						<input type="hidden" name="subTotal" id="subTotal" value="0">
						<strong><?php echo $currency_symbol->currency_symbol; ?> <span id="totalPrice">0</span></strong>
						<!-- onclick="AddToCart('<?php //echo $result[0]['items'][0]['menu_id'];
													?>')" -->
						<button type="button" class="addtocart btn addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" id="addtocart-<?php echo $result[0]['items'][0]['menu_id']; ?>" onclick="AddAddonsToCart('<?php echo $result[0]['items'][0]['menu_id']; ?>',this.id)"><?php echo $this->lang->line('add') ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	//get item price
	var totalPrice = 0;
	var radiototalPrice = 0;
	var checktotalPrice = 0;
</script>