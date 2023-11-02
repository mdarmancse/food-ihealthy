<?php //echo '<pre>'; print_r($userNotifications); exit;
if (!empty($userNotifications)) { ?>
	<a href="#" class="notification-btn"><i class="iicon-icon-01"></i><span class="notification_count"><?php echo $notification_count; ?></span></a>
	<div class="noti-popup">
		<div class="noti-title">
			<h5><?php echo $this->lang->line('notification') ?></h5>
			<div class="bell-icon">
				<i class="iicon-icon-01"></i>
				<span class="notification_count"><?php echo $notification_count; ?></span>
			</div>
		</div>
		<div class="noti-list">
			<?php if (!empty($userNotifications)) {
			    foreach ($userNotifications as $key => $value) {
			        if (date("Y-m-d", strtotime($value['datetime'])) == date("Y-m-d")) {
			            $noti_time = date("H:i:s") - date("H:i:s", strtotime($value['datetime']));
			            $noti_time = abs($noti_time) . ' '.$this->lang->line('mins_ago');
			        } else {
			            $d1 = strtotime(date("Y-m-d",strtotime($value['datetime'])));
						$d2 = strtotime(date("Y-m-d"));

						$noti_time = ($d2 - $d1)/86400;
						$noti_time = ($noti_time > 1 )?$noti_time.' '.$this->lang->line('days_ago'):$noti_time.' '.$this->lang->line('day_ago');
			        }
			        ?>
					<div class="noti-list-box">
						<?php $view_class = ($value['view_status'] == 0)?'unread':'read'; ?>
						<div class="noti-list-text <?php echo $view_class; ?>">
							<h6><?php echo $this->session->userdata('userFirstname') . ' ' . $this->session->userdata('userLastname'); ?></h6>
							<span class="min"><?php echo $noti_time; ?></span>
							<h6><?php echo ($value['notification_type'] == "order")?$this->lang->line('orderid'):$this->lang->line('eventid'); ?>: #<?php echo $value['entity_id']; ?></h6>
							<p><?php echo ($value['notification_slug'] == "event_cancelled")?$this->lang->line('event_cancelled_noti'):$this->lang->line($value['notification_slug']); ?></p>
						</div>
					</div>
				<?php }
			} ?>
		</div>
	</div>
<?php }
else { ?>
	<a href="#" class="notification-btn"><i class="iicon-icon-01"></i><span>0</span></a>
	<div class="noti-popup">
		<div class="noti-title">
			<h5><?php echo $this->lang->line('notification') ?></h5>
			<div class="bell-icon">
				<i class="iicon-icon-01"></i>
				<span>0</span>
			</div>
		</div>
		<div class="viewall-btn">
			<a href="javascript:void(0)" class="btn"><?php echo $this->lang->line('no_notifications') ?></a>
		</div>
	</div>
<?php } ?> 

<script type="text/javascript">
	$(".notification-btn").on("click", function(e){
		$(".noti-popup").toggleClass("open");
		e.stopPropagation();
		// unread the notifications
		jQuery.ajax({
            type : "POST",
			dataType : "html",
            url : '<?php echo base_url() . 'home/unreadNotifications' ?>',
            success: function(response) {
				//$('.notification_count').html(0);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
	});
</script>
