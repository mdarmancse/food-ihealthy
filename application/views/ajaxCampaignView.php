<?php if (!empty($campaign)) { ?>
    <div class="container">
        <div class="heading-title">
            <h2><?php echo "Campaign"; ?></h2>
            <div class="slider-arrow">
                <div id="campaign_customNav" class="arrow"></div>
            </div>
        </div>
        <div class="campaign-slider owl-carousel">
            <?php foreach ($campaign as $key => $value) { ?>
                <div class="quick-searches-box" onclick="getCampaignDetails(<?= $value->entity_id ?>)">
                    <img src="<?php echo ($value->image) ? $value->image : default_img; ?>" alt="image">
                    <h5><?php echo $value->name ?></h5>
                </div>
            <?php } ?>
        </div>
    </div>

    <script type="text/javascript">
        $(".campaign-slider").owlCarousel({
            loop: false,
            rewind: true,
            margin: 20,
            nav: true,
            autoplay: true,
            autoplayTimeout: 3500,
            navSpeed: 1300,
            touchDrag: true,
            slideBy: 2,
            autoplaySpeed: 1300,
            autoplayHoverPause: true,
            navContainer: "#campaign_customNav",
            responsive: {
                0: {
                    items: 2,
                    margin: 15,
                },
                600: {
                    items: 3,
                    margin: 20,
                },
                1000: {
                    items: 5,
                    margin: 20,
                },
                1300: {
                    items: 6,
                    margin: 20,
                },
                1550: {
                    items: 7,
                },
            },
        });
    </script>
<?php } ?>