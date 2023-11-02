<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('header'); ?>

<!--<section class="home-banner">-->
<!--	<div class="container">-->
<!--		<div class="your-doorstep">-->
<!---->
<!--			<p>--><?php //echo $this->lang->line('order_fav_rest'); ?><!--</p>-->
<!--			<br /><br /><br />-->
<!--			<form id="home_search_form" class="search-form">-->
<!--				<div class="form-group">-->
<!--					<input type="text" name="address" id="address" onFocus="geolocate('')" placeholder="--><?php //echo $this->lang->line('enter_address'); ?><!--" value="">-->
<!--					<input type="button" name="Search" value="--><?php //echo $this->lang->line('search'); ?><!--" class="btn" onclick="fillInAddress('home_page')">-->
<!--				</div>-->
<!--			</form>-->
<!--		</div>-->
<!--	</div>-->
<!--</section>-->

<section class="home-banner">


			<div id="carouselExample" class="carousel slide" data-ride="carousel">
				<ol class="carousel-indicators">
					<li data-target="#carouselExample" data-slide-to="0" class="active"></li>
					<li data-target="#carouselExample" data-slide-to="1"></li>
					<li data-target="#carouselExample" data-slide-to="2"></li>
				</ol>
				<div class="carousel-inner">
					<div class="carousel-item active">
						<img src="<?php echo base_url(); ?>assets/front/images/slider-1.jpg" class="d-block w-100" alt="Image 1">
					</div>
					<div class="carousel-item">
						<img src="<?php echo base_url(); ?>assets/front/images/slider-2.jpg" class="d-block w-100" alt="Image 2">
					</div>
					<div class="carousel-item">
						<img src="<?php echo base_url(); ?>assets/front/images/slider-3.jpg" class="d-block w-100" alt="Image 3">
					</div>
				</div>

				<a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
			</div>

		<div class="text-center mt-5"><!-- Added Bootstrap class for centering -->
			<div class="your-doorstep">
				<p><?php echo $this->lang->line('order_fav_rest'); ?></p>

				<form id="home_search_form" class="search-form">
					<div class="form-group">
						<input type="text" name="address" id="address" onFocus="geolocate('')" placeholder="<?php echo $this->lang->line('enter_address'); ?>" value="">
						<input type="button" name="Search" value="<?php echo $this->lang->line('search'); ?>" class="btn" onclick="fillInAddress('home_page')">

					</div>
				</form>

			</div>
		</div>

</section>

<div class="modal modal-main" id="myModal"></div>




<input type="hidden" id="user_login" value="<?= $this->session->userdata('is_user_login') ?>">
<!-- Modal -->
<div class="modal std-modal" tabindex="-1" role="dialog" id="campaign_modal">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="row align-items-center">
				<div class="col-12">
					<div class="modal-header">
						<h5 class="modal-title">Campaign</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span> </button>
					</div>

					<div class="modal-body">
						<div class="campaign-restaurants"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal -->

<div class="container">


	<section class="restaurant-app">
		<div class="restaurant-app-content">
			<div class="row Xrow">
				<div class="col-md-6 col-sm-12 Ximages">
					<div class="restaurant-app-text">
						<div class="heading-title-02">
							<h4><?php echo $this->lang->line('menu') ?></h4>
						</div>
						<p><?php echo $this->lang->line('home_menu_text') ?></p>
						<div class="white-button">
							<input type="button" value="<?php echo $this->lang->line('order_now'); ?>" class="btn" onclick="window.location.href='<?php echo base_url() . 'restaurant'; ?>'">

						</div>
					</div>
				</div>
				<div class="col-md-6 col-sm-12 Ximages">
					<div class="restaurant-app-img wow pulse">
						<img src="<?php echo base_url(); ?>assets/front/images/menu_banner.jpg" alt="Restaurant app">
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="restaurant-app">
		<div class="restaurant-app-content">
			<div class="row Xrow">
				<div class="col-md-6 col-sm-12 Ximages">
					<div class="restaurant-app-img wow pulse">
						<img src="<?php echo base_url(); ?>assets/front/images/about_banner.jpg" alt="About">
					</div>
				</div>
				<div class="col-md-6 col-sm-12 Ximages">
					<div class="restaurant-app-text restaurant-app-text-left">
						<div class="heading-title-02">
							<h4><?php echo $this->lang->line('about_home') ?></h4>
						</div>
						<p><?php echo $this->lang->line('home_about_text') ?></p>
						<div class="white-button">
							<input type="button" value="<?php echo $this->lang->line('read_more'); ?>" class="btn" onclick="window.location.href='<?php echo base_url() . 'about-us'; ?>'">

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


