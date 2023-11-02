<?php
// echo '<pre>';
// print_r($_SESSION);
// exit();
?>
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">
            <li class="sidebar-toggler-wrapper">
                <div class="sidebar-toggler">
                </div>
            </li>
            <li>&nbsp;</li>
            <li class="start <?php echo ($this->uri->segment(2) == 'dashboard') ? "active" : ""; ?>">
                <a href="<?php echo base_url() . ADMIN_URL; ?>/dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span class="title"><?php echo $this->lang->line('dashboard'); ?></span>
                    <span class="selected"></span>
                </a>
            </li>

            <?php
            if (
                $this->lpermission->method('zone_utilization', 'create')->access() ||
                $this->lpermission->method('zone_utilization', 'read')->access() ||
                $this->lpermission->method('zone_utilization', 'update')->access() ||
                $this->lpermission->method('zone_utilization', 'delete')->access() ||
                $this->lpermission->method('orders', 'create')->access() ||
                $this->lpermission->method('orders', 'read')->access() ||
                $this->lpermission->method('orders', 'update')->access() ||
                $this->lpermission->method('orders', 'delete')->access()
            ) { ?>

                <li class="start <?php echo ($this->uri->segment(2) == 'sub_dashboard' || $this->uri->segment(2) == 'order') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/sub_dashboard/view">
                        <i class="fa fa-dashboard"></i>
                        <span class="title"><?php echo $this->lang->line('sub_dashboard'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'sub_dashboard') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php
                        if (
                            $this->lpermission->method('zone_utilization', 'create')->access() ||
                            $this->lpermission->method('zone_utilization', 'read')->access() ||
                            $this->lpermission->method('zone_utilization', 'update')->access() ||
                            $this->lpermission->method('zone_utilization', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'sub_dashboard' && $this->uri->segment(3) == 'view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/sub_dashboard/view">
                                    <i class="fa fa-users"></i>
                                    <span class="title"><?php echo $this->lang->line('rider_dashboard'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>


                        <?php
                        if (
                            $this->lpermission->method('orders', 'create')->access() ||
                            $this->lpermission->method('orders', 'read')->access() ||
                            $this->lpermission->method('orders', 'update')->access() ||
                            $this->lpermission->method('orders', 'delete')->access()
                        ) { ?>

                            <li class="start <?php echo ($this->uri->segment(2) == 'order' && $this->uri->segment(3) != 'pending' && $this->uri->segment(3) != 'preorder' && $this->uri->segment(3) != 'delivered' && $this->uri->segment(3) != 'on-going') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/order/view">
                                    <i class="fa fa-bars"></i>
                                    <span class="title"><?php echo "Dispatch Panel"; ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                        <?php } ?>
                    </ul>
                </li>

            <?php } ?>


            <?php
            if (
                $this->lpermission->method('users', 'create')->access() ||
                $this->lpermission->method('users', 'read')->access() ||
                $this->lpermission->method('users', 'update')->access() ||
                $this->lpermission->method('users', 'delete')->access() ||
                $this->lpermission->method('riders', 'create')->access() ||
                $this->lpermission->method('riders', 'read')->access() ||
                $this->lpermission->method('riders', 'update')->access() ||
                $this->lpermission->method('riders', 'delete')->access() ||
                $this->lpermission->method('rider_list', 'create')->access() ||
                $this->lpermission->method('rider_list', 'read')->access() ||
                $this->lpermission->method('rider_list', 'update')->access() ||
                $this->lpermission->method('rider_list', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'users' || $this->uri->segment(3) == 'driver' || $this->uri->segment(3) == 'commission' || $this->uri->segment(3) == 'rider_view' || $this->uri->segment(3) == 'vehicle_view') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/users/view">
                        <i class="fa fa-users"></i>
                        <span class="title"><?php echo $this->lang->line('users'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'users' || $this->uri->segment(3) == 'driver' || $this->uri->segment(4) == 'driver') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (
                            $this->lpermission->method('users', 'create')->access() ||
                            $this->lpermission->method('users', 'read')->access() ||
                            $this->lpermission->method('users', 'update')->access() ||
                            $this->lpermission->method('users', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'users' && $this->uri->segment(3) != 'driver' && $this->uri->segment(4) != 'driver' && $this->uri->segment(3) != 'commission' && $this->uri->segment(5) != 'driver' && $this->uri->segment(3) != 'review') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/users/view">
                                    <i class="fa fa-users"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_user'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (
                            $this->lpermission->method('riders', 'create')->access() ||
                            $this->lpermission->method('riders', 'read')->access() ||
                            $this->lpermission->method('riders', 'update')->access() ||
                            $this->lpermission->method('riders', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'driver' || $this->uri->segment(4) == 'driver' ||  $this->uri->segment(3) == 'commission' || $this->uri->segment(5) == 'driver' || $this->uri->segment(3) == 'review') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/users/driver">
                                    <i class="fa fa-motorcycle" aria-hidden="true"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_driver'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php
                        if (
                            $this->lpermission->method('vehicle', 'create')->access() ||
                            $this->lpermission->method('vehicle', 'read')->access() ||
                            $this->lpermission->method('vehicle', 'update')->access() ||
                            $this->lpermission->method('vehicle', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'zone' && $this->uri->segment(3) == 'vehicle_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/vehicle_view">
                                    <i class="fa fa-bus" aria-hidden="true"></i>
                                    <span class="title">Vehicle</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('rider_list', 'create')->access() ||
                            $this->lpermission->method('rider_list', 'read')->access() ||
                            $this->lpermission->method('rider_list', 'update')->access() ||
                            $this->lpermission->method('rider_list', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'rider_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/rider_view">
                                    <i class="fa fa-motorcycle" aria-hidden="true"></i>
                                    <span class="title">Rider List</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

            <!-- Restaurant -->
            <?php
            if (
                $this->lpermission->method('restaurant', 'create')->access() ||
                $this->lpermission->method('menu', 'create')->access() ||
                $this->lpermission->method('branch', 'create')->access() ||
                $this->lpermission->method('addon_category', 'create')->access() ||
                $this->lpermission->method('popular_restaurant', 'create')->access() ||

                $this->lpermission->method('restaurant', 'read')->access() ||
                $this->lpermission->method('menu', 'read')->access() ||
                $this->lpermission->method('branch', 'read')->access() ||
                $this->lpermission->method('addon_category', 'read')->access() ||
                $this->lpermission->method('popular_restaurant', 'read')->access() ||

                $this->lpermission->method('restaurant', 'update')->access() ||
                $this->lpermission->method('menu', 'update')->access() ||
                $this->lpermission->method('branch', 'update')->access() ||
                $this->lpermission->method('addon_category', 'update')->access() ||
                $this->lpermission->method('popular_restaurant', 'update')->access() ||

                $this->lpermission->method('restaurant', 'delete')->access() ||
                $this->lpermission->method('menu', 'delete')->access() ||
                $this->lpermission->method('branch', 'delete')->access() ||
                $this->lpermission->method('addon_category', 'delete')->access() ||
                $this->lpermission->method('popular_restaurant', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'restaurant' || $this->uri->segment(2) == 'branch' || $this->uri->segment(2) == 'delivery_charge'  || $this->uri->segment(2) == 'addons_category' || $this->uri->segment(2) == 'popular_restaurants') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/restaurant/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('restaurant'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'restaurant' || $this->uri->segment(2) == 'branch' || $this->uri->segment(2) == 'delivery_charge'  || $this->uri->segment(2) == 'addons_category') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>


                    <ul class="sub-menu">
                        <?php
                        if (
                            $this->lpermission->method('restaurant', 'create')->access() ||
                            $this->lpermission->method('restaurant', 'read')->access() ||
                            $this->lpermission->method('restaurant', 'update')->access() ||
                            $this->lpermission->method('restaurant', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'restaurant' && $this->uri->segment(3) == 'view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/restaurant/view">
                                    <i class="fa fa-cutlery"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_res'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('menu', 'create')->access() ||
                            $this->lpermission->method('menu', 'read')->access() ||
                            $this->lpermission->method('menu', 'update')->access() ||
                            $this->lpermission->method('menu', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'view_menu' || $this->uri->segment(3) == 'add_menu' || $this->uri->segment(3) == 'edit_menu') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/restaurant/view_menu">
                                    <i class="fa fa-bars"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_res_menu'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <!-- <li class="start <?php echo ($this->uri->segment(3) == 'edit_res_menu' || $this->uri->segment(3) == 'add_menu' || $this->uri->segment(3) == 'edit_menu') ? "active" : ""; ?>">
                        <a href="<?php echo base_url() . ADMIN_URL; ?>/restaurant/edit_res_menu">
                            <i class="fa fa-bars"></i>
                            <span class="title"><?php echo $this->lang->line('edit_res_menu'); ?></span>
                            <span class="selected"></span>
                        </a>
                    </li> -->
                        <?php
                        if (
                            $this->lpermission->method('branch', 'create')->access() ||
                            $this->lpermission->method('branch', 'read')->access() ||
                            $this->lpermission->method('branch', 'update')->access() ||
                            $this->lpermission->method('branch', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'branch') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/branch/view">
                                    <i class="fa fa-building-o"></i>
                                    <span class="title"><?php echo $this->lang->line('manage_branch'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('addon_category', 'create')->access() ||
                            $this->lpermission->method('addon_category', 'read')->access() ||
                            $this->lpermission->method('addon_category', 'update')->access() ||
                            $this->lpermission->method('addon_category', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'addons_category') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/addons_category/view">
                                    <i class="fa fa-list-alt"></i>
                                    <span class="title"><?php echo $this->lang->line('addons_category'); ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('popular_restaurant', 'create')->access() ||
                            $this->lpermission->method('popular_restaurant', 'read')->access() ||
                            $this->lpermission->method('popular_restaurant', 'update')->access() ||
                            $this->lpermission->method('popular_restaurant', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'popular_restaurants') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/popular_restaurants/view">
                                    <i class="fa fa-fire" aria-hidden="true"></i>
                                    <span class="title">Popular Restaurants</span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                        <?php }
                        ?>
                    </ul>
                </li>

            <?php } ?>

            <!-- Zone and City -->
            <?php
            if (
                $this->lpermission->method('zone_area', 'create')->access() ||
                $this->lpermission->method('zone_area', 'read')->access() ||
                $this->lpermission->method('zone_area', 'update')->access() ||
                $this->lpermission->method('zone_area', 'delete')->access() ||
                $this->lpermission->method('city', 'create')->access() ||
                $this->lpermission->method('city', 'read')->access() ||
                $this->lpermission->method('city', 'update')->access() ||
                $this->lpermission->method('city', 'delete')->access()
            ) { ?>
                <li class="start <?php echo (($this->uri->segment(2) == 'zone' && $this->uri->segment(3) == 'city_view') || ($this->uri->segment(2) == 'zone' && $this->uri->segment(3) == 'view')) ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/view">
                        <i class="fa fa-building"></i>
                        <span class="title"><?php echo "Zone & City"; ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'zone' && $this->uri->segment(3) == 'view' || $this->uri->segment(3) == 'city_view') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php
                        if (
                            $this->lpermission->method('zone_area', 'create')->access() ||
                            $this->lpermission->method('zone_area', 'read')->access() ||
                            $this->lpermission->method('zone_area', 'update')->access() ||
                            $this->lpermission->method('zone_area', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'zone' && $this->uri->segment(3) == 'view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/view">
                                    <i class="fa fa-building" aria-hidden="true"></i>
                                    <span class="title">Zone Area</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php
                        if (
                            $this->lpermission->method('city', 'create')->access() ||
                            $this->lpermission->method('city', 'read')->access() ||
                            $this->lpermission->method('city', 'update')->access() ||
                            $this->lpermission->method('city', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'zone' && $this->uri->segment(3) == 'city_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/city_view">
                                    <i class="fa fa-building" aria-hidden="true"></i>
                                    <span class="title">City Area</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>





                    </ul>
                </li>
            <?php } ?>

            <!-- Zone -->
            <!-- <?php
                    if (
                        $this->lpermission->method('zone_area', 'create')->access() ||
                        $this->lpermission->method('zone_area', 'read')->access() ||
                        $this->lpermission->method('zone_area', 'update')->access() ||
                        $this->lpermission->method('zone_area', 'delete')->access()
                    ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'zone') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/view">
                        <i class="fa fa-area-chart" aria-hidden="true"></i>
                        <span class="title">Zone Area</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?> -->

            <!-- city -->
            <!-- <?php
                    if (
                        $this->lpermission->method('city', 'create')->access() ||
                        $this->lpermission->method('city', 'read')->access() ||
                        $this->lpermission->method('city', 'update')->access() ||
                        $this->lpermission->method('city', 'delete')->access()
                    ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'city') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/zone/city_view">
                        <i class="fa fa-area-chart" aria-hidden="true"></i>
                        <span class="title">City Area</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?> -->

            <?php
            if (
                $this->lpermission->method('menu_category', 'create')->access() ||
                $this->lpermission->method('menu_category', 'read')->access() ||
                $this->lpermission->method('menu_category', 'update')->access() ||
                $this->lpermission->method('menu_category', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'category') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/category/view">
                        <i class="fa fa-list-alt"></i>
                        <span class="title"><?php echo $this->lang->line('menu_category'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <?php if (
                $this->lpermission->method('campaign', 'create')->access() ||
                $this->lpermission->method('campaign', 'read')->access() ||
                $this->lpermission->method('campaign', 'update')->access() ||
                $this->lpermission->method('campaign', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == "campaign") ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/campaign/view">
                        <i class="fa fa-cutlery"></i>
                        <span class="title">Campaign</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php }
            ?>

            <!-- <?php
                    if (
                        $this->lpermission->method('orders', 'create')->access() ||
                        $this->lpermission->method('orders', 'read')->access() ||
                        $this->lpermission->method('orders', 'update')->access() ||
                        $this->lpermission->method('orders', 'delete')->access()
                    ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'order') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/order/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('orders'); ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'order') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="start <?php echo ($this->uri->segment(2) == 'order' && $this->uri->segment(3) != 'pending' && $this->uri->segment(3) != 'preorder' && $this->uri->segment(3) != 'delivered' && $this->uri->segment(3) != 'on-going') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/order/view">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="title"><?php echo "Dispatch Panel"; ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'preorder') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/order/preorder">
                                <i class="fa fa-adjust"></i>
                                <span class="title">Pre Order</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'pending') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/order/pending">
                                <i class="fa fa-clock-o"></i>
                                <span class="title"><?php echo $this->lang->line('placed'); ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'delivered') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/order/delivered">
                                <i class="fa fa-truck"></i>
                                <span class="title"><?php echo $this->lang->line('delivered'); ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'on-going') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/order/on-going">
                                <i class="fa fa-motorcycle"></i>
                                <span class="title"><?php echo $this->lang->line('onGoing'); ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'cancel') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/order/cancel">
                                <i class="fa fa-times"></i>
                                <span class="title"><?php echo $this->lang->line('cancel'); ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php } ?> -->
            <?php
            if (
                $this->lpermission->method('coupons', 'create')->access() ||
                $this->lpermission->method('coupons', 'read')->access() ||
                $this->lpermission->method('coupons', 'update')->access() ||
                $this->lpermission->method('coupons', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'coupon') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/coupon/view">
                        <i class="fa fa-dollar"></i>
                        <span class="title"><?php echo $this->lang->line('coupons'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <!-- Feature Item -->
            <?php
            if (
                $this->lpermission->method('feature_items', 'create')->access() ||
                $this->lpermission->method('feature_items', 'read')->access() ||
                $this->lpermission->method('feature_items', 'update')->access() ||
                $this->lpermission->method('feature_items', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'feature_items') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/feature_items/view">
                        <i class="fa fa-list-alt"></i>
                        <span class="title"><?php echo $this->lang->line('feature_items'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <!-- Popup Slider -->

            <?php
            if (
                $this->lpermission->method('popup_banner', 'create')->access() ||
                $this->lpermission->method('popup_banner', 'read')->access() ||
                $this->lpermission->method('popup_banner', 'update')->access() ||
                $this->lpermission->method('popup_banner', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'PopupSlider') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/PopupSlider/view">
                        <i class="fa fa-image"></i>
                        <span class="title"><?php echo "Popup Banner"; ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <?php
            if (
                $this->lpermission->method('rating_review', 'create')->access() ||
                $this->lpermission->method('rating_review', 'read')->access() ||
                $this->lpermission->method('rating_review', 'update')->access() ||
                $this->lpermission->method('rating_review', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'review') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/review/view">
                        <i class="fa fa-star"></i>
                        <span class="title"><?php echo $this->lang->line('rating_review'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <?php
            if (
                $this->lpermission->method('notifications', 'create')->access() ||
                $this->lpermission->method('notifications', 'read')->access() ||
                $this->lpermission->method('notifications', 'update')->access() ||
                $this->lpermission->method('notifications', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'notification') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/notification/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('notification'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <?php
            if (
                $this->lpermission->method('slider', 'create')->access() ||
                $this->lpermission->method('slider', 'read')->access() ||
                $this->lpermission->method('slider', 'update')->access() ||
                $this->lpermission->method('slider', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'slider-image') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/slider-image/view">
                        <i class="fa fa-image"></i>
                        <span class="title"><?php echo $this->lang->line('slider'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <?php
            if (
                $this->lpermission->method('cms', 'create')->access() ||
                $this->lpermission->method('cms', 'read')->access() ||
                $this->lpermission->method('cms', 'update')->access() ||
                $this->lpermission->method('cms', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'cms') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/cms/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('cms'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>

            <?php
            if (
                $this->lpermission->method('system_options', 'create')->access() ||
                $this->lpermission->method('system_options', 'read')->access() ||
                $this->lpermission->method('system_options', 'update')->access() ||
                $this->lpermission->method('system_options', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'system_option' && $this->uri->segment(3) == 'view') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo $this->lang->line('titleadmin_systemoptions'); ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>


            <?php
            if (
                $this->lpermission->method('reward_point_setting', 'create')->access() ||
                $this->lpermission->method('reward_point_setting', 'read')->access() ||
                $this->lpermission->method('reward_point_setting', 'update')->access() ||
                $this->lpermission->method('reward_point_setting', 'delete')->access() ||

                $this->lpermission->method('vouchers_request', 'create')->access() ||
                $this->lpermission->method('vouchers_request', 'read')->access() ||
                $this->lpermission->method('vouchers_request', 'update')->access() ||
                $this->lpermission->method('vouchers_request', 'delete')->access() ||

                $this->lpermission->method('earning_report', 'create')->access() ||
                $this->lpermission->method('earning_report', 'read')->access() ||
                $this->lpermission->method('earning_report', 'update')->access() ||
                $this->lpermission->method('earning_report', 'delete')->access() ||

                $this->lpermission->method('burning_report', 'create')->access() ||
                $this->lpermission->method('burning_report', 'read')->access() ||
                $this->lpermission->method('burning_report', 'update')->access() ||
                $this->lpermission->method('burning_report', 'delete')->access() ||

                $this->lpermission->method('claim_report', 'create')->access() ||
                $this->lpermission->method('claim_report', 'read')->access() ||
                $this->lpermission->method('claim_report', 'update')->access() ||
                $this->lpermission->method('claim_report', 'delete')->access()

            ) {

            ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'system_option' && ($this->uri->segment(3) == 'reward_view' || $this->uri->segment(3) == 'voucher_request' || $this->uri->segment(3) == 'Earning_report_view' || $this->uri->segment(3) == 'Burning_report_view' || $this->uri->segment(3) == 'Claim_report_view')) ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/reward_view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo "CRM" /*$this->lang->line('orders');*/ ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'system_option') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php
                        if (
                            $this->lpermission->method('reward_point_setting', 'create')->access() ||
                            $this->lpermission->method('reward_point_setting', 'read')->access() ||
                            $this->lpermission->method('reward_point_setting', 'update')->access() ||
                            $this->lpermission->method('reward_point_setting', 'delete')->access()
                        ) {
                        ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'reward_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/reward_view">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Reward Point Setting"; ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('vouchers_request', 'create')->access() ||
                            $this->lpermission->method('vouchers_request', 'read')->access() ||
                            $this->lpermission->method('vouchers_request', 'update')->access() ||
                            $this->lpermission->method('vouchers_request', 'delete')->access()
                        ) {
                        ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'voucher_request') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/voucher_request">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Vouchers Request"; ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('earning_report', 'create')->access() ||
                            $this->lpermission->method('earning_report', 'read')->access() ||
                            $this->lpermission->method('earning_report', 'update')->access() ||
                            $this->lpermission->method('earning_report', 'delete')->access()
                        ) {
                        ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'Earning_report_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/Earning_report_view">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Earning Report"; ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('burning_report', 'create')->access() ||
                            $this->lpermission->method('burning_report', 'read')->access() ||
                            $this->lpermission->method('burning_report', 'update')->access() ||
                            $this->lpermission->method('burning_report', 'delete')->access()
                        ) {
                        ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'Burning_report_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/Burning_report_view">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Burning Report"; ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php
                        if (
                            $this->lpermission->method('claim_report', 'create')->access() ||
                            $this->lpermission->method('claim_report', 'read')->access() ||
                            $this->lpermission->method('claim_report', 'update')->access() ||
                            $this->lpermission->method('claim_report', 'delete')->access()
                        ) {
                        ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'Claim_report_view') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/Claim_report_view">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Claim Report"; ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </li>
            <?php } ?>

            <!-- Refund -->
            <?php
            if (
                $this->lpermission->method('refund', 'create')->access() ||
                $this->lpermission->method('refund', 'read')->access() ||
                $this->lpermission->method('refund', 'update')->access() ||
                $this->lpermission->method('refund', 'delete')->access()
            ) {
            ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'system_option' && $this->uri->segment(3) == 'refund_view') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/system_option/refund_view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo "Refund"; ?></span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>
            <!--  -->

            <?php
            if (
                $this->lpermission->method('all_order_report', 'create')->access() ||
                $this->lpermission->method('all_order_report', 'read')->access() ||
                $this->lpermission->method('all_order_report', 'update')->access() ||
                $this->lpermission->method('all_order_report', 'delete')->access() ||

                $this->lpermission->method('riders_report', 'create')->access() ||
                $this->lpermission->method('riders_report', 'read')->access() ||
                $this->lpermission->method('riders_report', 'update')->access() ||
                $this->lpermission->method('riders_report', 'delete')->access() ||

                $this->lpermission->method('all_delivered_report', 'create')->access() ||
                $this->lpermission->method('all_delivered_report', 'read')->access() ||
                $this->lpermission->method('all_delivered_report', 'update')->access() ||
                $this->lpermission->method('all_delivered_report', 'delete')->access() ||

                $this->lpermission->method('order_report_customer_wise', 'create')->access() ||
                $this->lpermission->method('order_report_customer_wise', 'read')->access() ||
                $this->lpermission->method('order_report_customer_wise', 'update')->access() ||
                $this->lpermission->method('order_report_customer_wise', 'delete')->access() ||

                $this->lpermission->method('order_report_restaurant_wise', 'create')->access() ||
                $this->lpermission->method('order_report_restaurant_wise', 'read')->access() ||
                $this->lpermission->method('order_report_restaurant_wise', 'update')->access() ||
                $this->lpermission->method('order_report_restaurant_wise', 'delete')->access() ||

                $this->lpermission->method('user_acquisition_report', 'create')->access() ||
                $this->lpermission->method('user_acquisition_report', 'read')->access() ||
                $this->lpermission->method('user_acquisition_report', 'update')->access() ||
                $this->lpermission->method('user_acquisition_report', 'delete')->access()

            ) {

            ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'report_template' && ($this->uri->segment(3) != 'Claim_report_view' || $this->uri->segment(3) != 'Earning_report_view' || $this->uri->segment(3) != 'Burning_report_view')) ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/view">
                        <i class="fa fa-file-text"></i>
                        <span class="title"><?php echo "Reports" /*$this->lang->line('orders');*/ ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'report_template' && ($this->uri->segment(3) != 'Claim_report_view' || $this->uri->segment(3) != 'Earning_report_view' || $this->uri->segment(3) != 'Burning_report_view')) ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php
                        if (
                            $this->lpermission->method('all_order_report', 'create')->access() ||
                            $this->lpermission->method('all_order_report', 'read')->access() ||
                            $this->lpermission->method('all_order_report', 'update')->access() ||
                            $this->lpermission->method('all_order_report', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(2) == 'report_template' &&
                                                    ($this->uri->segment(3) != 'viewRiders' && $this->uri->segment(3) != 'rider_salary_report' && $this->uri->segment(3) != 'viewDeliveredReport' &&
                                                        $this->uri->segment(3) != 'Account_report' && $this->uri->segment(3) != 'viewDeliveredReport' &&
                                                        $this->uri->segment(3) != 'viewCancelReport' && $this->uri->segment(3) != 'viewResOrders'
                                                        && $this->uri->segment(3) != 'viewUserAcquisition' && $this->uri->segment(3) != 'viewResItems'
                                                        && $this->uri->segment(3) != 'viewUserItems' && $this->uri->segment(3) != 'viewTopUsers' &&
                                                        $this->uri->segment(3) != 'viewTopUsersOrderValueWise')) ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/view">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "All Order Report" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (
                            $this->lpermission->method('all_delivered_report', 'create')->access() ||
                            $this->lpermission->method('all_delivered_report', 'read')->access() ||
                            $this->lpermission->method('all_delivered_report', 'update')->access() ||
                            $this->lpermission->method('all_delivered_report', 'delete')->access()
                        ) { ?>

                            <li class="start <?php echo ($this->uri->segment(3) == 'viewDeliveredReport') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/viewDeliveredReport">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "All Delivered Report" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                        <?php } ?>



                        <?php if (
                            $this->lpermission->method('riders_report', 'create')->access() ||
                            $this->lpermission->method('riders_report', 'read')->access() ||
                            $this->lpermission->method('riders_report', 'update')->access() ||
                            $this->lpermission->method('riders_report', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'viewRiders') ? "active" : ""; ?>">

                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/viewRiders">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Riders Report" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (
                            $this->lpermission->method('riders_report', 'create')->access() ||
                            $this->lpermission->method('riders_report', 'read')->access() ||
                            $this->lpermission->method('riders_report', 'update')->access() ||
                            $this->lpermission->method('riders_report', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'rider_salary_report') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/rider_salary_report">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Rider Salary Report" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>


                        <!-- added on 13-03-2022 -->
                        <?php if (
                            $this->lpermission->method('riders_report', 'create')->access() ||
                            $this->lpermission->method('riders_report', 'read')->access() ||
                            $this->lpermission->method('riders_report', 'update')->access() ||
                            $this->lpermission->method('riders_report', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'Account_report') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/Account_report">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Account Report" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>




                        <?php if (
                            $this->lpermission->method('order_report_customer_wise', 'create')->access() ||
                            $this->lpermission->method('order_report_customer_wise', 'read')->access() ||
                            $this->lpermission->method('order_report_customer_wise', 'update')->access() ||
                            $this->lpermission->method('order_report_customer_wise', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'viewCancelReport') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/viewCancelReport">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Order Report(Customer Wise)" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if (
                            $this->lpermission->method('order_report_restaurant_wise', 'create')->access() ||
                            $this->lpermission->method('order_report_restaurant_wise', 'read')->access() ||
                            $this->lpermission->method('order_report_restaurant_wise', 'update')->access() ||
                            $this->lpermission->method('order_report_restaurant_wise', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'viewResOrders') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/viewResOrders">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "Order Report(Restaurant Wise)" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if (
                            $this->lpermission->method('user_acquisition_report', 'create')->access() ||
                            $this->lpermission->method('user_acquisition_report', 'read')->access() ||
                            $this->lpermission->method('user_acquisition_report', 'update')->access() ||
                            $this->lpermission->method('user_acquisition_report', 'delete')->access()
                        ) { ?>
                            <li class="start <?php echo ($this->uri->segment(3) == 'viewUserAcquisition') ? "active" : ""; ?>">
                                <a href="<?php echo base_url() . ADMIN_URL; ?>/report_template/viewUserAcquisition">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title"><?php echo "User Acquisition Report" /*$this->lang->line('all_orders');*/ ?></span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>


                    </ul>
                </li>

            <?php } ?>

            <?php if (
                $this->lpermission->method('roles_permissions', 'create')->access() ||
                $this->lpermission->method('roles_permissions', 'read')->access() ||
                $this->lpermission->method('roles_permissions', 'update')->access() ||
                $this->lpermission->method('roles_permissions', 'delete')->access()
            ) { ?>
                <li class="start <?php echo ($this->uri->segment(2) == 'Permission') ? "active" : ""; ?>">
                    <a href="<?php echo base_url() . ADMIN_URL; ?>/Permission/add_role">
                        <i class="fa fa-lock"></i>
                        <span class="title"><?php echo "Roles and Permission" /*$this->lang->line('orders');*/ ?></span>
                        <span class="arrow <?php echo ($this->uri->segment(2) == 'Permission') ? "open" : ""; ?>"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="start <?php echo ($this->uri->segment(2) == 'Permission' && $this->uri->segment(3) == "add_role") ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/Permission/add_role">
                                <i class="fa fa-file-text"></i>
                                <span class="start"><?php echo "Add Role" /*$this->lang->line('all_orders');*/ ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'role_list') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/Permission/role_list">
                                <i class="fa fa-file-text"></i>
                                <span class="start"><?php echo "Role List" /*$this->lang->line('all_orders');*/ ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'assign_rule') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/Permission/assign_rule">
                                <i class="fa fa-file-text"></i>
                                <span class="start"><?php echo "User Assign Role" /*$this->lang->line('all_orders');*/ ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <!-- <li class="start <?php echo ($this->uri->segment(3) == 'add_module') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/Permission/add_module">
                                <i class="fa fa-file-text"></i>
                                <span class="start"><?php echo "Add Module" ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="start <?php echo ($this->uri->segment(3) == 'add_sub_module') ? "active" : ""; ?>">
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/Permission/add_sub_module">
                                <i class="fa fa-file-text"></i>
                                <span class="start"><?php echo "Add Sub Module" ?></span>
                                <span class="selected"></span>
                            </a>
                        </li> -->
                    </ul>
                </li>

            <?php } ?>

        </ul>
    </div>
</div>