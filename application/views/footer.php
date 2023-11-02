<div class="wait-loader display-no" id="quotes-main-loader"><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<footer class="footer-area">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-12">
				<div class="row align-items-center">


				<div class="footer-logo">
					<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/front/images/footer_logo.png" alt=""></a>
				</div>
				<div class="footer-links">
					<ul>
						<?php $lang_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en' ;
						$cmsPages = $this->common_model->getCmsPages($lang_slug);

						if (!empty($cmsPages)) {
							foreach ($cmsPages as $key => $value) {
								if ($value->CMSSlug == "about-us") { ?>
									<li class="<?php echo ($current_page == 'AboutUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'about-us'; ?>"> > <?php echo $this->lang->line('about_us') ?></a></li>
								<?php }
								else if($value->CMSSlug == "contact-us") { ?>
									<li class="<?php echo ($current_page == 'ContactUs') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'contact-us'; ?>"> > <?php echo $this->lang->line('contact_us') ?></a></li>
								<?php }
								else if($value->CMSSlug == "terms") { ?>
									<li class="<?php echo ($current_page == 'TermsAndConditions') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'terms-and-conditions'; ?>"> > <?php echo $this->lang->line('terms_and_conditions')?> </a></li>
								<?php }
								else if($value->CMSSlug == "privacy-policy") { ?>
									<li class="<?php echo ($current_page == 'PrivacyPolicy') ? 'current_page_item' : ''; ?>"><a href="<?php echo base_url() . 'privacy-policy'; ?>"> > <?php echo $this->lang->line('privacy_policy')?> </a></li>
								<?php }

							}
						} ?>
					</ul>
				</div>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="contact-button" style="text-align: end">
					<a href="<?php echo base_url() . 'contact-us'; ?>" class="btn"><?php echo $this->lang->line('contact_us') ?></a>
				</div>
				<div class="social-icon">
					<ul>
						<li><a href="#"><i class="iicon-icon-08"></i></a></li>
						<li><a href="#"><i class="iicon-icon-09"></i></a></li>
						<li><a href="#"><i class="iicon-icon-10"></i></a></li>
						<li><a href="#"><span class="fa-brands fa-instagram"></span></a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-12 copyDev">
<!--				<hr>-->

				<div class="row">
					<div class="copyright col-md-6 col-sm-12">
						<p><?php echo $this->lang->line('copyright_footer'); ?> <a target="_blank" href="<?php echo base_url(); ?>"><?php echo $this->lang->line('site_footer'); ?></a></p>
					</div>
					<div class=" develop_by col-md-6 col-sm-12">
						<p>Develop by: <span ><a target="_blank" href="<?php echo base_url(); ?>"><?php echo $this->lang->line('develop_by'); ?></a></span></p>
					</div>
				</div>


			</div>
		</div>
	</div>
</footer>


<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/custom_js.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/scripts/front-validations.js"></script>
</body>
</html>
