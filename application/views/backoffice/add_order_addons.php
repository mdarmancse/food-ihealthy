<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<!-- Modal Header -->
		<div class="modal-header">
			<h4 class="modal-title"> Select Your Choice</h4>
			<button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
		</div>
		<!-- Modal body -->
		<div class="modal-body">
			<form id="custom_items_form">
				<div class="popup-radio-btn-main">
					<!-- <input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $result[0]['items'][0]['restaurant_id']; ?>">
					<input type="hidden" name="menu_id" id="menu_id" value="<?php echo $menuId; ?>"> -->
					<div class="item-price-label">
						<span><?php echo $this->lang->line('item') ?></span>
						<span><?php echo $this->lang->line('price') ?></span>
					</div>
					<?php
					foreach ($result as $key => $value) { ?>

						<div class="radio-btn-box">
							<div class="customizable-title">
								<h5><?php echo $key; ?></h5>
							</div>
							<?php
							foreach ($value as $keys => $addvalue) { ?>
								<div class="radio-btn-list">
									<label>
										<?php if ($addvalue->is_multiple == 1) { ?>
											<input type="checkbox" class="check_addons" name="<?php echo $key . '-' . $keys; ?>" id="<?php echo $addvalue->add_ons_name . '-' . $keys; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addvalue->add_ons_price; ?>,<?php echo $addvalue->is_multiple; ?>)" amount="<?php echo $addvalue->add_ons_price; ?>" add_ons_id="<?php echo $addvalue->add_ons_id; ?>" menu_id="<?php echo $addvalue->menu_id; ?>">
										<?php } else { ?>
											<input type="radio" class="radio_addons" name="<?php echo $key; ?>" id="<?php echo $addvalue->add_ons_name . '-' . $keys; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addvalue->add_ons_price; ?>,<?php echo $addvalue->is_multiple; ?>)" amount="<?php echo $addvalue->add_ons_price; ?>" add_ons_id="<?php echo $addvalue->add_ons_id; ?>" menu_id="<?php echo $addvalue->menu_id; ?>">
										<?php } ?>
										<span><?php echo $addvalue->add_ons_name; ?></span>
									</label>
									<span style="float: right;"><?php echo $addvalue->add_ons_price; ?> </span>
								</div>
							<?php }
							?>
						</div>
					<?php }
					?>
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
						<button type="button" class="addtocart btn addtocart" id="addtocart" onclick="addOnsItems()"><?php echo $this->lang->line('add') ?></button>
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


	function getItemPrice(id, price, is_multiple) {
		radiototalPrice = 0;
		checktotalPrice = 0;
		if (is_multiple != 1) {
			//$("#custom_items_form input[type=radio]:checked").each(function() { 
			$("input:radio.radio_addons:checked").each(function() {
				var sThisVal = (this.checked ? $(this).attr("amount") : 0);
				radiototalPrice = parseFloat(radiototalPrice) + parseFloat(sThisVal);
			});
		} else {
			$('.check_addons:checkbox:checked').each(function() {
				var sThisVal = (this.checked ? $(this).attr("amount") : 0);
				checktotalPrice = parseFloat(checktotalPrice) + parseFloat(sThisVal);
			});
		}
		totalPrice = radiototalPrice + checktotalPrice;
		$('#totalPrice').html(totalPrice);
		$('#subTotal').val(totalPrice);
	}


	function addOnsItems() {

		var subTotal = $('#subTotal').val();
		var valueArray = [];
		$('.check_addons:checkbox:checked').each(function() {
			var addons_id = $(this).attr("add_ons_id");
			var menu_id = $(this).attr("menu_id");
			console.log(addons_id);
			valueArray.push({
				"add_ons_id": addons_id,
				'menu_id': menu_id
			});
		});

		$("#custom_items_form input[type=radio][class='radio_addons']:checked").each(function() {
			var addons_id = $(this).attr("add_ons_id");
			var menu_id = $(this).attr("menu_id");
			console.log(addons_id);
			valueArray.push({
				"add_ons_id": addons_id,
				'menu_id': menu_id
			});
		});
		console.log("array " + valueArray);
		// send addons array to cart
		if (valueArray.length > 0) {
			jQuery.ajax({
				type: "POST",
				url: BASEURL + 'backoffice/order/addOns',
				data: {
					'add_ons_array': valueArray,
					'subTotal': subTotal
				},
				beforeSend: function() {
					$('#quotes-main-loader').show();
				},
				success: function(response) {
					$('#quotes-main-loader').hide();
					$('#addOnsdetails').modal('hide');
					$('#item_subtotal').val(subTotal);
					
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		}

	}
</script>

<style>
	.item-price-label {
		display: -webkit-box;
		display: -moz-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		justify-content: space-between;
		margin-bottom: 20px;
	}



	.item-price-label>span {
		text-transform: uppercase;
		font-size: 18px;
		font-weight: 700;
		margin-top: -18px;
	}

	.item-price-label>span {
		text-transform: uppercase;
		font-size: 18px;
	}

	.item-price-label>span {
		font-size: 16px;
	}

	.popup-total-main .popup-total h2 {
		font-size: 20px;
		font-weight: 700;
		color: #161212;
		margin: 0px;
	}

	.popup-total-main {
		display: -webkit-box;
		display: -moz-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		align-items: center;
		background: #fff;
		padding: 15px 15px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
		border-radius: 5px;
		margin-bottom: 17px;
	}

	.popup-total-main .total-price {
		display: -webkit-box;
		display: -moz-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		align-items: center;
		margin: 0 0 0 auto;
	}

	.popup-total-main .total-price strong {
		color: #ffb300;
		color: var(--main-color);
		font-size: 25px;
		font-weight: 700;
		margin-right: 20px;
	}

	.popup-total-main .addtocart.btn {
		padding: 0.2rem 2.0rem;
		font-size: 16px;
	}

	.popup-total-main .popup-total h2 {
		font-size: 20px;
	}

	.popup-total-main .total-price strong {
		font-size: 25px;
	}

	.popup-total-main {
		padding: 7px;
	}

	.popup-total-main .addtocart.btn {
		padding: 1px 10px;
		font-size: 14px;
	}

	.popup-total-main .total-price strong {
		font-size: 20px;
	}
</style>