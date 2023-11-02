<?php
class Restaurant_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    // get restaurant details
    public function getRestaurantDetail($content_id, $searchArray = NULL, $food = NULL, $price = NULL)
    {
        $language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.phone_number,restaurant.restaurant_slug,restaurant.content_id,currencies.currency_symbol,currencies.currency_code");
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('restaurant.language_slug', $language_slug);
        $this->db->where('restaurant.content_id', $content_id);
        $this->db->group_by('restaurant.entity_id');
        $result['restaurant'] = $this->db->get_where('restaurant', array('status' => 1))->result_array();
        if (!empty($result['restaurant'])) {
            foreach ($result['restaurant'] as $key => $value) {
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
                $result['restaurant'][$key]['timings'] = $newTimingArr[strtolower($day)];
                $result['restaurant'][$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
            }
        }
        $result['menu_items'] = array();
        $result['packages'] = array();
        $result['categories'] = array();
        if (!empty($result['restaurant'])) {
            $restaurant_id = $result['restaurant'][0]['restaurant_id'];
            $this->db->select('restaurant_menu_item.*');
            $this->db->where('restaurant_menu_item.restaurant_id', $restaurant_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach ($searchArray as $key => $value) {
                    $like_statementsOne[] = "restaurant_menu_item.name LIKE '%" . $value . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "restaurant_menu_item.menu_detail LIKE '%" . $value . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "restaurant_menu_item.availability LIKE '%" . $value . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('(' . $like_stringOne . ' OR ' . $like_stringTwo . ' OR ' . $like_stringThree . ')');
            }
            if ($price == "low") {
                $this->db->order_by('restaurant_menu_item.price', 'asc');
            } else {
                $this->db->order_by('restaurant_menu_item.price', 'desc');
            }
            if ($food == "non_veg") {
                $this->db->where('restaurant_menu_item.is_veg', 0);
            } else if ($food == "veg") {
                $this->db->where('restaurant_menu_item.is_veg', 1);
            }
            $result['menu_items'] = $this->db->get_where('restaurant_menu_item', array('status' => 1))->result_array();
            if (!empty($result['menu_items'])) {
                foreach ($result['menu_items'] as $key => $value) {
                    $result['menu_items'][$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
                }
            }

            $this->db->select('restaurant_package.*');
            $this->db->where('restaurant_package.restaurant_id', $restaurant_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach ($searchArray as $key => $value) {
                    $like_statementsOne[] = "restaurant_package.name LIKE '%" . $value . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "restaurant_package.detail LIKE '%" . $value . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "restaurant_package.availability LIKE '%" . $value . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('(' . $like_stringOne . ' OR ' . $like_stringTwo . ' OR ' . $like_stringThree . ')');
            }
            $result['packages'] = $this->db->get_where('restaurant_package', array('status' => 1))->result_array();
            if (!empty($result['packages'])) {
                foreach ($result['packages'] as $key => $value) {
                    $result['packages'][$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
                }
            }
            $this->db->select('restaurant_menu_item.category_id,category.name');
            $this->db->join('category', 'restaurant_menu_item.category_id = category.entity_id', 'left');
            $this->db->where('restaurant_menu_item.restaurant_id', $restaurant_id);
            if (!empty($searchArray)) {
                $like_statementsOne = array();
                $like_statementsTwo = array();
                $like_statementsThree = array();
                foreach ($searchArray as $key => $value) {
                    $like_statementsOne[] = "restaurant_menu_item.name LIKE '%" . $value . "%'";
                    $like_stringOne = "(" . implode(' OR ', $like_statementsOne) . ")";
                    $like_statementsTwo[] = "restaurant_menu_item.menu_detail LIKE '%" . $value . "%'";
                    $like_stringTwo = "(" . implode(' OR ', $like_statementsTwo) . ")";
                    $like_statementsThree[] = "restaurant_menu_item.availability LIKE '%" . $value . "%'";
                    $like_stringThree = "(" . implode(' OR ', $like_statementsThree) . ")";
                }
                $this->db->where('(' . $like_stringOne . ' OR ' . $like_stringTwo . ' OR ' . $like_stringThree . ')');
            }
            if ($price == "low") {
                $this->db->order_by('restaurant_menu_item.price', 'asc');
            } else {
                $this->db->order_by('restaurant_menu_item.price', 'desc');
            }
            if ($food == "non_veg") {
                $this->db->where('restaurant_menu_item.is_veg', 0);
            } else if ($food == "veg") {
                $this->db->where('restaurant_menu_item.is_veg', 1);
            }
            $this->db->group_by('restaurant_menu_item.category_id');
            $result['categories'] = $this->db->get_where('restaurant_menu_item', array('restaurant_menu_item.status' => 1))->result_array();
            if (!empty($result['categories'])) {
                foreach ($result['categories'] as $key => $value) {
                    $this->db->select('restaurant_menu_item.*');
                    $this->db->where('restaurant_menu_item.restaurant_id', $restaurant_id);
                    $this->db->where('restaurant_menu_item.category_id', $value['category_id']);
                    if ($price == "low") {
                        $this->db->order_by('restaurant_menu_item.price', 'asc');
                    } else {
                        $this->db->order_by('restaurant_menu_item.price', 'desc');
                    }
                    if ($food == "non_veg") {
                        $this->db->where('restaurant_menu_item.is_veg', 0);
                    } else if ($food == "veg") {
                        $this->db->where('restaurant_menu_item.is_veg', 1);
                    }
                    $result[$value['name']] = $this->db->get_where('restaurant_menu_item', array('status' => 1))->result_array();
                }
            }
        }
        return $result;
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
    // get restaurant id
    public function getRestaurantID($restaurant_slug)
    {
        $this->db->select('entity_id');
        return $this->db->get_where('restaurant', array('restaurant_slug' => $restaurant_slug))->first_row();
    }
    // get content id from slug
    public function getContentID($restaurant_slug)
    {
        $this->db->select('content_id');
        return $this->db->get_where('restaurant', array('restaurant_slug' => $restaurant_slug))->first_row();
    }
    // get content id from restaurant id
    public function getRestContentID($restaurant_id)
    {
        $this->db->select('content_id');
        return $this->db->get_where('restaurant', array('entity_id' => $restaurant_id))->first_row();
    }
    // get All Restaurants
    public function getAllRestaurants($limit, $offset, $search_item = NULL)
    {
        $language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug");
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->join('restaurant_menu_item', 'restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.language_slug = "' . $language_slug . '"', 'left');
        $this->db->where('restaurant.language_slug', $language_slug);
        $this->db->group_by('restaurant.content_id');
        if (!empty($search_item)) {
            $this->db->where("restaurant.name LIKE '%" . $search_item . "%' OR restaurant_menu_item.name LIKE '%" . $search_item . "%'");
        }
        $this->db->limit($limit, $offset);
        $result['data'] = $this->db->get_where('restaurant', array('restaurant.status' => 1))->result_array();

        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
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
                $result['data'][$key]['timings'] = $newTimingArr[strtolower($day)];
                $result['data'][$key]['image'] = ($value['image']) ? image_url . $value['image'] : '';
            }
        }
        // total count
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug");
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->join('restaurant_menu_item', 'restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.language_slug = "' . $language_slug . '"', 'left');
        $this->db->where('restaurant.language_slug', $language_slug);
        $this->db->group_by('restaurant.content_id');
        if (!empty($search_item)) {
            $this->db->where("restaurant.name LIKE '%" . $search_item . "%' OR restaurant_menu_item.name LIKE '%" . $search_item . "%'");
        }
        $result['count'] =  $this->db->get_where('restaurant', array('restaurant.status' => 1))->num_rows();
        return $result;
    }
    //get ratings and reviews of a restaurant
    public function getReviewsRatings($restaurant_id)
    {
        $this->db->select('review.*,users.first_name,users.last_name,users.image');
        $this->db->join('users', 'review.user_id = users.entity_id', 'left');
        $this->db->where('review.restaurant_id', $restaurant_id);
        return $this->db->get_where('review', array('review.status' => 1))->result_array();
    }
    //check booking availability
    public function getBookingAvailability($date, $people, $restaurant_id)
    {
        $date = date('Y-m-d H:i:s', strtotime($date));
        $datetime = date($date, strtotime('+1 hours'));
        $this->db->select('capacity,timings');
        $this->db->where('entity_id', $restaurant_id);
        $capacity =  $this->db->get('restaurant')->first_row();

        if ($capacity) {
            $timing = $capacity->timings;
            if ($timing) {
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date('l', strtotime($date));
                foreach ($timing as $keys => $values) {
                    $day = date('l', strtotime($date));
                    if ($keys == strtolower($day)) {
                        $newTimingArr[strtolower($day)]['open'] = (!empty($values['open'])) ? date('g:i A', strtotime($values['open'])) : '';
                        $newTimingArr[strtolower($day)]['close'] = (!empty($values['close'])) ? date('g:i A', strtotime($values['close'])) : '';
                        $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                        $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                    }
                }
            }
            $capacity->timings = $newTimingArr[strtolower($day)];
            //for booking
            $this->db->select('IFNULL(SUM(no_of_people),0) as people');
            $this->db->where('booking_date', $datetime);
            $this->db->where('restaurant_id', $restaurant_id);
            $event = $this->db->get('event')->first_row();
            //get event booking
            $peopleCount = $capacity->capacity - $event->people;
            if ($peopleCount >= $people && (date('H:i', strtotime($capacity->timings['close'])) > date('H:i', strtotime($date))) && (date('H:i', strtotime($capacity->timings['open'])) < date('H:i', strtotime($date)))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    //get tax
    public function getRestaurantTax($tblname, $restaurant_id, $flag)
    {
        if ($flag == 'order') {
            $this->db->select('restaurant.name,restaurant.image,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code');
            $this->db->join('restaurant_address', 'restaurant.entity_id = restaurant_address.resto_entity_id', 'left');
            $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        } else {
            $this->db->select('restaurant.name,restaurant.image,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant.amount_type,restaurant.amount,restaurant_address.latitude,restaurant_address.longitude');
            $this->db->join('restaurant_address', 'restaurant.entity_id = restaurant_address.resto_entity_id', 'left');
            $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        }
        $this->db->where('restaurant.entity_id', $restaurant_id);
        return $this->db->get($tblname)->first_row();
    }
    // get number of restaurant reviews
    public function getReviewsNumber($restaurant_id, $rating)
    {
        $this->db->where('restaurant_id', $restaurant_id);
        $ratingPlus = $rating + 1;
        $this->db->where('(rating >= ' . $rating . ' AND rating < ' . $ratingPlus . ')');
        $this->db->group_by('entity_id');
        return $this->db->get('review')->num_rows();
    }
    // get restaurants with pagination
    public function getRestaurantsForOrder($limit, $offset, $resdish = NULL, $latitude = NULL, $longitude = NULL, $minimum_range = NULL, $maximum_range = NULL, $food_veg = NULL, $food_non_veg = NULL, $pagination = NULL)
    {
        $language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
        $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug");
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->join('restaurant_menu_item', 'restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1', 'left');
        if (!empty($resdish)) {
            $this->db->where("restaurant_menu_item.name LIKE '%" . $resdish . "%' OR restaurant.name LIKE '%" . $resdish . "%' OR address.address LIKE '%" . $resdish . "%' OR address.landmark LIKE '%" . $resdish . "%'");
        }
        if ($food_veg == 1 && $food_non_veg == 0) {
            $this->db->where('restaurant_menu_item.is_veg', 1);
        }
        if ($food_veg == 0 && $food_non_veg == 1) {
            $this->db->where('restaurant_menu_item.is_veg', 0);
        }
        $this->db->group_by('restaurant.content_id');
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
                $this->db->select("restaurant.entity_id as restaurant_id,restaurant.name,address.address,address.landmark,address.latitude,address.longitude,restaurant.image,restaurant.timings,restaurant.restaurant_slug,restaurant.content_id,restaurant.language_slug");
                $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
                $this->db->join('restaurant_menu_item', 'restaurant.entity_id = restaurant_menu_item.restaurant_id AND restaurant_menu_item.status = 1', 'left');
                $this->db->where_in('restaurant.content_id', $content_id);
                $this->db->where('restaurant.language_slug', $language_slug);
                if (!empty($resdish)) {
                    $this->db->where("restaurant_menu_item.name LIKE '%" . $resdish . "%' OR restaurant.name LIKE '%" . $resdish . "%' OR address.address LIKE '%" . $resdish . "%' OR address.landmark LIKE '%" . $resdish . "%'");
                }
                if ($food_veg == 1 && $food_non_veg == 0) {
                    $this->db->where('restaurant_menu_item.is_veg', 1);
                }
                if ($food_veg == 0 && $food_non_veg == 1) {
                    $this->db->where('restaurant_menu_item.is_veg', 0);
                }
                $this->db->where('restaurant.status', 1);
                $this->db->group_by('restaurant.content_id');
                $this->db->order_by('restaurant.entity_id');


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
        $finalArray = array();
        if (!empty($finalData) && !empty($latitude) && !empty($longitude)) {
            foreach ($finalData as $key => $value) {
                $latitude1 = $latitude;
                $longitude1 = $longitude;
                $latitude2 = $value['latitude'];
                $longitude2 = $value['longitude'];
                $earth_radius = 6371;
                $dLat = deg2rad($latitude2 - $latitude1);
                $dLon = deg2rad($longitude2 - $longitude1);

                $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
                $c = 2 * asin(sqrt($a));
                $d = intval($earth_radius * $c);
                $finalData[$key]['distance'] = $d;
                if (isset($minimum_range) && isset($maximum_range)) {
                    if ($minimum_range <= $d && $d <= $maximum_range) {
                        $finalArray[] = $value;
                    } else {
                        unset($finalData[$key]);
                    }
                } else {
                    if (MINIMUM_RANGE <= $d && $d <= MAXIMUM_RANGE) {
                        $finalArray[] = $value;
                    } else {
                        unset($finalData[$key]);
                    }
                }
            }
        }
        $finalRestaurants = $finalData;
        if (!empty($pagination)) {
            $finalRestaurants = array_slice($finalData, $offset, $limit);
        }
        return $finalRestaurants;
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
    // get total orders of a user
    public function getTotalOrders($user_id, $restaurant_id)
    {
        $this->db->select('entity_id');
        $this->db->where('user_id', $user_id);
        $this->db->where('restaurant_id', $restaurant_id);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "complete")');
        return $this->db->get('order_master')->num_rows();
    }
    // get total reviews of a user
    public function getTotalReviews($user_id, $restaurant_id)
    {
        $this->db->select('entity_id');
        $this->db->where('user_id', $user_id);
        $this->db->where('restaurant_id', $restaurant_id);
        return $this->db->get('review')->num_rows();
    }
}
