<?php
class Home_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //server side check email exist
    public function checkEmail($Email)
    {
        $this->db->where('email', $Email);
        return $this->db->get('users')->num_rows();
    }
    //server side check email exist
    public function checkPhone($Phone)
    {
        $this->db->where('mobile_number', $Phone);
        return $this->db->get('users')->num_rows();
    }
    // validation for mobile number
    public function mobileCheck($mobile_number)
    {
        return $this->db->get_where('users', array('mobile_number' => $mobile_number))->num_rows();
    }
    // get restaurant details
    public function getRestaurants($popular = null)
    {
        $language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug");
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->group_by('restaurant.content_id');
        // $this->db->group_by('order.restaurant_id');
        //$this->db->order_by(count(order.restaurant_id),'DESC');
        //$this->db->order_by('restaurant.entity_id','DESC');
        if ($popular) {
            $this->db->where('is_popular', 1);
        }
        $result = $this->db->get_where('restaurant', array('restaurant.status' => 1))->result_array();
        $finalData = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $timing = $value['timings'];
                if ($timing) {
                    $timing =  unserialize(html_entity_decode($timing));
                    $newTimingArr = array();
                    $day = date("l");
                    foreach ($timing as $keys => $values) {
                        $day = date("l");
                        if ($keys == strtolower($day)) {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open'])) ? date('g:i A', strtotime($values['open'])) : '';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close'])) ? date('g:i A', strtotime($values['close'])) : '';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                            $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open'])) ? (((date("H:m") < date("H:m", strtotime($values['close']))) && (date("H:m") >= date("H:m", strtotime($values['open'])))) ? 'Open' : 'Closed') : 'Closed';
                        }
                    }
                }
                $result[$key]['timings'] = $newTimingArr[strtolower($day)];
                $result[$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
            }
            $content_id = array();
            $RestDataArr = array();
            foreach ($result as $key => $value) {
                $content_id[] = $value['content_id'];
                $RestDataArr[$value['content_id']] = array(
                    'content_id' => $value['content_id'],
                    'restaurant_slug' => $value['restaurant_slug'],
                    'restaurant_id' => $value['restaurant_id']
                );
            }
            if (!empty($content_id)) {
                $this->db->select("restaurant.entity_id as restaurant_id, count(order.restaurant_id),restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug");
                $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
                $this->db->join('order_master as order', 'restaurant.entity_id = order.restaurant_id', 'left');
                $this->db->where_in('restaurant.content_id', $content_id);
                $this->db->where('restaurant.language_slug', $language_slug);

                $this->db->where('restaurant.status', 1);
                //$this->db->group_by('restaurant.content_id');
                $this->db->group_by('order.restaurant_id');
                // $this->db->order_by('restaurant.entity_id');
                $this->db->order_by(2, 'DESC');
                $restaurantLng = $this->db->get('restaurant')->result_array();

                foreach ($restaurantLng as $key => $value) {
                    $timing = $value['timings'];
                    if ($timing) {
                        $timing =  unserialize(html_entity_decode($timing));
                        $newTimingArr = array();
                        $day = date("l");
                        foreach ($timing as $keys => $values) {
                            $day = date("l");
                            if ($keys == strtolower($day)) {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open'])) ? date('g:i A', strtotime($values['open'])) : '';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close'])) ? date('g:i A', strtotime($values['close'])) : '';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open'])) ? (((date("H:m") < date("H:m", strtotime($values['close']))) && (date("H:m") >= date("H:m", strtotime($values['open'])))) ? 'Open' : 'Closed') : 'Closed';
                            }
                        }
                    }
                    $restaurantLng[$key]['timings'] = $newTimingArr[strtolower($day)];
                    $restaurantLng[$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
                }
                foreach ($restaurantLng as $key => $value) {
                    $finalData[$value['content_id']] = array(
                        'MainRestaurantID' => $value['restaurant_id'],
                        'name' => $value['name'],
                        'address' => $value['address'],
                        'landmark' => $value['landmark'],
                        'latitude' => $value['latitude'],
                        'longitude' => $value['longitude'],
                        'image' => $value['image'],
                        'timings' => $value['timings'],
                        'language_slug' => $value['language_slug'],
                        'content_id' => $RestDataArr[$value['content_id']]['content_id'],
                        'restaurant_slug' => $RestDataArr[$value['content_id']]['restaurant_slug'],
                        'restaurant_id' => $RestDataArr[$value['content_id']]['restaurant_id']
                    );
                }
            }
        }
        return $finalData;
    }
    // get restaurant reviews
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
    // get all categories
    public function getAllCategories()
    {
        $language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
        $this->db->where('language_slug', $language_slug);
        $this->db->group_by('content_id');
        return $this->db->get_where('category', array('status' => 1))->result();
    }

    public function getFeatureItems()
    {
        $this->db->select('restaurant_menu_item.*');
        $this->db->join('feature_items', 'feature_items.menu_item_id = restaurant_menu_item.entity_id', 'left');
        $this->db->where('restaurant_menu_item.status', 1);
        $this->db->where('feature_items.status', 1);
        $result = $this->db->get('restaurant_menu_item')->result();

        return $result;
    }
    // search restaurant details
    public function searchRestaurants($category_id)
    {
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug");
        $this->db->join('restaurant', 'restaurant_menu_item.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->where('restaurant_menu_item.category_id', $category_id);
        $this->db->where('restaurant_menu_item.status', 1);
        $this->db->group_by('restaurant.entity_id');
        $result = $this->db->get('restaurant_menu_item')->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $timing = $value['timings'];
                if ($timing) {
                    $timing =  unserialize(html_entity_decode($timing));
                    $newTimingArr = array();
                    $day = date("l");
                    foreach ($timing as $keys => $values) {
                        $day = date("l");
                        if ($keys == strtolower($day)) {
                            $newTimingArr[strtolower($day)]['open'] = (!empty($values['open'])) ? date('g:i A', strtotime($values['open'])) : '';
                            $newTimingArr[strtolower($day)]['close'] = (!empty($values['close'])) ? date('g:i A', strtotime($values['close'])) : '';
                            $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                            $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close']) && !empty($values['open'])) ? (((date("H:m") < date("H:m", strtotime($values['close']))) && (date("H:m") >= date("H:m", strtotime($values['open'])))) ? 'Open' : 'Closed') : 'Closed';
                        }
                    }
                }
                $result[$key]['timings'] = $newTimingArr[strtolower($day)];
                $result[$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
            }
        }
        return $result;
    }
    // get coupons
    public function getAllCoupons()
    {
        $this->db->order_by('updated_date', 'desc');
        $this->db->limit(6);
        return $this->db->get_where('coupon', array('status' => 1))->result();
    }

    public function getCampaign($restaurant_array = null)
    {
        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');

        $dates = ['start_date <=' => $currentTime, 'end_date >=' => $currentTime];
        $this->db->select('campaign.*');
        $this->db->where($dates);

        if ($restaurant_array && count($restaurant_array) > 0) {
            foreach ($restaurant_array as $res) {
                $res_array[] = $res->restuarant_id;
            }

            $this->db->join('campaign_restaurant_map', 'campaign_restaurant_map.campaign_id = campaign.entity_id');
            $this->db->where_in('campaign_restaurant_map.restaurant_id', $res_array);
        }

        $this->db->order_by('sort_value');
        $this->db->group_by('campaign.entity_id');

        $data =  $this->db->get('campaign')->result();

        foreach ($data as $key => $value) {
            $value->image = ($value->image) ? $value->image : '';
        }

        return $data;
    }
}