<!--	<section class="restaurant-app">-->
<!--		<div class="restaurant-app-content">-->
<!--			<div class="row">-->
<!--				<div class="col-md-6 col-sm-12">-->
<!--					<div class="restaurant-app-text">-->
<!--						<div class="heading-title-02">-->
<!--							<h4>--><?php //echo $this->lang->line('video') ?><!--</h4>-->
<!--						</div>-->
<!--						<p>--><?php //echo $this->lang->line('home_video_text') ?><!--</p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--				<div class="col-md-6 col-sm-12">-->
<!--					<div class="restaurant-app-img wow pulse">-->
<!---->
<!--						<video controls autoplay loop muted width="100%" height="100%">-->
<!--							<source src="--><?php //echo base_url(); ?><!--assets/front/video/video.mp4" type="video/mp4">-->
<!--							Your browser does not support the video tag.-->
<!--						</video>-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	</section>-->
	<section class="restaurant-app">
		<div class="restaurant-app-content">




						<video controls autoplay loop muted width="100%" height="100%">

							<source src="<?php echo base_url(); ?>assets/front/video/video.mp4" type="video/mp4">
							Your browser does not support the video tag.
						</video>

				</div>


	</section>
	<section class="restaurant-app">

		<div class="reserve-form">

			<div class="row">
				<div class="col-md-2 col-sm-2">

						<div class="box-h2">
							<h2 class="text-center"><?php echo $this->lang->line('reservation') ?></h2>
						</div>

				</div>
				<div class="col-md-10 col-sm-10">

					<div class="container">
						<form id="check_event_availability" name="check_event_availability" method="post" class="form-horizontal float-form">

						<div class="row justify-content-center">
							<div class="col-lg-6 col-md-6 col-sm-12">
									<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $restaurant_details['restaurant'][0]['restaurant_id']; ?>">
									<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->session->userdata('UserID'); ?>">

									<div class="form-group">
										<input type="text" name="branch_name" id="branch_name" class="form-control" placeholder="">
										<label for="name"><?php echo $this->lang->line('select_branch') ?></label>

									</div>

									<div class="form-group">
										<input type="email" name="email" id="email" class="form-control" placeholder="">
										<label for="email"><?php echo $this->lang->line('booking_date') ?></label>

									</div>

							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">

									<div class="form-group">
										<input type="text" name="name" id="name" class="form-control" placeholder="">
										<label for="name"><?php echo $this->lang->line('no_of_guest') ?></label>

									</div>

									<div class="form-group">
										<input type="email" name="email" id="email" class="form-control" placeholder="">
										<label for="email"><?php echo $this->lang->line('booking_time') ?></label>

									</div>

							</div>
						</div>
							<div class="continue-btn">
								<button type="submit" name="submit_page" id="submit_page" value="Check Availability" class="btn btn-success danger-btn"><?php echo $this->lang->line('check_avail') ?></button>
							</div>
						</form>
					</div>

				</div>
			</div>


		</div>
	</section>


</div>




<!-- <div class="long-bar"></div> -->



<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= MAP_API_KEY ?>&libraries=places"></script>

<script>
	$('#carouselExample').carousel();
</script>


<script>


$(document).on('ready', function() {

		initAutocomplete('address');
		// auto detect location if even searched once.
		if (SEARCHED_LAT == '' && SEARCHED_LONG == '' && SEARCHED_ADDRESS == '') {
			getLocation('home_page');
		} else {
			getSearchedLocation(SEARCHED_LAT, SEARCHED_LONG, SEARCHED_ADDRESS, 'home_page');
		}

		$(window).keydown(function(event) {
			if (event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});

	});

	function getCampaignDetails(campaign_id) {
		html = "";
		$.ajax({
			type: "POST",
			url: BASEURL + "v1/api/getCampaignRestaurants",
			data: {
				latitude: SEARCHED_LAT,
				longitude: SEARCHED_LONG,
				campaign_id: campaign_id
			},
			success: function(data) {
				$(".modal-title").html(data.restaurant[0].campaign[0].name);
				data.restaurant.map((key, index) => {
					html += '<a href="<?php echo base_url() . 'restaurant/restaurant-detail/' ?>/' + key.restaurant_slug + '">'
					html += "<div class='camp_res'>" +
						"<div class='camp_img'><img alt='image' src='" + key.image + "' /></div>" +
						"<div class='camp_details'>" +
						"<div><strong>" + key.name + "</strong></div>" +
						"<div>" + key.address + "</div>" +
						"</div>" +
						"</div>";
				})

				$(".campaign-restaurants").html(html);
				$("#campaign_modal").modal('show');
			}

		})
	};
</script>
<?php $this->load->view('footer'); ?>
