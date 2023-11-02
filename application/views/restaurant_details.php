<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('header');
$menu_ids = array();
if (!empty($menu_arr)) {
	$menu_ids = array_column($menu_arr, 'menu_id');
} ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/bootstrap-tagsinput.css">
<section class="inner-banner restaurant-detail-banner">
	<div class="container">
		<div class="inner-pages-banner">

		</div>
	</div>
</section>

<section class="inner-pages-section rest-detail-section">
	<div class="rest-detail-main">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="rest-detail">
						<div class="rest-detail-img-main">
							<div class="rest-detail-img">
								<img src="<?php echo ($restaurant_details['restaurant'][0]['image']) ? ($restaurant_details['restaurant'][0]['image']) : (default_img); ?>">
							</div>
						</div>
						<div class="rest-detail-content">
							<h2><?php echo $restaurant_details['restaurant'][0]['name']; ?> </h2>
							<p><i class="iicon-icon-20"></i><?php echo $restaurant_details['restaurant'][0]['address']; ?></p>
							<ul>
								<li><i class="iicon-icon-05"></i><?php echo ($restaurant_details['restaurant'][0]['ratings'] > 0) ? $restaurant_details['restaurant'][0]['ratings'] : '<strong class="newres">' . $this->lang->line("new") . '</strong>'; ?></li>
								<li><i class="iicon-icon-18"></i><?php echo $restaurant_details['restaurant'][0]['timings']['open'] . '-' . $restaurant_details['restaurant'][0]['timings']['close']; ?></li>
								<li><i class="iicon-icon-19"></i><?php echo $restaurant_details['restaurant'][0]['phone_number']; ?></li>
							</ul>
							<?php $closed = ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? 'closed' : ''; ?>
							<div class="openclose <?php echo $closed; ?>"><?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? $this->lang->line('closed') : $this->lang->line('open'); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('order_food_from') ?> <?php echo $restaurant_details['restaurant'][0]['name']; ?></h2>
				</div>
				<div class="menu_review">
					<a href="#" class="active" id="menu_link"><button class="btn res-menu"><?php echo $this->lang->line('menu'); ?></button></a>
					<a href="#" id="review_link"><button class="btn res-review"><?php echo $this->lang->line('review_ratings'); ?></button></a>
				</div>
			</div>
		</div>


		<div class="row restaurant-detail-row">
			<!-- restaurant details start-->
			<div class="col-sm-12 col-md-5 col-lg-8" id="menu" style="display: block;">
				<div class="search-dishes">
					<form action="#" id="#" class="inner-pages-form">

						<div class="form-group search-restaurant">
							<input class="input-tags" type="text" name="search_dish" placeholder="<?php echo $this->lang->line('search_dishes') ?>" id="search_dish">
							<input type="button" name="Search" value="<?php echo $this->lang->line('search') ?>" class="btn" onclick="searchMenuDishes(<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>)">
						</div>
					</form>
				</div>
				<div id="details_content">
					<?php if (!empty($restaurant_details['menu_items']) || !empty($restaurant_details['packages']) || !empty($restaurant_details['categories'])) {
						if (!empty($restaurant_details['categories'])) { ?>
							<div class="slider-checkbox-main">
								<div class="pn-ProductNav_Wrapper">
									<button id="pnAdvancerLeft" class="pn-Advancer pn-Advancer_Left" type="button"><i class="iicon-icon-16"></i></button>
									<nav id="pnProductNav" class="pn-ProductNav">
										<div id="pnProductNavContents" class="pn-ProductNav_Contents">
											<?php foreach ($restaurant_details['categories'] as $key => $value) { ?>
												<div class="slider-checkbox" aria-selected="true">
													<label>
														<input class="check-menu" type="checkbox" name="checkbox-option" id="checkbox-option-<?php echo $value['category_id']; ?>" onclick="menuSearch(<?php echo $value['category_id']; ?>)">
														<span><?php echo $value['name']; ?></span>
													</label>
												</div>
											<?php } ?>
											<span id="pnIndicator" class="pn-ProductNav_Indicator"></span>
										</div>
									</nav>
									<button id="pnAdvancerRight" class="pn-Advancer pn-Advancer_Right" type="button"><i class="iicon-icon-17"></i></button>
								</div>
							</div>
						<?php } ?>
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

						<div class="is_close"> <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? '<span id="closedres">' . $this->lang->line('not_accepting_orders') . '</span>' : ''; ?>
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
																<img src="<?php echo ($value['image']) ? ($value['image']) : (default_img); ?>">
																<div class="label-sticker"><span><?php echo $this->lang->line('popular') ?></span></div>
															</div>
														</div>
														<div class="detail-list-content">
															<div class="detail-list-text">
																<h4><?php echo $value['name']; ?></h4>
																<p><?php echo $value['menu_detail']; ?></p>
																<strong><?php echo ($value['check_add_ons'] != 1) ? $restaurant_details['restaurant'][0]['currency_symbol'] . ' ' . $value['price'] : ''; ?></strong>
															</div>
															<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
																if ($value['check_add_ons'] == 1) { ?>
																	<div class="add-btn">
																		<?php $add = (in_array($value['entity_id'], $menu_ids)) ? 'Added' : 'Add'; ?>
																		<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? 'disabled' : ''; ?> onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)"> <?php echo (in_array($value['entity_id'], $menu_ids)) ? $this->lang->line('added') : $this->lang->line('add'); ?> </button>
																		<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
																	</div>
																<?php } else { ?>
																	<div class="add-btn">
																		<?php $add = (in_array($value['entity_id'], $menu_ids)) ? 'Added' : 'Add'; ?>
																		<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $value['entity_id']; ?>" id="addtocart-<?php echo $value['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $value['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? 'disabled' : ''; ?>> <?php echo (in_array($value['entity_id'], $menu_ids)) ? $this->lang->line('added') : $this->lang->line('add'); ?> </button>
																	</div>
															<?php }
															} ?>
														</div>
													</div>
												</div>
										<?php }
										} ?>
									</div>
								<?php } ?>
							<?php } ?>
							<?php if (!empty($restaurant_details['categories'])) {
								foreach ($restaurant_details['categories'] as $key => $value) { ?>
									<div class="detail-list-box-main categories" id="category-<?php echo $value['category_id']; ?>">
										<div class="detail-list-title">
											<h3><?php echo $value['name']; ?></h3>
										</div>
										<div class="detail-list-box type-food-option">
											<?php if ($restaurant_details[$value['name']]) {
												foreach ($restaurant_details[$value['name']] as $key => $mvalue) { ?>
													<div class="detail-list <?php echo ($mvalue['is_veg'] == 1) ? 'veg' : 'non-veg'; ?>">
														<div class="detail-list-img">
															<div class="list-img">
																<img src="<?php echo ($mvalue['image']) ? (image_url . $mvalue['image']) : (default_img); ?>">

															</div>
														</div>
														<div class="detail-list-content">

															<div class="detail-list-text">
																<h4><?php echo $mvalue['name']; ?></h4>
																<p><?php echo $mvalue['menu_detail']; ?></p>
																<strong><?php echo ($mvalue['check_add_ons'] != 1) ? $restaurant_details['restaurant'][0]['currency_symbol'] . ' ' . $mvalue['price'] : ''; ?></strong>
															</div>
															<?php if ($restaurant_details['restaurant'][0]['timings']['closing'] != "Closed") {
																if ($mvalue['check_add_ons'] == 1) { ?>
																	<?php $add = (in_array($mvalue['entity_id'], $menu_ids)) ? 'Added' : 'Add'; ?>
																	<div class="add-btn">
																		<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? 'disabled' : ''; ?> onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'addons',this.id)"> <?php echo (in_array($mvalue['entity_id'], $menu_ids)) ? $this->lang->line('added') : $this->lang->line('add'); ?> </button>
																		<span class="cust"><?php echo $this->lang->line('customizable') ?></span>
																	</div>
																<?php } else { ?>
																	<div class="add-btn">
																		<?php $add = (in_array($mvalue['entity_id'], $menu_ids)) ? 'Added' : 'Add'; ?>
																		<button class="btn <?php echo strtolower($add); ?> addtocart-<?php echo $mvalue['entity_id']; ?>" id="addtocart-<?php echo $mvalue['entity_id']; ?>" onclick="checkCartRestaurant(<?php echo $mvalue['entity_id']; ?>,<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>,'',this.id)" <?php echo ($restaurant_details['restaurant'][0]['timings']['closing'] == "Closed") ? 'disabled' : ''; ?>> <?php echo (in_array($mvalue['entity_id'], $menu_ids)) ? $this->lang->line('added') : $this->lang->line('add'); ?> </button>
																	</div>
															<?php }
															} ?>
														</div>
													</div>
											<?php }
											} ?>
										</div>
									</div>
							<?php }
							} ?>
						</div>
					<?php } else { ?>
						<div class="slider-checkbox-main">
							<div class="detail-list-title">
								<h3><?php echo $this->lang->line('no_results_found') ?></h3>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<!-- restaurant details end -->
			<!-- ratings and review start -->
			<div class="col-sm-12 col-md-5 col-lg-8" id="review" style="display: none;">
				<div class="detail-list-box-main">
					<div class="detail-list-title">
						<h3><?php echo $this->lang->line('review_ratings') ?></h3>
						<?php if (!empty($this->session->userdata('UserID')) && $remaining_reviews > 0) { ?>
							<button class="btn" onclick="addReview(<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>)"><?php echo $this->lang->line('title_admin_reviewadd'); ?></button>
						<?php } ?>
					</div>
					<div class="rating-review-main">
						<?php if ($this->session->flashdata('review_added')) { ?>
							<div class="alert alert-success" id="review_success"><?php echo $this->session->flashdata('review_added'); ?></div>
						<?php } ?>
						<div class="review-progress">
							<div class="progress-main">
								<div class="review-all">
									<p class="text-center"><?php echo (!empty($restaurant_reviews)) ? count($restaurant_reviews) : 0; ?> <?php echo (!empty($restaurant_reviews)) ? ((count($restaurant_reviews) > 1) ? $this->lang->line('reviews') : $this->lang->line('review')) : $this->lang->line('review'); ?></p>
								</div>
								<?php for ($i = 5; $i > 0; $i--) { ?>
									<div class="progress-box">
										<span class="star-icon"><?php echo $i; ?></span>
										<div class="progress">
											<?php
											$noOfReviews = $this->restaurant_model->getReviewsNumber($restaurant_details['restaurant'][0]['restaurant_id'], $i);
											$percentage = $noOfReviews * 100 / count($restaurant_reviews); ?>
											<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percentage . '%'; ?>">
											</div>
										</div>
										<span><?php echo $noOfReviews; ?></span>
									</div>
								<?php } ?>
							</div>
						</div>
						<div class="rate-restaurant">
							<div class="star-rating-main">
								<div class="star-rating">
									<?php for ($i = 1; $i < 6; $i++) {
										$activeClass = '';
										if ($i <= $restaurant_details['restaurant'][0]['ratings']) {
											$activeClass = 'active'; ?>
										<?php } ?>
										<button class="<?php echo $activeClass; ?>"><i class="iicon-icon-28"></i></button>
									<?php } ?>
								</div>
								<div class="review-all">
									<span><i class="iicon-icon-05"></i><?php echo $restaurant_details['restaurant'][0]['ratings']; ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="review-box-main">
						<div id="limited-reviews">
							<?php if (!empty($restaurant_reviews)) {
								foreach ($restaurant_reviews as $key => $value) {
									if ($key <= 4) { ?>
										<div class="review-list">
											<div class="review-img">
												<div class="user-images">
													<img src="<?php echo ($value['image']) ? $value['image'] : default_img; ?>">
												</div>
											</div>
											<div class="review-content">
												<div class="user-name-date">
													<h3><?php echo $value['first_name'] . ' ' . $value['last_name']; ?></h3>
													<div class="review-star">
														<span><i class="iicon-icon-05"></i><?php echo number_format($value['rating'], 1); ?></span>
													</div>
													<div class="review-date">
														<span><?php echo date("d M Y", strtotime($value['created_date'])); ?></span>
													</div>
												</div>
												<p>"<?php echo ucfirst($value['review']); ?>"</p>
											</div>
										</div>
							<?php }
								}
							} ?>
						</div>
						<div id="all_reviews" class="display-no">
							<?php if (!empty($restaurant_reviews)) {
								foreach ($restaurant_reviews as $key => $value) {
									if ($key > 4) { ?>
										<div class="review-list">
											<div class="review-img">
												<div class="user-images">
													<img src="<?php echo ($value['image']) ? $value['image'] : default_img; ?>">
												</div>
											</div>
											<div class="review-content">
												<div class="user-name-date">
													<h3><?php echo $value['first_name'] . ' ' . $value['last_name']; ?></h3>
													<div class="review-star">
														<span><i class="iicon-icon-05"></i><?php echo number_format($value['rating'], 1); ?></span>
													</div>
													<div class="review-date">
														<span><?php echo date("d M Y", strtotime($value['created_date'])); ?></span>
													</div>
												</div>
												<p>"<?php echo ucfirst($value['review']); ?>"</p>
											</div>
										</div>
							<?php }
								}
							} ?>
						</div>
						<?php if (count($restaurant_reviews) > 4) { ?>
							<button id="review_button" class="btn btn-success danger-btn" onclick="showAllReviews()"><?php echo $this->lang->line('all_reviews') ?></button>
						<?php } ?>
					</div>
				</div>
			</div>
			<!-- ratings and review end -->

			<!-- your cart -->
			<div class="col-sm-12 col-md-5 col-lg-4" id="your_cart">
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
										<?php $sd =  $value['sd']; ?>
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
										<strong><?php echo $restaurant_details['restaurant'][0]['currency_symbol']; ?> <?php echo $value['totalPrice']; ?></strong>
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

								$sd_total = round($value['totalPrice'] * ($sd / 100)) + $sd_total;

								//$sd_calculation = 0; //cause different item has different sd;
							} ?>
						</div>

						<div class="cart-subtotal">
							<?php $total_vat = round($cart_details['cart_total_price'] * ($vat / 100)); ?>
							<strong><?php echo $this->lang->line('vat') ?></strong>
							<strong class="price"><?php echo $restaurant_details['restaurant'][0]['currency_symbol']; ?> <?php echo $total_vat; ?></strong>
						</div>
						<div class="cart-subtotal">

							<strong><?php echo $this->lang->line('sd') ?></strong>
							<strong class="price"><?php echo $restaurant_details['restaurant'][0]['currency_symbol']; ?> <?php echo $sd_total; ?></strong>
						</div>
						<div class="cart-subtotal">
							<?php $total_price = $cart_details['cart_total_price'] + $total_vat + $sd_total ?>
							<strong><?php echo $this->lang->line('sub_total') ?></strong>
							<strong class="price"><?php echo $restaurant_details['restaurant'][0]['currency_symbol']; ?> <?php echo $total_price; ?></strong>
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
			</div>
			<!-- your cart end -->
		</div>




	</div>
