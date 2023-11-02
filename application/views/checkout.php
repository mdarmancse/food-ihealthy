<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('header'); ?>

<section class="inner-pages-section cart-section">
	<div class="container" id="ajax_checkout">
		<div class="row">
			<div class="col-lg-12">
				<div class="heading-title">
					<h2><?php echo $this->lang->line('checkout') ?></h2>
				</div>
			</div>
		</div>
		<div class="row cart-row">
			<div class="col-lg-8">
				<div class="checkout-account">
					<div class="account-title">
						<img src="<?php echo base_url(); ?>assets/front/images/boy.svg">
						<h3><?php echo $this->lang->line('account') ?></h3>
					</div>
					<?php if ($this->session->userdata('is_user_login') != 1) { ?>
						<div class="account-tag-line">
							<p><?php echo $this->lang->line('acc_tag_line') ?></p>
						</div>
						<div id="login_form">
							<form action="<?php echo base_url() . 'checkout'; ?>" id="form_front_login_checkout" name="form_front_login_checkout" method="post" class="form-horizontal float-form">
								<div class="form-body">
									<?php if (!empty($this->session->flashdata('error_MSG'))) { ?>
										<div class="alert alert-danger">
											<?php echo $this->session->flashdata('error_MSG'); ?>
										</div>
									<?php } ?>
									<?php if (!empty($loginError)) { ?>
										<div class="alert alert-danger">
											<?php echo $loginError; ?>
										</div>
									<?php } ?>
									<?php if (validation_errors()) { ?>
										<div class="alert alert-danger login-validations">
											<?php echo validation_errors(); ?>
										</div>
									<?php } ?>
									<div class="login-details">
										<div class="form-group">
											<input type="number" name="login_phone_number" id="login_phone_number" class="form-control" placeholder=" ">
											<label><?php echo $this->lang->line('phone_number') ?></label>
										</div>
										<div class="form-group mb-0">
											<input type="password" name="login_password" id="login_password" class="form-control" placeholder=" ">
											<label><?php echo $this->lang->line('password') ?></label>
										</div>
									</div>
									<div class="action-button account-btn">
										<button type="submit" name="submit_login_page" id="submit_login_page" value="Login" class="btn btn-primary"><?php echo $this->lang->line('title_login') ?></button>
										<a href="<?php echo base_url() . 'home/registration'; ?>" class="btn btn-secondary"><?php echo $this->lang->line('sign_up') ?></a>
									</div>
								</div>
							</form>
						</div>
					<?php } else { ?>
						<div class="login-complete">
							<div class="login-img-main">
								<div class="user-img">
									<img src="<?php echo default_user_img; ?>">
								</div>
							</div>
							<div class="logged-in">
								<strong><?php echo $this->lang->line('logged_in') ?></strong>
								<p><?php echo $this->session->userdata('userFirstname') . ' ' . $this->session->userdata('userLastname'); ?></p>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="account-accordion">
					<div class="accordion" id="accordionExampleOne">
						<div class="card" id="ajax_your_items">
							<div class="card-header" id="headingOne">
								<div class="card-header-title" data-toggle="collapse" data-target="#collapseOne">
									<img src="<?php echo base_url(); ?>assets/front/images/picnic-basket.svg">
									<h3><?php echo $this->lang->line('your_items') ?></h3>
								</div>
							</div>
							<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExampleOne">
								<div class="card-body">
									<div class="cart-content-table">
										<table>
											<tbody>
												<?php if (!empty($cart_details['cart_items'])) {
													foreach ($cart_details['cart_items'] as $cart_key => $value) { ?>
														<tr>
															<td class="item-img-main">
																<div><i class="iicon-icon-15 <?php echo ($value['is_veg'] == 1) ? 'veg' : 'non-veg'; ?>"></i></div>
															</td>
															<td class="item-name">
																<?php echo $value['name']; ?>
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
															</td>
															<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $value['totalPrice']; ?></strong></td>
															<td>
																<div class="add-cart-item">
																	<div class="number">
																		<input type="hidden" name="total_cart_items" id="total_cart_items" value="<?php echo count($cart_details['cart_items']); ?>">
																		<span class="minus" id="minusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'minus',<?php echo $cart_key; ?>)"><i class="iicon-icon-22"></i></span>
																		<input type="text" name="item_count_check" id="item_count_check" value="<?php echo $value['quantity']; ?>" class="pointer-none" />
																		<span class="plus" id="plusQuantity" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'plus',<?php echo $cart_key; ?>)"><i class="iicon-icon-21"></i></span>
																	</div>
																</div>
															</td>
															<td class="close-btn-cart"><button class="close-btn" onclick="customCheckoutItemCount(<?php echo $value['menu_id']; ?>,<?php echo $value['restaurant_id']; ?>,'remove',<?php echo $cart_key; ?>)"><i class="iicon-icon-38"></i></button></td>
														</tr>
													<?php }
												} else { ?>
													<div class="cart-empty text-center">
														<img src="<?php echo base_url(); ?>assets/front/images/empty-cart.png">
														<h6><?php echo $this->lang->line("cart_empty"); ?><br> <?php $this->lang->line("add_some_dishes"); ?></h6>
													</div>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="order_mode_method">
						<form id="checkout_form" name="checkout_form" method="post" class="form-horizontal float-form">
							<?php if ($this->session->userdata('is_user_login') == 1 && !empty($cart_details['cart_items'])) { ?>
								<div class="accordion" id="accordionExampleTwo">
									<div class="card" id="order_mode_content">
										<div class="card-header" id="headingTwo">
											<div class="card-header-title" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true">
												<img src="<?php echo base_url(); ?>assets/front/images/order-mode.svg">
												<h3><?php echo $this->lang->line('order_mode') ?></h3>
											</div>
										</div>
										<div id="collapseTwo" class="collapse in show" aria-labelledby="headingTwo" data-parent="#accordionExampleTwo">
											<div class="card-body">
												<div class="choose-order-mode">
													<div class="choose-order-title">
														<h6><?php echo $this->lang->line('choose_order_mode') ?></h6>
													</div>
													<div class="order-mode">
														<div class="card">
															<div class="radio-btn-list">
																<!--<label>-->
																<input type="hidden" name="subtotal" id="subtotal" value="<?php echo $cart_details['cart_total_price']; ?>">

																<!--	<input type="radio" name="choose_order" id="pickup" value="pickup" onclick="showPickup(<?php echo $cart_details['cart_total_price']; ?>);">-->
																<!--	<span><?php echo $this->lang->line('pickup') ?></span>-->
																<!--</label>-->
																<!-- </div>
											                    	<div class="radio-btn-list"> -->
																<label>
																	<input type="radio" name="choose_order" id="delivery" value="delivery" onclick="showDelivery(<?php echo $cart_details['cart_total_price']; ?>);">
																	<span><?php echo $this->lang->line('delivery') ?></span>
																</label>
															</div>
															<div class="delivery-form display-no" id="delivery-form">
																<div class="current-location">
																	<p><img src="<?php echo base_url(); ?>assets/front/images/current-location.svg"> <?php echo $this->lang->line('choose_delivery_address') ?></p>
																</div>
																<div class="radio-btn-list">
																	<label>
																		<input type="radio" name="add_new_address" value="add_new_address" class="add_new_address" onclick="showAddAdress();">
																		<span><?php echo $this->lang->line('add_address') ?></span>
																	</label>
																</div>
																<div id="add_address_content" class="display-no">
																	<h5><?php echo $this->lang->line('add_address') ?></h5>
																	<div class="login-details">
																		<div class="form-group">
																			<input type="hidden" name="add_latitude" id="add_latitude">
																			<input type="hidden" name="add_longitude" id="add_longitude">
																			<input type="text" name="add_address_area" id="add_address_area" onFocus="geolocate('')" placeholder=" " onchange="getLatLong('<?php echo $cart_details['cart_total_price']; ?>')" class="form-control">
																			<label><?php echo $this->lang->line('delivery_area') ?></label>
																		</div>
																		<div class="form-group">
																			<input type="text" name="add_address" id="add_address" class="form-control" placeholder=" ">
																			<label><?php echo $this->lang->line('your_location') ?></label>
																		</div>
																		<div class="form-group">
																			<input type="text" name="landmark" id="landmark" class="form-control" placeholder=" ">
																			<label><?php echo $this->lang->line('landmark') ?></label>
																		</div>
																		<div class="form-group">
																			<input type="text" name="zipcode" id="zipcode" class="form-control" placeholder=" ">
																			<label><?php echo $this->lang->line('zipcode') ?></label>
																		</div>
																		<div class="form-group">
																			<input type="text" name="city" id="city" class="form-control" placeholder=" ">
																			<label><?php echo $this->lang->line('city') ?></label>
																		</div>
																	</div>
																</div>
																<?php $address = $this->checkout_model->getUsersAddress($this->session->userdata('UserID'));
																if (!empty($address)) { ?>
																	<div class="radio-btn-list">
																		<label>
																			<input type="radio" name="add_new_address" value="add_your_address" class="add_new_address" onclick="showYourAdress();">
																			<span><?php echo $this->lang->line('choose_your_address') ?></span>
																		</label>
																	</div>
																	<div id="your_address_content" class="display-no">
																		<h5><?php echo $this->lang->line('choose_your_address') ?></h5>
																		<div class="login-details">
																			<div class="form-group">
																				<select class="form-control" name="your_address" id="your_address" onchange="getAddLatLong(this.value,<?php echo $cart_details['cart_total_price']; ?>)">
																					<option value=""><?php echo $this->lang->line('select') ?></option>
																					<?php foreach ($address as $key => $value) { ?>
																						<option value="<?php echo $value['entity_id']; ?>"><?php echo $value['address'] . ',' . $value['landmark'] . ',' . $value['zipcode'] . ',' . $value['city']; ?></option>
																					<?php } ?>
																				</select>
																				<label><?php echo $this->lang->line('your_address') ?></label>
																			</div>
																		</div>
																	</div>
																<?php } ?>
															</div>
														</div>
														<div class="card card2">
															<div>
																<div class="current-location">
																	<h5><?php echo $this->lang->line('apply_coupon') ?></h5>
																	<p id="your_coupons"><?php echo $this->lang->line('no_coupons_available') ?></p>
																</div>

															</div>
														</div>
														<div class="card">
															<div>
																<div class="current-location">
																	<h5><?php echo $this->lang->line('extra_comment') ?></h5>
																</div>
																<div>
																	<div class="form-group">
																		<input type="text" name="extra_comment" id="extra_comment" class="form-control" placeholder=" ">
																		<label><?php echo $this->lang->line('extra_comment') ?></label>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="accordion" id="accordionExampleThree">
									<div class="card">
										<div class="card-header" id="headingThree">
											<div class="card-header-title" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true">
												<img src="<?php echo base_url(); ?>assets/front/images/payment.png">
												<h3><?php echo $this->lang->line('payment_method') ?></h3>
											</div>
										</div>
										<div id="collapseThree" class="collapse in show" aria-labelledby="headingThree" data-parent="#accordionExampleThree">
											<div class="card-body">
												<div class="payment-mode">
													<div class="payment-title">
														<h6><?php echo $this->lang->line('choose_payment_method') ?></h6>
													</div>
													<div class="order-mode">
														<div class="card">
															<div class="radio-btn-list">
																<label>
																	<input type="radio" name="payment_option" id="payment_option1" value="cod" onclick="payment_methods(this.value)" required checked />
																	<span><?php echo $this->lang->line('cod') ?></span>
																</label>
																<label>
																	<input type="radio" name="payment_option" id="payment_option1" value="bkash" onclick="payment_methods(this.value)" required />
																	<span><?php echo "Bkash" ?></span>
																</label>
																<!-- <label>
																	<input type="radio" name="payment_option" id="payment_option1" value="nagad" onclick="payment_methods(this.value)" required />
																	<span><?php echo "Nagad" ?></span>
																</label>

																<label>
																	<input type="radio" name="payment_option" id="payment_option1" value="online" onclick="payment_methods(this.value)" required />
																	<span><?php echo "More Options" ?></span>
																</label> -->
															</div>
														</div>
													</div>
													<input type="hidden" name="transaction_id" id="transaction_id" value="">
													<input type="hidden" name="paymentID" id="paymentID" value="">
													<input type="hidden" name="payment_method" id="payment_method" value="">
												</div>

												<div class="bkash_agreement_list">
													<div class="radio-btn-list">

													</div>

												</div>
												<div class="proceed-btn">
													<button type="submit" name="submit_order" id="submit_order" value="Proceed" class="btn btn-primary" disabled><?php echo $this->lang->line('proceed') ?></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</form>
					</div>
				</div>
			</div>
			<?php if (!empty($cart_details['cart_items'])) { ?>
				<div class="col-lg-4" id="ajax_order_summary">
					<div class="order-summary">
						<div class="order-summary-title">
							<h3><i class="iicon-icon-02"></i><?php echo $this->lang->line('order_summary') ?></h3>
						</div>
						<div class="order-summary-content">
							<table>
								<tbody>
									<tr>
										<td><?php echo $this->lang->line('no_of_items') ?></td>
										<td><strong><?php echo count($cart_details['cart_items']); ?></strong></td>
									</tr>
									<tr>
										<td><?php echo $this->lang->line('sub_total') ?></td>
										<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $cart_details['cart_total_price']; ?></strong></td>
									</tr>
									<?php /* ?><tr>
												<td><?php echo $this->lang->line('delivery_charges') ?></td>
												<td><span id="delivery_charges"><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo ($this->session->userdata('deliveryCharge'))?$this->session->userdata('deliveryCharge'):0; ?></strong></span></td>
											</tr><?php */ ?>
								</tbody>
								<tfoot>
									<tr>
										<td><?php echo $this->lang->line('to_pay') ?></td>
										<?php $to_pay = $cart_details['cart_total_price'] + $delivery_charges;
										$this->session->set_userdata(array('total_price' => $to_pay)); ?>
										<td><strong><?php echo $currency_symbol->currency_symbol; ?> <?php echo $to_pay; ?></strong></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
<!--/ end content-area section -->

<!-- Order Confirmation -->
<div class="modal modal-main" id="order-confirmation">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('order_confirmation') ?></h4>
				<button type="button" class="close" data-dismiss="modal" onclick="document.location.href='<?php echo base_url(); ?>restaurant';"><i class="iicon-icon-23"></i></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<div class="availability-popup">
					<div class="availability-images">
						<img src="<?php echo base_url(); ?>assets/front/images/order-confirmation.svg" alt="Booking availability">
					</div>
					<h2><?php echo $this->lang->line('thankyou_for_order') ?></h2>
					<p><?php echo $this->lang->line('order_placed') ?></p>
					<span id="track_order"><a href="<?php echo base_url(); ?>myprofile" class="btn"><?php echo $this->lang->line("track_order"); ?></a></span>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- order delivery not available -->
<div class="modal modal-main" id="delivery-not-avaliable">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('delivery_not_available') ?></h4>
				<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<div class="availability-popup">
					<div class="availability-images">
						<img src="<?php echo base_url(); ?>assets/front/images/no-delivery.png" alt="Booking availability">
					</div>
					<h2><?php echo $this->lang->line('avail_text1') ?></h2>
					<p><?php echo $this->lang->line('avail_text2') ?></p>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<?php if ($this->session->userdata('is_user_login') == 1) { ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= MAP_API_KEY ?>&libraries=places"></script>
<?php } ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/front/js/scripts/admin-management-front.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js" defer></script>

<script type="text/javascript">
	let selected_bkash = "";
	let payMethod = "";
	let deliveryMethodSelected = false;
	$(document).ready(function() {
		jQuery("#payment_option").prop('required', true);
		$('#signup_form').hide();
		var page = '<?php echo $page; ?>';
		if (page == "login") {
			$('#login_form').show();
			$('#signup_form').hide();
		}
		if (page == "register") {
			$('#login_form').hide();
			$('#signup_form').show();
		}
		$(window).keydown(function(event) {
			if (event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
	});

	function payment_methods(method) {
		if (method == "cod") {
			$("#payment_method").val("COD");
			$(".bkash_agreement_list").hide();
			payMethod = "cod";
		} else if (method == "bkash") {
			$("#payment_method").val("bKash payment");
			$(".bkash_agreement_list").show();
			payMethod = "bkash";
			if (payMethod == "bkash" && selected_bkash && deliveryMethodSelected) {
				$("#pay_with_bkash").removeAttr('disabled');
				$("#submit_order").removeAttr('disabled');
			} else {
				$("#pay_with_bkash").attr('disabled', 'disabled');
				$("#submit_order").attr('disabled', 'disabled');
			}
			bkashShowAgreementList()
		} else if (method == "nagad") {
			$("#payment_method").val("Nagad Payment");
			$(".bkash_agreement_list").hide();
			payMethod = "nagad";
			initNagad()
		} else if (method == "online") {
			$("#payment_method").val("Online Payment");
			$(".bkash_agreement_list").hide();
			payMethod = "online";
			if (payMethod == "online" && deliveryMethodSelected) {
				$("#submit_order").removeAttr('disabled');
			} else {
				$("#submit_order").attr('disabled', 'disabled');
			}
			initSSL()
		}
	}

	function setBkash(paymentID) {
		selected_bkash = paymentID;

		if (payMethod == "bkash" && selected_bkash && deliveryMethodSelected) {
			$("#pay_with_bkash").removeAttr('disabled');
			$("#submit_order").removeAttr('disabled');
		} else {
			$("#pay_with_bkash").attr('disabled', 'disabled');
			$("#submit_order").attr('disabled', 'disabled');
		}
	}

	function addBkash() {
		const user_id = "<?= $this->session->UserID ?>";
		$.ajax({
			type: "POST",
			url: BASEURL + "v1/Bkash_web/createAgreement",
			data: JSON.stringify({
				user_Id: user_id
			}),
			success: function(response) {
				if (response.errorCode) {
					toastr.error("Something went wrong");
				} else {
					openWindow(response.url, user_id, "agreement");
				}
			}
		})
	}

	function bkashDeleteAgreement(agreementID) {
		$.ajax({
			type: "POST",
			url: BASEURL + "v1/Bkash_web/CancelAgreement",
			data: {
				agreementID: agreementID
			},
			success: function(response) {
				if (response.status == 1) {
					toastr.success("Successfully deleted.")
				} else {
					toastr.error("Something went wrong");
				}

				bkashShowAgreementList();
			}
		})
	}


	function bkashShowAgreementList() {

		const user_id = "<?= $this->session->UserID ?>";
		$.ajax({
			type: "POST",
			url: BASEURL + "v1/api/getPaymentOption",
			data: {
				user_id: user_id
			},
			success: function(response) {
				let html = "";
				html += '<button type="button" name="add_agreement_bkash" id="add_agreement_bkash" value="bkash_add" class="btn btn-secondary" style="margin-bottom: 10px" onclick="addBkash()">+ Add Bkash</button>';
				response.data.map((k, i) => {
					html += "<label class='agreement_list_item'>";
					html += "<div>";
					html += '<input type="radio" name="bk_agreement" id="bk_agreement1" class="bk_agreement" value="' + k.paymentID + '" onclick="setBkash(\'' + k.paymentID + '\')"/>';
					html += '<span>' + k.name + '</span>';
					html += "</div>";
					html += "<div class='close-btn-cart'>";
					html += "<button class='delete-btn' type='button' onclick='bkashDeleteAgreement(\"" + k.paymentID + "\")'><i class='iicon-icon-38'></i></button>";
					html += "</div>";
					html += "</label>";
				})

				html += '<button type="button" name="pay_with_bkash" id="pay_with_bkash" value="bkash_add" class="btn btn-bkash width-full" onclick="bkashInit()" disabled>Pay With Bkash</button>';


				$(".bkash_agreement_list > .radio-btn-list").html(html)
			}
		})
	}


	function bkashInit() {
		const user_id = "<?= $this->session->UserID ?>";
		const total_amount = $("#to_pay").val();

		$.ajax({
			type: "POST",
			url: BASEURL + "v1/Bkash_web/grant",
			data: JSON.stringify({
				user_Id: user_id,
				total_amount: total_amount,
				agreementID: selected_bkash
			}),
			success: function(response) {
				openWindow(response, user_id);
			}
		})
	}

	function initNagad() {
		const total_amount = "<?php echo $cart_details['cart_total_price'] ?>";

		$.ajax({
			type: "POST",
			url: BASEURL + "v1/Nagad_web/init",
			data: JSON.stringify({
				total_amount: total_amount,
			}),
			success: function(response) {
				openWindow(response, "", "");
			}
		})
	}

	function initSSL() {
		const total_amount = "<?php echo $cart_details['cart_total_price'] ?>";
		const user_num = "<?= $this->session->userPhone ?>"
		$.ajax({
			type: "POST",
			url: BASEURL + "Ssl_web/sslEndPoint",
			data: JSON.stringify({
				total_amount: total_amount,
				tran_id: random_tran_id,
				cus_phone: user_num,
			}),
			success: function(response) {
				openWindow(response, "", "");
			}
		})
	}

	const random_tran_id = () => {
		var length = 16;
		var characters =
			"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		var charactersLength = characters.length;
		var result = "";
		for (var i = 0; i < 16; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	};


	function openWindow(data, user_id, agreement) {
		let a = window.open(
			data,
			"child window",
			"height=800px, width=500px, top=" + ((screen.height - 800) / 2) + ", left=" + ((screen.width - 500) / 2)
		);

		const messageListener = (e) => {
			window.removeEventListener("message", messageListener, false);

			data = JSON.parse(e.data.replace(/\/$/, ""));
			console.log(data)
			if (data.errorCode) {
				toastr.error("Error: " + data.message ? data.message : "");
			} else {
				if (payMethod == "bkash") {
					if (agreement == "agreement") {
						$.ajax({
							type: "POST",
							url: BASEURL + "v1/Bkash_web/saveagreement",
							data: {
								user_id: user_id,
								msidn: data.customerMsisdn,
								agreementid: data.agreement_id,
							},
							success: function(response) {
								toastr.success("New bkash number added.")
								bkashShowAgreementList();
							}
						})
					} else {
						$("#transaction_id").val(data.invoice);
						$("#paymentID").val(data.trxID);
						$("#checkout_form").submit();
					}
				}

				if (payMethod == "nagad" || payMethod == "online") {
					$("#transaction_id").val(data.invoice);
					$("#checkout_form").submit();
				}
			}
		};

		window.addEventListener("message", messageListener, false);
	}
</script>
<?php $this->load->view('footer'); ?>