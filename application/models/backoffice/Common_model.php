<?php
class Common_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //get notification count
    public function getNotificationCount()
    {
        $this->db->select('order_count');
        $this->db->where('admin_id', $this->session->userdata('UserID'));
        return $this->db->get('order_notification')->first_row();
    }
    public function getAllModifiedMenucount()
    {
        $this->db->select('count(*) as allcount');
        $this->db->select('restaurant_menu_item.name as menu_name,restaurant.name as res_name');
        $this->db->join('restaurant_menu_item', 'restaurant_menu_item.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('restaurant_menu_item.status = 0');
        $this->db->where('restaurant_menu_item.need_modification = 1');

        if (!($this->lpermission->method('full_menu_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
            }
        }

        return $this->db->get('restaurant')->first_row();
    }
    //get notification count
    public function getEventNotificationCount()
    {
        $this->db->select('event_count');
        $this->db->where('admin_id', $this->session->userdata('UserID'));
        return $this->db->get('event_notification')->first_row();
    }
    //get voucher count
    public function getVoucherCount()
    {
        $this->db->select('*');
        $this->db->where('is_read', 0);
        return $this->db->count_all_results('voucher_notification');
    }
    public function getLanguages()
    {
        $this->db->where('active', 1);
        return $this->db->get_where('languages')->result();
    }
    public function getCmsPages($language_slug, $cms_slug = NULL)
    {
        if (!empty($cms_slug)) {
            $array = array('language_slug' => $language_slug, 'status' => 1, 'CMSSlug' => $cms_slug);
        } else {
            $array = array('language_slug' => $language_slug, 'status' => 1);
        }
        return $this->db->get_where('cms', $array)->result();
    }
    public function getFirstLanguages($slug)
    {
        return $this->db->get_where('languages', array('language_slug' => $slug))->first_row();
    }
    //get default lang
    public function getdefaultlang()
    {
        return $this->db->get_where('languages', array('language_default' => 1))->first_row();
    }
    //get item discount
    public function getItemDiscount($where)
    {
        $this->db->where($where);
        $this->db->where('end_date >', date('Y-m-d H:i:s'));
        $result['couponAmount'] =  $this->db->get('coupon')->result_array();
        if (!empty($result['couponAmount'])) {
            $res = array_column($result['couponAmount'], 'entity_id');
            $this->db->where_in('coupon_id', $res);
            $result['itemDetail'] = $this->db->get('coupon_item_map')->result_array();
        }
        return $result;
    }
    //get table data
    public function getRestaurantinSession($tblname, $UserID)
    {
        $this->db->where('created_by', $UserID);
        return $this->db->get($tblname)->result_array();
    }

    // get all the currencies
    public function getCountriesCurrency()
    {
        return $this->db->get('currencies')->result_array();
    }
    // get the currency id from currency name
    public function getCurrencyID($currency_name)
    {
        return $this->db->get_where('currencies', array('currency_name' => $currency_name))->first_row();
    }
    // get currency symbol
    public function getCurrencySymbol($currency_id)
    {
        return $this->db->get_where('currencies', array('currency_id' => $currency_id))->first_row();
    }
    // get currency symbol
    public function getRestaurantCurrency($content_id)
    {
        return $this->db->get_where('restaurant', array('content_id' => $content_id))->first_row();
    }
    // get currency symbol
    public function getRestaurantCurrencySymbol($restaurant_id)
    {
        $this->db->select('currencies.currency_symbol');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        return $this->db->get_where('restaurant', array('entity_id' => $restaurant_id))->first_row();
    }
    // get currency symbol
    public function getEventCurrencySymbol($entity_id)
    {
        $this->db->select('currencies.currency_symbol');
        $this->db->join('restaurant', 'event.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        return $this->db->get_where('event', array('event.entity_id' => $entity_id))->first_row();
    }
    /****************************************
    Function: addData, Add record in table
    $tablename: Name of table
    $data: array of data
     *****************************************/
    public function addData($tablename, $data)
    {
        $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }

    /****************************************
    Function: updateData, Update records in table
    $tablename: Name of table
    $data: array of data
    $wherefieldname: where field name
    $wherefieldvalue: where field value
     ****************************************/
    public function updateData($tablename, $data, $wherefieldname, $wherefieldvalue)
    {
        $this->db->where($wherefieldname, $wherefieldvalue);
        $this->db->update($tablename, $data);
        return $this->db->affected_rows();
    }

    /****************************************
    Function: updateData, Delete records from table
    $tablename: Name of table
    $wherefieldname: where field name
    $wherefieldvalue: where field value
     ****************************************/
    public function deleteData($tablename, $wherefieldname, $wherefieldvalue)
    {
        $this->db->where($wherefieldname, $wherefieldvalue);
        return $this->db->delete($tablename);
    }

    /****************************************
    Function: getSingleRow, get first row from table in Object format using single WHERE clause
    $tablename: Name of table
    $wherefieldname: where field name
    $wherefieldvalue: where field value
     ****************************************/
    public function getSingleRow($tablename, $wherefieldname, $wherefieldvalue)
    {
        $this->db->where($wherefieldname, $wherefieldvalue);
        return $this->db->get($tablename)->first_row();
    }

    /****************************************
    Function: getMultipleRows, get multiple row from table in Object format using single WHERE clause
    $tablename: Name of table
    $wherefieldname: where field name
    $wherefieldvalue: where field value
     ****************************************/
    public function getMultipleRows($tablename, $wherefieldname, $wherefieldvalue)
    {
        $this->db->where($wherefieldname, $wherefieldvalue);
        return $this->db->get($tablename)->result();
    }

    /****************************************
    Function: getRowsMultipleWhere, get row from table in Object format using multiple WHERE clause
    $tablename: Name of table
    $wherearray: where field array
     ****************************************/
    public function getRowsMultipleWhere($tablename, $wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->result();
    }

    public function getSingleRowMultipleWhere($tablename, $wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->first_row();
    }

    /****************************************
    Function: getAllRows, get row from table in array object format
    $tablename: Name of table
    $wherearray: where field array
     ****************************************/
    public function getAllRows($tablename)
    {
        return $this->db->get($tablename)->result();
    }
    /****************************************
    Function: getAllRecordArray, get row from table in array format
    $tablename: Name of table
     ****************************************/
    public function getAllRecordArray($tablename)
    {
        return $this->db->get($tablename)->result_array();
    }
    /****************************************
    Function: deleteInsertRecord, Delete existing records and insert new records
    $tablename: Name of table
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    $data: array of data that need to insert
     ****************************************/
    public function deleteInsertRecord($tablename, $wherefieldname, $wherefieldvalue, $data)
    {
        $this->db->where($wherefieldname, $wherefieldvalue);
        $this->db->delete($tablename);

        return $this->db->insert_batch($tablename, $data);
    }

    /****************************************
    Function: insertBatch, Bulk insert new records
    $tablename: Name of table
    $data: array of data that need to insert
     ****************************************/
    public function insertBatch($tablename, $data)
    {
        return $this->db->insert_batch($tablename, $data);
    }

    /****************************************
    Function: updateBatch, Bulk update records
    $tablename: Name of table
    $data: array of data that need to insert
    $fieldname: Field name used as WHERE Clause
     ****************************************/
    public function updateBatch($tablename, $data, $fieldname)
    {
        return $this->db->update_batch($tablename, $data, $fieldname);
    }

    public function getRestaurantReview($restaurant_id)
    {
        $this->db->select("review.restaurant_id,review.rating,review.review,users.first_name,users.last_name,users.image");
        $this->db->join('users', 'review.user_id = users.entity_id', 'left');
        $this->db->where('review.status', 1);
        $this->db->where('review.restaurant_id', $restaurant_id);
        $result =  $this->db->get('review')->result();
        $avg_rating = 0;
        if (!empty($result)) {
            $rating = array_column($result, 'rating');
            $a = array_filter($rating);
            if (count($a)) {
                $average = array_sum($a) / count($a);
            }
            $avg_rating = number_format($average, 1);
        }
        return $avg_rating;
    }

    public function getLang($language_slug)
    {
        $this->db->select('language_name,language_slug');
        $this->db->where('language_slug', $language_slug);
        return $this->db->get('languages')->first_row();
    }

    public function getUsersNotification($user_id, $status = NULL)
    {
        $notifications = array();
        // orders
        $this->db->where('user_id', $user_id);
        if ($status == 'unread') {
            $this->db->where('view_status', 0);
        }
        $this->db->order_by('datetime', 'desc');
        $orders = $this->db->get('user_order_notification')->result();
        if (!empty($orders)) {
            foreach ($orders as $key => $value) {
                $notifications[] = array(
                    'notification_type' => 'order',
                    'notification_type_id' => $value->user_notification_id,
                    'entity_id' => $value->order_id,
                    'user_id' => $value->user_id,
                    'notification_slug' => $value->notification_slug,
                    'view_status' => $value->view_status,
                    'datetime' => $value->datetime,
                );
            }
        }
        // events
        $this->db->where('user_id', $user_id);
        if ($status == 'unread') {
            $this->db->where('view_status', 0);
        }
        $this->db->order_by('datetime', 'desc');
        $events = $this->db->get('user_event_notifications')->result();
        if (!empty($events)) {
            foreach ($events as $key => $value) {
                $notifications[] = array(
                    'notification_type' => 'event',
                    'notification_type_id' => $value->event_notification_id,
                    'entity_id' => $value->event_id,
                    'user_id' => $value->user_id,
                    'notification_slug' => $value->notification_slug,
                    'view_status' => $value->view_status,
                    'datetime' => $value->datetime,
                );
            }
        }
        // sort array in descending order
        usort($notifications, function ($a, $b) {
            $dateA = date('Y-m-d H:i:s', strtotime($a['datetime']));
            $dateB = date('Y-m-d H:i:s', strtotime($b['datetime']));
            // descending ordering, use `<=` for ascending
            return $dateA <= $dateB;
        });
        return $notifications;
    }

    //get menu items
    public function getMenuItem($entity_id, $restaurant_id)
    {
        $language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
        $ItemDiscount = $this->getItemDiscount(array('status' => 1, 'coupon_type' => 'discount_on_items'));
        $couponAmount = $ItemDiscount['couponAmount'];
        $ItemDiscount = (!empty($ItemDiscount['itemDetail'])) ? array_column($ItemDiscount['itemDetail'], 'item_id') : array();

        $this->db->select(
            '
        menu.restaurant_id,
        menu.is_deal,
        menu.entity_id as menu_id,
        menu.status,
        menu.vat,
        menu.sd,
        menu.name,
        menu.price,
        menu.menu_detail,
        menu.image,
        menu.is_veg,
        menu.recipe_detail,
        availability,
        c.name as category,
        c.entity_id as category_id,
        add_ons_master.add_ons_name,
        add_ons_master.add_ons_price,
        add_ons_category.name as addons_category,
        menu.check_add_ons,
        add_ons_category.entity_id as addons_category_id,
        add_ons_master.add_ons_id,
        add_ons_master.variation_id,
        add_ons_master.has_variation,
        add_ons_master.is_multiple,
        variations.variation_name,
        variations.variation_add_on,
        variations.variation_price
        '
        );
        $this->db->join('category as c', 'menu.category_id = c.entity_id', 'left');
        $this->db->join('add_ons_master', 'menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1', 'left');
        $this->db->join('add_ons_category', 'add_ons_master.category_id = add_ons_category.entity_id', 'left');
        $this->db->join('variations', 'add_ons_master.variation_id = variations.entity_id', 'left');
        $this->db->where('menu.restaurant_id', $restaurant_id);
        $this->db->where('menu.language_slug', $language_slug);
        $this->db->where('menu.entity_id', $entity_id);
        $result = $this->db->get('restaurant_menu_item as menu')->result();

        $menu = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $offer_price = '';
                if (in_array($value->menu_id, $ItemDiscount)) {
                    if (!empty($couponAmount)) {
                        if ($couponAmount[0]['max_amount'] < $value->price) {
                            if ($couponAmount[0]['amount_type'] == 'Percentage') {
                                $offer_price = $value->price - round(($value->price * $couponAmount[0]['amount']) / 100);
                            } else if ($couponAmount[0]['amount_type'] == 'Amount') {
                                $offer_price = $value->price - $couponAmount[0]['amount'];
                            }
                        }
                    }
                }
                $offer_price = ($offer_price) ? $offer_price : '';
                if (!isset($menu[$value->category_id])) {
                    $menu[$value->category_id] = array();
                    $menu[$value->category_id]['category_id'] = $value->category_id;
                    $menu[$value->category_id]['category_name'] = $value->category;
                }
                $image = ($value->image) ? (image_url . $value->image) : (default_img);
                $total = 0;
                if ($value->check_add_ons == 1) {
                    if (!isset($menu[$value->category_id]['items'][$value->menu_id])) {
                        $menu[$value->category_id]['items'][$value->menu_id] = array();
                        $menu[$value->category_id]['items'][$value->menu_id] = array(
                            'restaurant_id' => $value->restaurant_id,
                            'menu_id' => $value->menu_id,
                            'name' => $value->name,
                            'price' => $value->price,
                            'offer_price' => $offer_price,
                            'menu_detail' => $value->menu_detail,
                            'image' => $image,
                            'recipe_detail' => $value->recipe_detail,
                            'availability' => $value->availability,
                            'is_veg' => $value->is_veg,
                            'is_customize' => $value->check_add_ons,
                            'is_deal' => $value->is_deal,
                            'has_variation' => $value->has_variation,
                            'status' => $value->status,
                            'vat' => $value->vat,
                            'sd' => $value->sd
                        );
                    }

                    if ($value->has_variation == 1) {


                        if (!isset($menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id])) {
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id] = array();
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['variation_id'] = $value->variation_id;
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['variation_name'] = $value->variation_name;
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['variation_price'] = $value->variation_price;
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['hasVariationAddon'] = $value->variation_add_on;
                        }

                        if ($value->variation_add_on == 1 && !isset($menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id])) {
                            $i = 0;
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id] = array();
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                            $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                        }
                        ($value->addons_category) ? $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name, 'add_ons_price' => $value->add_ons_price) : '';
                        ($value->is_multiple == 1) ? $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['max_choice'] = ($value->max_choice ? $value->max_choice : $i + 1) : '';
                        $i++;
                    } else {

                        if (!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id])) {
                            $i = 0;
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                            $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                        }
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name, 'add_ons_price' => $value->add_ons_price);
                        $i++;
                    }
                } else {
                    $menu[$value->category_id]['items'][]  = array('restaurant_id' => $value->restaurant_id, 'menu_id' => $value->menu_id, 'name' => $value->name, 'price' => $value->price, 'offer_price' => $offer_price, 'menu_detail' => $value->menu_detail, 'image' => $image, 'recipe_detail' => $value->recipe_detail, 'availability' => $value->availability, 'is_veg' => $value->is_veg, 'is_customize' => $value->check_add_ons, 'is_deal' => $value->is_deal, 'status' => $value->status, 'vat' => $value->vat, 'sd' => $value->sd);
                }
            }
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        $quarterfinal = array();
        $new = array();
        foreach ($menu as $nm => $va) {
            $final = array();
            foreach ($va['items'] as $kk => $items) {
                if ($items['is_customize'] == 1) {
                    if ($items['has_variation'] == 1) {
                        if (!empty($items['variation_list'])) {
                            $semifinal = array();
                            foreach ($items['variation_list'] as $variation_list) {
                                $quarterfinal = array();
                                if ($variation_list['hasVariationAddon'] == 1) {
                                    foreach ($variation_list['addons_category_list'] as $each_add_cat) {
                                        if ($each_add_cat) {
                                            array_push($quarterfinal, $each_add_cat);
                                        }
                                    }
                                    if ($variation_list) {
                                        $variation_list['addons_category_list'] = $quarterfinal;
                                        array_push($semifinal, $variation_list);
                                    }
                                } else {
                                    array_push($semifinal, $variation_list);
                                }
                            }
                            $items['variation_list'] = $semifinal;
                        }
                    } else {
                        if (!empty($items['addons_category_list'])) {
                            $semifinal = array();
                            foreach ($items['addons_category_list'] as $addons_cat_list) {
                                if ($addons_cat_list) {
                                    array_push($semifinal, $addons_cat_list);
                                }
                            }

                            $items['addons_category_list'] = $semifinal;
                        }
                    }
                }


                array_push($final, $items);
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }
        return $finalArray;
    }
    // get Cart items
    public function getCartItems($cart_details, $cart_restaurant)
    {
        $cartItems = array();
        $cartTotalPrice = 0;
        if (!empty($cart_details)) {
            foreach (json_decode($cart_details) as $key => $value) {
                $details = $this->getMenuItem($value->menu_id, $cart_restaurant);
                if (!empty($details)) {
                    if ($details[0]['items'][0]['is_customize'] == 1) {
                        if ($details[0]['items'][0]['has_variation'] == 1) {

                            $variation_id = $value->addons->variation_id;
                            $addons_category_id = array_column($value->addons->addons_category_list, 'addons_category_id');
                            $add_onns_id = array_column($value->addons->addons_category_list, 'add_onns_id');
                            foreach ($details[0]['items'][0]['variation_list'] as $k => $var) {
                                if (!($var['variation_id'] == $variation_id)) {
                                    unset($details[0]['items'][0]['variation_list'][$k]);
                                } else {
                                    if (!empty($var['addons_category_list'])) {
                                        foreach ($var['addons_category_list'] as $key => $cat_value) {
                                            if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
                                                unset($details[0]['items'][0]['variation_list'][$k]['addons_category_list'][$key]);
                                            } else {
                                                if (!empty($cat_value['addons_list'])) {
                                                    foreach ($cat_value['addons_list'] as $addkey => $add_value) {
                                                        if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
                                                            unset($details[0]['items'][0]['variation_list'][$k]['addons_category_list'][$key]['addons_list'][$addkey]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $addons_category_id = array_column($value->addons, 'addons_category_id');
                            $add_onns_id = array_column($value->addons, 'add_onns_id');

                            if (!empty($details[0]['items'][0]['addons_category_list'])) {
                                foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
                                    if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
                                        unset($details[0]['items'][0]['addons_category_list'][$key]);
                                    } else {
                                        if (!empty($cat_value['addons_list'])) {
                                            foreach ($cat_value['addons_list'] as $addkey => $add_value) {
                                                if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
                                                    unset($details[0]['items'][0]['addons_category_list'][$key]['addons_list'][$addkey]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // getting subtotal
                    if ($details[0]['items'][0]['is_customize'] == 1) {
                        $subtotal = 0;
                        if ($details[0]['items'][0]['has_variation'] == 1) {
                            foreach ($details[0]['items'][0]['variation_list'] as $k => $var) {
                                $subtotal += $var['variation_price'];
                                if (!empty($var['addons_category_list'])) {
                                    foreach ($var['addons_category_list'] as $key => $cat_value) {
                                        if (!empty($cat_value['addons_list'])) {
                                            foreach ($cat_value['addons_list'] as $addkey => $add_value) {
                                                $subtotal += $add_value['add_ons_price'];
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!empty($details[0]['items'][0]['addons_category_list'])) {
                                foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
                                    if (!empty($cat_value['addons_list'])) {
                                        foreach ($cat_value['addons_list'] as $addkey => $add_value) {
                                            $subtotal += $add_value['add_ons_price'];
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $subtotal = 0;
                        if ($details[0]['items'][0]['is_deal'] == 1) {
                            $price = ($details[0]['items'][0]['offer_price']) ? $details[0]['items'][0]['offer_price'] : (($details[0]['items'][0]['price']) ? $details[0]['items'][0]['price'] : 0);
                        } else {
                            $price = ($details[0]['items'][0]['price']) ? $details[0]['items'][0]['price'] : 0;
                        }
                        $subtotal = $subtotal + $price;
                    }
                    $cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
                    $cartItems[] = array(
                        'menu_id' => $details[0]['items'][0]['menu_id'],
                        'restaurant_id' => $cart_restaurant,
                        'name' => $details[0]['items'][0]['name'],
                        'quantity' => $value->quantity,
                        'is_customize' => $details[0]['items'][0]['is_customize'],
                        'is_veg' => $details[0]['items'][0]['is_veg'],
                        'is_deal' => $details[0]['items'][0]['is_deal'],
                        'price' => $details[0]['items'][0]['price'],
                        'vat' => $details[0]['items'][0]['vat'],
                        'sd' => $details[0]['items'][0]['sd'],
                        'offer_price' => $details[0]['items'][0]['offer_price'],
                        'subtotal' => $subtotal,
                        'totalPrice' => ($subtotal * $value->quantity),
                        'cartTotalPrice' => $cartTotalPrice,
                        'has_variation' => $details[0]['items'][0]['has_variation'] == 1 ? 1 : 0,
                        'variation_list' => $details[0]['items'][0]['variation_list'],
                        'addons_category_list' => $details[0]['items'][0]['addons_category_list'],
                    );
                }
            }
        }
        $cart_details = array(
            'cart_items' => $cartItems,
            'cart_total_price' => $cartTotalPrice,
        );
        return $cart_details;
    }

    //get country
    public function getSelectedPhoneCode()
    {
        $this->db->where('OptionSlug', 'phone_code');
        return $this->db->get('system_option')->first_row();
    }

    // get system options
    public function getSystemOptions()
    {
        return $this->db->get('system_option')->result_array();
    }

    public function isOperationOn()
    {
        $res = $this->db->select('name, value')
            ->from('operation_sytem_option')
            ->where('name', 'operation_on_off')
            ->get()
            ->first_row();

        if ($res->value == 1) {
            return true;
        }

        return false;
    }
}