</section>

<div class="modal modal-main" id="myconfirmModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h4>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<form id="custom_items_form">
					<h5><?php echo $this->lang->line('menu_already_added') ?> <br> <?php echo $this->lang->line('want_to_add_new_item') ?></h5>
					<div class="popup-radio-btn-main">
						<div class="radio-btn-box">
							<div class="radio-btn-list">
								<label>
									<input type="hidden" name="con_entity_id" id="con_entity_id" value="">
									<input type="hidden" name="con_restaurant_id" id="con_restaurant_id" value="">
									<input type="hidden" name="con_item_id" id="con_item_id" value="">
									<input type="radio" class="radio_addon" name="addedToCart" id="addnewitem" value="addnewitem">
									<span><?php echo $this->lang->line('as_new_item') ?></span>
								</label>
							</div>
							<div class="radio-btn-list">
								<label>
									<input type="radio" class="radio_addon" name="addedToCart" id="increaseitem" value="increaseitem">
									<span><?php echo $this->lang->line('increase_quantity') ?></span>
								</label>
							</div>
						</div>
					</div>
					<div class="popup-total-main">
						<div class="total-price">
							<button type="button" class="addtocart btn" id="addtocart" onclick="ConfirmCartAdd()"><?php echo $this->lang->line('add_to_cart') ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-main" id="reviewModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('review_ratings') ?></h4>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<form id="review_form" name="review_form" method="post" class="form-horizontal float-form">
					<div class="review-img">
						<div class="user-images">
							<img src="<?php echo base_url(); ?>assets/front/images/review.png">
						</div>
					</div>
					<div class="rating">
						<input type="hidden" name="review_user_id" id="review_user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
						<input type="hidden" name="review_restaurant_id" id="review_restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>">

						<span><input type="radio" name="rating" id="str5" value="5"><label for="str5"></label></span>
						<span><input type="radio" name="rating" id="str4" value="4"><label for="str4"></label></span>
						<span class="checked"><input type="radio" name="rating" id="str3" value="3"><label for="str3"></label></span>
						<span><input type="radio" name="rating" id="str2" value="2"><label for="str2"></label></span>
						<span><input type="radio" name="rating" id="str1" value="1"><label for="str1"></label></span>
					</div>
					<div>
						<input type="text" name="review_text" id="review_text" class="form-control" placeholder="<?php echo $this->lang->line('write_review') ?>">
					</div>
					<div>
						<button type="submit" name="submit_review" id="submit_review" class="btn btn-primary"><?php echo $this->lang->line('add_review') ?></button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-main" id="anotherRestModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('add_to_cart') ?> ?</h4>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<form id="custom_cart_restaurant_form">
					<h5><?php echo $this->lang->line('res_details_text1') ?> <br><?php echo $this->lang->line('res_details_text2') ?></h5>
					<div class="popup-radio-btn-main">
						<div class="radio-btn-box">
							<div class="radio-btn-list">
								<label>
									<input type="hidden" name="rest_entity_id" id="rest_entity_id" value="">
									<input type="hidden" name="rest_restaurant_id" id="rest_restaurant_id" value="">
									<input type="hidden" name="is_addon" id="rest_is_addon" value="">
									<input type="hidden" name="item_id" id="item_id" value="">
									<input type="radio" class="radio_addon" name="addNewRestaurant" id="discardOld" value="discardOld">
									<span><?php echo $this->lang->line('discard_old') ?></span>
								</label>
							</div>
							<div class="radio-btn-list">
								<label>
									<input type="radio" class="radio_addon" name="addNewRestaurant" id="keepOld" value="keepOld">
									<span><?php echo $this->lang->line('keep_old') ?></span>
								</label>
							</div>
						</div>
					</div>
					<div class="popup-total-main">
						<div class="total-price">
							<button type="button" class="cartrestaurant btn" id="cartrestaurant" onclick="ConfirmCartRestaurant()"><?php echo $this->lang->line('confirm') ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- The Modal -->
<div class="modal modal-main" id="myModal"></div>
<?php if (!empty($restaurant_details['categories'])) { ?>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/tab-slider.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/front/js/scripts/admin-management-front.js"></script>
<script src="<?php echo base_url(); ?>assets/front/js/bootstrap-tagsinput.js"></script>

<!-- for review/rating and menu -->
<script type="text/javascript">
	$(function() {
		// Check Radio-box
		$(".rating input:radio").filter('[value=3]').prop('checked', true);
		$('.rating input').click(function() {
			$(".rating span").removeClass('checked');
			$(this).parent().addClass('checked');
		});

		$('input:radio').change(
			function() {
				var userRating = this.value;
			});

		$('#menu_link').click(function(e) {
			$("#menu").delay(100).fadeIn(100);
			$("#review").fadeOut(100);
			$('#review_link').removeClass('active');
			$(this).addClass('active');
			e.preventDefault();
		});
		$('#review_link').click(function(e) {
			$("#review").delay(100).fadeIn(100);
			$("#menu").fadeOut(100);
			$('#menu_link').removeClass('active');
			$(this).addClass('active');
			e.preventDefault();
		});

	});
</script>


<script type="text/javascript">
	$(document).on('ready', function() {

		var count = '<?php echo count($cart_details['cart_items']); ?>';
		$('#cart_count').html(count);

		$(window).keydown(function(event) {
			if (event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
	});
</script>
<?php $this->load->view('footer'); ?>
