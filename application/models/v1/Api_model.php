<?php
class Api_model extends CI_Model
{
    var $pointOnVertex = true;

    function __construct()
    {
        parent::__construct();
        $this->load->model(ADMIN_URL . '/Sub_dashboard_model');
    }
    /***************** General API's Function *****************/
    public function getLanguages($current_lang)
    {
        $result = $this->db->select('*')->get_where('languages', array('language_slug' => $current_lang))->first_row();
        return $result;
    }
    public function getRewardValue($value)
    {
        $this->db->select('value');
        $system_value = $this->db->get_where('reward_point_setting', array('name' => $value))->first_row();
        return $system_value->value;
    }
    public function sendNotiRestaurant($res_id)
    {
        $this->db->select('device_id');
        $this->db->where('entity_id', $res_id);
        $data = $this->db->get('restaurant')->result();
        #prep the bundle
        $fields = array();
        $message = $this->lang->line('push_new_order');
        $fields['to'] = $data[0]->device_id; // only one user to send push notification
        $fields['notification'] = array('body'  => $message, 'sound' => 'default');
        $fields['data'] = array('screenType' => 'order');

        $headers = array(
            'Authorization: key=' . FCM_RES_KEY,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function getRecord($table, $fieldName, $where)
    {
        $this->db->where($fieldName, $where);
        return $this->db->get($table)->first_row();
    }
    public function getRecordAll($table, $fieldName, $where)
    {
        $this->db->where($fieldName, $where);
        $this->db->where('status', 1);
        return $this->db->get($table)->result();
    }

    public function getLimitations($name)
    {
        $this->db->select('entity_id,user_limitation');
        $this->db->where('name', $name);
        $this->db->where('coupon_type', 'user_registration');
        return $this->db->get('coupon')->first_row();
    }
    public function getLoginForProvider($providerType, $providerId)
    {
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.mobile_number,users.image,users.notification,users.sms_otp');
        $this->db->where('login_provider', $providerType);
        $this->db->where('login_provider_id', $providerId);
        $this->db->where('user_type', 'User');
        return $this->db->get('users')->first_row();
    }
    public function getpopupbanner()
    {
        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');
        $this->db->select('image,status');
        $this->db->where('status', 1);
        $this->db->where('end_date >', $currentTime);
        $this->db->where('start_date <', $currentTime);
        $images =  $this->db->get('popup_slider_image')->result();
        foreach ($images as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
        }
        return $images;
    }
    public function getLoginWithPhoneOnly($phone)
    {
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.mobile_number,users.image,users.notification,users.sms_otp');
        $this->db->where('mobile_number', $phone);
        $this->db->where('user_type', 'User');
        return $this->db->get('users')->first_row();
    }


    public function updateValue($table, $column, $userId)
    {
        $this->db->where('entity_id', $userId);
        $this->db->update($table, $column);
    }

    public function addData($Data)
    {
        $this->db->insert('order_status', $Data);
        return $this->db->insert_id();
    }
    //get record with multiple where
    public function getRecordMultipleWhere($table, $whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
    public function getAllRecordMultipleWhere($table, $whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->result();
    }
    //get home
    public function getHomeRestaurant($user_id, $latitude, $longitude, $searchItem, $food, $rating, $priceRange, $date, $distance, $offer, $language_slug, $count, $page_no = 1, $campaign_id, $zone_id, $search_restaurant_only = false, $isPopular = null)
    {
        if ($zone_id == null) {
            $zone_id = $this->checkGeoFenceForZone($latitude, $longitude);
        }

        $zone_id = $this->checkGeoFenceForZone($latitude, $longitude);

        if ($zone_id) {

            $result = new stdClass(); // equivalent to (object)[]

            $opsSetting = $this->getOperationSettings();

            $operation_timing = @unserialize(html_entity_decode($opsSetting['operation_timing']));

            if ($opsSetting['operation_on_off'] == 1) {

                $getResIds = $this->getRestaurantIdsInRadius($zone_id, $latitude, $longitude);
                $count_of_res = count($getResIds);

                $a = new DateTime();
                $currentTime = $a->format('Y-m-d H:i:s');

                // $specialStartTime = new DateTime("17:00");
                // $specialEndTime = new DateTime("19:00");

                $this->db->select("res.restaurant_slug, res.delivery_time,res.likes,res.content_id,res.is_popular,res.entity_id as restuarant_id,res.name,res.timings,res.break_timing,res.image,res.phone_number,res.cover_image,res.price_range,res_map.coupon_id as coupons,address.address,address.landmark,AVG (review.rating) as rating, (6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code");
                //$this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,res.cover_image,res.price_range,res_map.coupon_id as coupons,address.address,address.landmark,AVG (review.rating) as rating, currencies.currency_symbol,currencies.currency_code");
                $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
                $this->db->join('review', 'res.entity_id = review.restaurant_id', 'left');
                $this->db->join('currencies', 'res.currency_id = currencies.currency_id', 'left');
                $this->db->join('restaurant_menu_item as menu', 'res.entity_id = menu.restaurant_id', 'left');
                $this->db->join('coupon_restaurant_map as res_map', 'res.entity_id = res_map.restaurant_id', 'left');
                $this->db->join('coupon', 'coupon.entity_id = res_map.coupon_id', 'left');
                if ($campaign_id) {
                    $this->db->join('campaign_restaurant_map as camp_res_map', 'res.entity_id = camp_res_map.restaurant_id', 'left');
                    $this->db->join('campaign', 'campaign.entity_id = camp_res_map.campaign_id', 'left');
                }
                $this->db->where('res.status', 1);
                $this->db->where_in('res.entity_id', ($count_of_res > 0 ? array_column($getResIds, 'restaurant_id') : 0));
                //$this->db->where('res.timings');

                // if ($distance) {
                //     $this->db->having('distance <=', $distance);
                // } else {
                //     $this->db->having('distance <', NEAR_KM);
                // }

                if ($offer) {

                    foreach ($offer as $key => $value) {
                        if ($value == "discount") {
                            //$this->db->distinct('res_map.restaurant_id');
                            //$this->db->from ('coupon_restaurant_map as res_map');
                            // $this->db->join('coupon_restaurant_map as res_map', 'res.entity_id = res_map.restaurant_id');
                            // $this->db->join('coupon', 'coupon.entity_id = res_map.coupon_id');
                            $array = ['coupon.start_date <=' => $date, 'coupon.end_date >=' => $date];
                            $this->db->where($array);
                        }
                    }
                }

                if ($searchItem) {
                    //$this->db->join('restaurant_menu_item as menu', 'res.entity_id = menu.restaurant_id', 'left');
                    $this->db->join('category', 'menu.category_id = category.entity_id', 'left');
                    if ($search_restaurant_only) {
                        $where = "(res.name like '%" . addslashes($searchItem) . "%')";
                    } else {
                        $where = "(menu.name like '%" . addslashes($searchItem) . "%' OR res.name like '%" . addslashes($searchItem) . "%' OR category.name like '%" . addslashes($searchItem) . "%')";
                    }
                    $this->db->where($where);
                    $this->db->where('menu.status', 1);
                    $this->db->where('category.status', 1);
                }
                if ($food != '') {


                    //$this->db->join('menu', 'res.entity_id = menu.restaurant_id');

                    // $this->db->where('res.is_veg',$food);
                    // $this->db->or_where('res.is_veg',NULL);

                    if ($food == 1) {
                        $this->db->where('res.is_veg != 0');
                        // $this->db->where('res.is_veg', NULL);
                    }

                    if ($food == 2) {
                        //$both = [0,2];
                        // $this->db->whereIn('res.is_veg', $both);

                        $this->db->where('res.is_veg != 1');
                    }
                }

                if ($priceRange) {

                    //$this->db->join('menu', 'res.entity_id = menu.restaurant_id');
                    $this->db->where('res.price_range', $priceRange);
                }

                if ($rating) {
                    $this->db->having('rating <=', $rating);
                }

                if ($campaign_id) {
                    $this->db->where('campaign.entity_id', $campaign_id);
                }

                if ($isPopular) {
                    $this->db->where('res.is_popular', 1);
                }

                $this->db->where('res.language_slug', $language_slug);
                $this->db->group_by('res.entity_id');
                //$this->db->order_by('res.entity_id','DESC');

                if ($count) {
                    if ($page_no) {
                        $this->db->limit($count, $page_no * $count);
                    } else {
                        $this->db->limit($count);
                    }
                }
                $this->db->order_by('res.sort_value');
                // $this->db->limit($count,$page_no*$count);
                // $this->db->limit(1);
                $result =  $this->db->get('restaurant as res')->result();
                foreach ($result as $key => $value) {
                    $this->db->select('distinct(category_id)');
                    $this->db->where('restaurant_id', $value->restuarant_id);
                    $this->db->where('status', 1);
                    $result1 =  $this->db->get('restaurant_menu_item')->result();
                    $value->res_tag = $result1;
                    $break_timing = $value->break_timing;
                    $timing = $value->timings;
                    if ($timing) {
                        $timing =  unserialize(html_entity_decode($timing));
                        $newTimingArr = array();
                        $day = date("l");
                        $count = 0;
                        loop:
                        foreach ($timing as $keys => $values) {
                            if ($keys == strtolower($day)) {

                                $count++;


                                if (empty($values['open']) && empty($values['close'])) {
                                    if ($count == 8) {
                                        break;
                                    } else if ($day == "Sunday") {
                                        $day = date("l", strtotime($day . "+1 days"));
                                        goto loop;
                                    } else {
                                        $day = date("l", strtotime($day . "+1 days"));
                                    }
                                    // $newTimingArr[strtolower($day)]['open'] =  date('g:i A', strtotime($values['open']));
                                } else if ((date('H:i') < date('H:i', strtotime($values['open']))) || date(DATE_ATOM) < date(DATE_ATOM, strtotime($day . $values['open']))) {
                                    $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                    $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                    $newTimingArr[strtolower($day)]['off'] = 'close'; //(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                    $newTimingArr[strtolower($day)]['closing'] = 'close'; //(!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                                } else if (date('H:i') > date('H:i', strtotime($values['close']))) {
                                    $day = date("l", strtotime($day . "+1 days"));
                                    goto loop;
                                } else {

                                    if (date("l") < $day) {
                                        $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                        $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                        $newTimingArr[strtolower($day)]['off'] = 'close'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                        $newTimingArr[strtolower($day)]['closing'] = 'close'; //(date("l") > $day) ? 'close':'open';
                                    } else {
                                        $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                        $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                        $newTimingArr[strtolower($day)]['off'] = 'open'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                        $newTimingArr[strtolower($day)]['closing'] = 'open'; //(date("l") > $day) ? 'close':'open';

                                    }
                                }


                                // if (($specialStartTime->diff(new DateTime)->format('%R') == '+') && ($specialEndTime->diff(new DateTime)->format('%R') == '-')) {
                                //     $newTimingArr[strtolower($day)]['off'] = 'close'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                //     $newTimingArr[strtolower($day)]['closing'] = 'close'; //(date("l") > $day) ? 'close':'open';
                                // }

                                if ($operation_timing && $operation_timing != '') {
                                    $op_time_count = 0;
                                    foreach ($operation_timing as $ot) {
                                        if ($ot['on'] == 1) {
                                            $opsStartTime = new DateTime($ot['open']);
                                            $opsCloseTime = new DateTime($ot['close']);

                                            if (!(($opsStartTime->diff(new DateTime)->format('%R') == '+') &&
                                                ($opsCloseTime->diff(new DateTime)->format('%R') == '-'))) {
                                                $newTimingArr[strtolower($day)]['off'] = 'close';
                                                $newTimingArr[strtolower($day)]['closing'] = 'close';
                                            } else {
                                                $op_time_count++;
                                            }

                                            if ($op_time_count > 0) {
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }



                    if ($user_id) {
                        $checkRecord = $this->getRecord('user_favourite_restaurants', 'user_id', $user_id);

                        if ($checkRecord) {

                            $favourite_restaurants = unserialize($checkRecord->favourite_restaurants);

                            if (($key = array_search($value->restuarant_id, $favourite_restaurants)) !== false) {
                                $value->favourite_restaurant = 1;
                            } else {
                                $value->favourite_restaurant = 0;
                            }
                        } else {
                            $value->favourite_restaurant = 0;
                        }
                    } else {
                        $value->favourite_restaurant = 0;
                    }

                    // no of orders
                    $this->db->where('restaurant_id', $value->restuarant_id);
                    $this->db->where('order_status', 'delivered');
                    $no_of_orders = $this->db->get('order_master')->num_rows();

                    $this->db->select('coupon.name,coupon.description,res_map.coupon_id');
                    $this->db->join('coupon_restaurant_map as res_map', 'coupon.entity_id = res_map.coupon_id');

                    $this->db->where('res_map.restaurant_id', $value->restuarant_id);
                    $this->db->where('coupon.status', 1);
                    $this->db->where('coupon.coupon_type !=', 'selected_user');
                    $this->db->where('coupon.source', NULL);
                    $this->db->order_by('coupon.entity_id', 'DESC');
                    $this->db->limit(2);
                    $activeDates = ['start_date <=' => $currentTime, 'end_date >=' => $currentTime];
                    $this->db->where($activeDates);
                    $couponDetails = $this->db->get('coupon')->result_array();

                    if ($campaign_id) {
                        $this->db->select('campaign.name,campaign.description,camp_res_map.campaign_id');
                        $this->db->join('campaign_restaurant_map as camp_res_map', 'campaign.entity_id = camp_res_map.campaign_id');

                        $this->db->where('camp_res_map.restaurant_id', $value->restuarant_id);
                        $dates = ['start_date <=' => $currentTime, 'end_date >=' => $currentTime];
                        $this->db->where($dates);
                        $this->db->group_by("campaign.entity_id");
                        $this->db->limit(1);
                        $campaignDetails = $this->db->get('campaign')->result_array();
                        $value->campaign = $campaignDetails;
                    }

                    $value->coupons = $couponDetails;
                    $value->timings = $newTimingArr[strtolower($day)];
                    $value->image = ($value->image) ? image_url . $value->image : '';
                    $value->cover_image = ($value->cover_image) ? image_url . $value->cover_image : '';
                    $value->rating = ($value->rating) ? number_format((float)$value->rating, 1, '.', '') : null;
                    $value->zone_id = $zone_id;
                    $value->no_of_orders = $no_of_orders;
                    $value->res_count = $count_of_res;
                }
                // $lenght = count($result);
                // $final = array();

                //   foreach ($ids as $key => $value) {
                //     # code...
                //     $i = 0;
                //     while ($value != $result[$i]->restuarant_id) {
                //         if ($i == $lenght) {
                //             break;
                //         } else {
                //             $i++;
                //         }
                //     }

                //     if($result[$i] != null)
                //     {
                //         $final[$key] = $result[$i];
                //         unset($result[$i]);
                //     }

                // }
                // $final = array_values($final);
                // return $final;
                return $result;
            } else {
                return $result;
            }
        } else {
            return $zone_id;
        }
    }

    //get restaurant timing
    public function getTimings($resId, $openValue)
    {
        $currentTime = new DateTime();
        $timeStepInMinute = 15;
        $array = ['entity_id' => $resId];
        $this->db->select('timings, break_timing');
        $this->db->where($array);
        $result = $this->db->get('restaurant')->result();


        $opsSetting = $this->getOperationSettings();

        $operation_timing = @unserialize(html_entity_decode($opsSetting['operation_timing']));

        foreach ($result as $key => $value) {
            $day = date("l");
            $tommorow = date("l", strtotime($day . "+1 days"));
            $dayAfterTommorow = date("l", strtotime($day . "+2 days"));
            $timing = $value->timings;
            $break_timing = $value->break_timing;
            $newTimingArr = array();

            if ($timing) {
                $timing =  unserialize(html_entity_decode($timing));

                foreach ($timing as $keys => $values) {

                    if ((strtolower($day) == $keys) && empty($values['open']) && ($openValue == true)) {
                        $newTimingArr['Open'] = false;
                        $newTimingArr[strtoupper($keys)] = null;
                        $result = $newTimingArr['Open'];
                        return $result;
                    } else if (empty($values['open'])) {
                        $newTimingArr[strtoupper($keys)] = null;
                    } else {
                        $timeStepArrayRes = $this->hoursRange(
                            strtotime(date(('H:i'), strtotime($this->ceilTimeToNextStoppage($values['open'], $timeStepInMinute)))),
                            strtotime(date(('H:i'), strtotime($this->ceilTimeToNextStoppage($values['close'], $timeStepInMinute)))),
                            60 * $timeStepInMinute,
                            'h:i A',
                            (strtolower($day) == $keys ? $currentTime : '')
                        );
                    }



                    if ($operation_timing && !empty($operation_timing)) {

                        $ot1StepTime = array();
                        $ot2StepTime = array();
                        $ot1 = $operation_timing['time1'];
                        $ot2 = $operation_timing['time2'];
                        if ($ot1['on'] == 1 && !empty($ot1['open']) && !empty($ot1['close'])) {
                            $opsOpenTime = $ot1['open'];
                            $opsCloseTime = $ot1['close'];

                            $ot1StepTime = $this->hoursRange(strtotime($this->ceilTimeToNextStoppage($opsOpenTime, $timeStepInMinute)), strtotime($this->ceilTimeToNextStoppage($opsCloseTime, $timeStepInMinute)), 60 * $timeStepInMinute, 'h:i A');
                        }

                        if ($ot2['on'] == 1 && !empty($ot2['open']) && !empty($ot2['close'])) {
                            $opsOpenTime = $ot2['open'];
                            $opsCloseTime = $ot2['close'];

                            $ot2StepTime = $this->hoursRange(strtotime($this->ceilTimeToNextStoppage($opsOpenTime, $timeStepInMinute)), strtotime($this->ceilTimeToNextStoppage($opsCloseTime, $timeStepInMinute)), 60 * $timeStepInMinute, 'h:i A');
                        }

                        if (($ot1['on'] == 1 && !empty($ot1['open']) && !empty($ot1['close'])) ||
                            ($ot2['on'] == 1 && !empty($ot2['open']) && !empty($ot2['close']))
                        ) {
                            $new_op_step_time = array_merge($ot1StepTime, $ot2StepTime);

                            foreach ($timeStepArrayRes as $k => $v) {
                                $not_count = 0;
                                foreach ($new_op_step_time as $key => $value) {
                                    if ($v['label'] == $value['label']) {
                                        $not_count++;
                                    }
                                }
                                if ($not_count == 0) {
                                    unset($timeStepArrayRes[$k]);
                                }
                            }
                        }
                    }


                    if ($values['off'] == 0 || empty($values['open']) || empty($values['close'])) {
                        $newTimingArr[strtoupper($keys)] = array();
                    } else {
                        array_shift($timeStepArrayRes);
                        array_pop($timeStepArrayRes);
                        // if (count($timeStepArrayRes) == 1) {
                        //     $onlySlotValue = $timeStepArrayRes[0]['value'];
                        //     $onlySlotString = strtotime($onlySlotValue);
                        //     $plus5min = $onlySlotString + (60 * 5);
                        //     $minus5min = $onlySlotString - (60 * 5);


                        //     unset($timeStepArrayRes[0]);

                        //     $timeStepArrayRes[0] = array(
                        //         "label"     => date("g:i A", $minus5min),
                        //         "value"     => date("H:i", $minus5min),
                        //     );

                        //     $timeStepArrayRes[1] = array(
                        //         "label"     => date("g:i A", $onlySlotString),
                        //         "value"     => date("H:i", $onlySlotString),
                        //     );
                        // }
                        $newTimingArr[strtoupper($keys)] = $timeStepArrayRes;
                    }
                }
            }
        }

        $threeDayTiming[0] = array();
        $threeDayTiming[0]['Day'] = strtoupper($day);
        $threeDayTiming[0]['time_array'] = $newTimingArr[strtoupper($day)];

        $threeDayTiming[1] = array();
        $threeDayTiming[1]['Day'] = strtoupper($tommorow);
        $threeDayTiming[1]['time_array'] = $newTimingArr[strtoupper($tommorow)];

        $threeDayTiming[2] = array();
        $threeDayTiming[2]['Day'] = strtoupper($dayAfterTommorow);
        $threeDayTiming[2]['time_array'] = $newTimingArr[strtoupper($dayAfterTommorow)];

        return $threeDayTiming;
    }

    // https://stackoverflow.com/a/21896310
    public function hoursRange($lower = 0, $upper = 86400, $step = 3600, $format = '', $today = null)
    {
        $times = array();
        $count = 0;

        if (empty($format)) {
            $format = 'g:i a';
        }

        foreach (range($lower, $upper, $step) as $increment) {
            $increment = date('H:i', $increment);

            list($hour, $minutes) = explode(':', $increment);

            $date = new DateTime($hour . ':' . $minutes);


            $utcdiff = $date->format('O');

            $timeArea = $utcdiff == "+0600" ? $timeArea = "Bangladesh Standard Time" : $utcdiff;

            if (!($today && ($date->diff($today)->format('%R') == '+'))) {
                $times[$count]['label'] = $date->format($format);
                $times[$count]['value'] = $date->format('H:i');
                $count++;
            }
        }

        return $times;
    }

    public function ceilTimeToNextStoppage($time, $step)
    {
        $inputTime = new DateTime($time);
        if ($inputTime->diff(new DateTime("23:45"))->format("%R") == "-") {
            $time = "23:45";
        }
        if (new DateTime());
        $timestring = strtotime(date($time));
        $ceiled = ceil($timestring / ($step * 60)) * ($step * 60);

        return date('H:i', $ceiled);
    }


    //get banner
    // public function getbanner()
    // {
    //     $this->db->select('image,restaurant_id , action_type,url,item_id');
    //     $images =  $this->db->get('slider_image')->result();
    //     $actionData = array();
    //     foreach ($images as $key => $value) {
    //         $value->image = ($value->image) ? image_url . $value->image : '';

    //         if (empty($value->url)) {
    //             $value->url = null;
    //         }
    //         if ($value->item_id == 0) {
    //             $value->item_id = null;
    //         }
    //         if ($value->restaurant_id == 0) {
    //             $value->restaurant_id = null;
    //         }
    //         if ($value->action_type == 0) {
    //             $value->action_type = null;
    //         }
    //     }

    //     return $images;
    // }
    // public function getPopularRestaurant($zone_id)
    // {

    //     $restaurants = $this->getRestaurantIds($zone_id);
    //     $res_id = array_column($restaurants, 'restaurant_id');

    //     $this->db->select('slider_image.image,restaurant_id,res.name');
    //     $this->db->join('restaurant as res', 'res.entity_id = slider_image.restaurant_id', 'left');
    //     $this->db->where_in('restaurant_id', $res_id);
    //     $this->db->or_where('restaurant_id', 0);
    //     // //for eid
    //     // $this->db->order_by('desc');
    //     // $this->db->limit(1);

    //     $images =  $this->db->get('slider_image')->result();
    //     foreach ($images as $key => $value) {
    //         $value->image = ($value->image) ? image_url . $value->image : '';
    //         if (($value->restaurant_id) == 0) {
    //             $value->content_id = "0";
    //         } else {
    //             $data = $this->db->select('content_id')->get_where('restaurant', array('entity_id' => $value->restaurant_id, 'status' => 1))->first_row();

    //             $value->content_id = $data->content_id;
    //         }
    //     }
    //     return array_values($images);


    //     //  // //for eid
    //     //  $this->db->select('image,entity_id');
    //     //  $this->db->order_by('entity_id','desc');
    //     //  $this->db->limit(1);
    //     //  $images =  $this->db->get('slider_image')->result();

    //     //  foreach ($images as $key => $value) {
    //     //     $value->image = ($value->image) ? image_url . $value->image : '';
    //     // }

    //     //  return $images;
    // }

    public function getbanner($zone_id)
    {

        $restaurants = $this->getRestaurantIds($zone_id);
        $res_id = array_column($restaurants, 'restaurant_id');

        $this->db->select('slider_image.image,restaurant_id,res.name');
        $this->db->join('restaurant as res', 'res.entity_id = slider_image.restaurant_id', 'left');
        $this->db->where_in('restaurant_id', $res_id);
        $this->db->or_where('restaurant_id', 0);
        // //for eid
        // $this->db->order_by('desc');
        // $this->db->limit(1);

        $images =  $this->db->get('slider_image')->result();
        foreach ($images as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
            if (($value->restaurant_id) == 0) {
                $value->content_id = "0";
            } else {
                $data = $this->db->select('content_id')->get_where('restaurant', array('entity_id' => $value->restaurant_id, 'status' => 1))->first_row();

                $value->content_id = $data->content_id;
            }
        }
        return array_values($images);
    }

    public function getFeatureItems($restaurant)
    {
        $res_id[] = array();
        foreach ($restaurant as $key => $values) {

            $res_id[$key] = $values->restuarant_id;
        }


        $this->db->select(
            'feature.feature_id,
            feature.sort_value as sortValue,
            feature.description,
            feature.cover_image as coverImage,
            res.entity_id as restaurantId,
            res.content_id,
            res.name as restaurantName,
            res.image as restaurantImage,
            menu.entity_id as menu_id,
            menu.status,
            menu.name,
            menu.price,
            menu.menu_detail,
            menu.image,
            availability,
            c.name as category,
            c.entity_id as category_id,
            c.sort_value as sort_value,
            add_ons_master.add_ons_name,
            add_ons_master.add_ons_price,
            add_ons_category.name as addons_category,
            menu.check_add_ons,
            add_ons_category.entity_id as addons_category_id,
            add_ons_master.add_ons_id,
            add_ons_master.is_multiple,
            add_ons_master.variation_id,
            add_ons_master.has_variation,
            variations.variation_name,
            variations.variation_price'
        );
        $this->db->join('restaurant as res', 'res.entity_id = feature.restaurant_id', 'left');
        $this->db->join('restaurant_menu_item as menu', 'menu.entity_id = feature.menu_item_id', 'left');
        $this->db->join('category as c', 'menu.category_id = c.entity_id', 'left');
        $this->db->join('add_ons_master', 'menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1', 'left');
        $this->db->join('add_ons_category', 'add_ons_master.category_id = add_ons_category.entity_id', 'left');
        $this->db->join('variations', 'add_ons_master.variation_id = variations.entity_id', 'left');
        $this->db->where_in('res.entity_id', $res_id);
        $this->db->where('feature.status', 1);
        $this->db->where('menu.status', 1);

        $result = $this->db->get('feature_items as feature')->result();
        $result_copy = $result;

        foreach ($result_copy as $key => $value) {
            $menu_timing = $value->availability;

            if ($menu_timing && ($menu_timing != '' || $menu_timing != null)) {
                $unserialized_menu_timing = @unserialize($menu_timing);

                if ($unserialized_menu_timing && !empty($unserialized_menu_timing)) {
                    $break_count = 0;
                    foreach ($unserialized_menu_timing as $t_key => $t_value) {

                        if ($t_value['on'] != 0 && $t_value['open'] != '' && $t_value['close'] != '') {
                            $menuOpenTime = new DateTime($t_value['open']);
                            $menuCloseTime = new DateTime($t_value['close']);
                            if ((($menuOpenTime->diff(new DateTime)->format('%R') == '+') &&
                                ($menuCloseTime->diff(new DateTime)->format('%R') == '-'))) {
                                $break_count++;
                            }
                        }
                    }

                    if ($break_count == 0) {
                        unset($result_copy[$key]);
                    }
                }
            }
        }

        $result = array_values($result_copy);

        $menu = array();
        foreach ($result as $key => $value) {
            $offer_price = '';

            if (!isset($menu[$value->category_id])) {
                $menu[$value->category_id] = array();
                $menu[$value->category_id]['category_id'] = $value->category_id;
                $menu[$value->category_id]['category_name'] = $value->category;
                $menu[$value->category_id]['sort_value'] = $value->sort_value;
            }
            $image = ($value->image) ? image_url . $value->image : '';
            $total = 0;
            if ($value->check_add_ons == 1) {
                if (!isset($menu[$value->category_id]['items'][$value->menu_id])) {
                    $menu[$value->category_id]['items'][$value->menu_id] = array();
                    $menu[$value->category_id]['items'][$value->menu_id] = array(
                        'menu_id'       => $value->menu_id,
                        'restaurant_id' => $value->restaurantId,
                        'content_id'    => $value->content_id,
                        'name'          => $value->name,
                        'price'         => $value->price,
                        'offer_price'   => $offer_price,
                        'menu_detail'   => $value->menu_detail,
                        'image'         => $image,
                        'recipe_detail' => $value->recipe_detail,
                        'availability'  => $value->availability,
                        'is_veg'        => $value->is_veg,
                        'is_customize'  => $value->check_add_ons,
                        'is_deal'       => $value->is_deal,
                        'status'        => $value->status
                    );
                }

                if ($value->has_variation == 1) {
                    $menu[$value->category_id]['items'][$value->menu_id]['has_variation'] = $value->has_variation;

                    if (!isset($menu[$value->category_id]['items'][$value->menu_id]['variation_list'])) {
                        $i = 0;
                        $menu[$value->category_id]['items'][$value->menu_id]['variation_list'] = array(); //need clearence

                    }

                    $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$i] = array('variation_id' => $value->variation_id, 'variation_name' => $value->variation_name, 'variation_price' => $value->variation_price);

                    if (!isset($menu[$value->category_id]['items'][$value->menu_id]['variation_list']['addons_category_list'])) {
                        $menu[$value->category_id]['items'][$value->menu_id]['variation_list']['addons_category_list'] = array();
                        $menu[$value->category_id]['items'][$value->menu_id]['variation_list']['addons_category_list']['is_multiple'] = $value->is_multiple;
                    }
                    $menu[$value->category_id]['items'][$value->menu_id]['variation_list']['addons_category_list']['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name);
                } else {
                    $menu[$value->category_id]['items'][$value->menu_id]['has_variation'] = $value->has_variation;
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
                // if ($value->is_deal == 1) {
                //     if (!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'])) {
                //         $i = 0;
                //         $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'] = array();
                //         $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['is_multiple'] = $value->is_multiple;
                //     }
                //     $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list']['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name);
                //     $i++;
                // } else {
                //     if (!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id])) {
                //         $i = 0;
                //         $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                //         $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                //         $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                //         $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                //     }
                //     $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name, 'add_ons_price' => $value->add_ons_price);
                //     $i++;
                // }
            } else {
                $menu[$value->category_id]['items'][]  = array('menu_id' => $value->menu_id, 'restaurant_id' => $value->restaurantId, 'content_id' => $value->content_id, 'name' => $value->name, 'price' => $value->price, 'offer_price' => $offer_price, 'menu_detail' => $value->menu_detail, 'image' => $image, 'recipe_detail' => $value->recipe_detail, 'availability' => $value->availability, 'is_veg' => $value->is_veg, 'is_customize' => $value->check_add_ons, 'is_deal' => $value->is_deal, 'status' => $value->status);
            }
        }
        $finalArray = array();
        $final = array();
        $semifinal = array();
        foreach ($menu as $nm => $va) {
            $final = array();
            foreach ($va['items'] as $kk => $items) {
                if (!empty($items['addons_category_list'])) {
                    $semifinal = array();
                    foreach ($items['addons_category_list'] as $addons_cat_list) {
                        array_push($semifinal, $addons_cat_list);
                    }
                    $items['addons_category_list'] = $semifinal;
                }
                array_push($final, $items);
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }
        return $finalArray;
    }

    public function getFeatureItemsforDetails($restaurant_id)
    {
        // $this->db->select('feature_id');
        // $this->db->from('feature_items');
        //
        $this->db->select('feature.feature_id,feature.sort_value as sortValue,feature.description,feature.cover_image as coverImage, res.entity_id as restaurantId,res.content_id, res.name as restaurantName, res.image as restaurantImage, resmenu.entity_id as itemId,resmenu.name as item,resmenu.image as itemImage,resmenu.price');
        $this->db->join('restaurant as res', 'res.entity_id = feature.restaurant_id', 'left');
        $this->db->join('restaurant_menu_item as resmenu', 'resmenu.entity_id = feature.menu_item_id', 'left');
        $this->db->where('feature.restaurant_id', $restaurant_id);
        $this->db->where('feature.status', 1);

        $data = $this->db->get('feature_items as feature')->result();

        //     $value->restaurantImage = $value->restaurantImage ? image_url . $value->restaurantImage : '';
        foreach ($data as $key => $value) {
            $value->coverImage = $value->coverImage ? image_url . $value->coverImage : '';
            $value->restaurantImage = $value->restaurantImage ? image_url . $value->restaurantImage : '';
            $value->itemImage = $value->itemImage ? image_url . $value->itemImage : '';
        }

        return $data;
    }

    public function fetchCategory()
    {
        $this->db->select('entity_id,name,image');
        $this->db->where('status', 1);
        $data = $this->db->get('category')->result();
        // $this->response(['category' => $data, 'status'=>1,'message'=>$this->lang->line("records_found")]);
        foreach ($data as $key => $value) {
            $value->image = $value->image ? image_url . $value->image : '';
        }

        return $data;
    }
    //get home page category
    public function getcategory($language_slug, $zone_id)
    {
        // $this->db->select('category.content_id,category.entity_id as category_id, category.name,category.image,category.sort_value');
        // $this->db->where('category.language_slug', $language_slug);
        // $this->db->order_by('category.entity_id', 'desc');
        // //$this->db->limit(4, 0);
        // $result =  $this->db->get('category')->result();
        // foreach ($result as $key => $value) {
        //     $value->image = ($value->image) ? image_url . $value->image : '';
        // }
        // return $result;

        //$res_id = array_values($res_id);
        // $this->db->select('category.content_id,category.entity_id as category_id, category.name,category.image');
        $restaurants = $this->getRestaurantIds($zone_id);
        $res_id = array_column($restaurants, 'restaurant_id');

        $this->db->select('cat.content_id,cat.entity_id as category_id, cat.name,cat.image,cat.sort_value');
        $this->db->join('restaurant_menu_item as menu', 'menu.category_id = cat.entity_id', 'left');
        $this->db->where('cat.language_slug', $language_slug);
        $this->db->where_in('menu.restaurant_id', $res_id);
        $this->db->where_in('menu.status', 1);
        $this->db->where('cat.status', 1);
        $this->db->order_by('cat.entity_id', 'desc');
        $this->db->group_by('cat.entity_id');
        //$this->db->limit(4, 0);
        $result =  $this->db->get('category as cat')->result();
        foreach ($result as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
        }
        return $result;
    }
    //get restaurant
    public function getRestaurantDetail($content_id, $language_slug, $date, $entity_id = null, $zone_id = null, $user_id = null, $latitude = null, $longitude = null)
    {
        $qu = ($longitude && $latitude) ?
            "(6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance" : "";
        $this->db->select(
            "
        res.delivery_time,
        res.content_id,
        res.entity_id as restuarant_id,
        res.name,
        res.phone_number,
        res.timings,
        res.image,
        res.cover_image,
        res.price_range,
        address.address,
        address.landmark,
        AVG(review.rating) as rating,
        currencies.currency_symbol,
        currencies.currency_code," . $qu
        );
        $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
        $this->db->join('review', 'res.entity_id = review.restaurant_id', 'left');
        $this->db->join('currencies', 'res.currency_id = currencies.currency_id', 'left');

        if ($zone_id) {
            $this->db->join('zone_res_map', 'zone_res_map.restaurant_id = res.entity_id', 'left');
            $this->db->where('zone_res_map.zone_id', $zone_id);
        }

        if (!$entity_id) {
            $this->db->where('res.content_id', $content_id);
        } else {
            $this->db->where('res.entity_id', $entity_id);
        }
        $this->db->where('res.language_slug', $language_slug);
        $this->db->group_by('res.entity_id');
        $result =  $this->db->get('restaurant as res')->result();
        foreach ($result as $key => $value) {
            $timing = $value->timings;
            if ($timing) {
                $timing =  unserialize(html_entity_decode($timing));
                $newTimingArr = array();
                $day = date("l");
                loop:
                foreach ($timing as $keys => $values) {

                    if ($keys == strtolower($day)) {


                        if (empty($values['open']) && empty($values['close'])) {
                            if ($day == "Sunday") {
                                $day = date("l", strtotime($day . "+1 days"));

                                goto loop;
                                // $newTimingArr[strtolower($day)]['open'] = date(DATE_ATOM, strtotime($day . $values['open']));
                                // $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                // $newTimingArr[strtolower($day)]['off'] = 'close'; //(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                // $newTimingArr[strtolower($day)]['closing'] = 'close';

                            } else {
                                $day = date("l", strtotime($day . "+1 days"));
                            }

                            // $newTimingArr[strtolower($day)]['open'] =  date('g:i A', strtotime($values['open']));
                        } else if ((date('H:i') < date('H:i', strtotime($values['open']))) || date(DATE_ATOM) < date(DATE_ATOM, strtotime($day . $values['open']))) {
                            $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                            $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                            $newTimingArr[strtolower($day)]['off'] = 'close'; //(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                            $newTimingArr[strtolower($day)]['closing'] = 'close'; //(!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                        } else if (date('H:i') > date('H:i', strtotime($values['close']))) {
                            $day = date("l", strtotime($day . "+1 days"));
                            // $newTimingArr[strtolower($day)]['open'] =  date('g:i A', strtotime($values['open']));
                            // $newTimingArr[strtolower($day)]['close'] = date('g:i A', strtotime($values['close']));
                            // $newTimingArr[strtolower($day)]['off'] = 'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                            // $newTimingArr[strtolower($day)]['closing'] = 'close';//(!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                        } else {

                            if (date("l") < $day) {
                                $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                $newTimingArr[strtolower($day)]['off'] = 'close'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                $newTimingArr[strtolower($day)]['closing'] = 'close'; //(date("l") > $day) ? 'close':'open';
                            } else {
                                $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                $newTimingArr[strtolower($day)]['off'] = 'open'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                $newTimingArr[strtolower($day)]['closing'] = 'open'; //(date("l") > $day) ? 'close':'open';

                            }
                        }
                    }
                }
            }

            $this->db->select('coupon.name,coupon.description,res_map.coupon_id');
            $this->db->join('coupon_restaurant_map as res_map', 'coupon.entity_id = res_map.coupon_id');
            $this->db->where('coupon.coupon_type!=', 'selected_user');
            $this->db->where('coupon.source', NULL);
            $this->db->where('res_map.restaurant_id', $value->restuarant_id);
            $activeDates = ['start_date <=' => $date, 'end_date >=' => $date];
            $this->db->where($activeDates);
            $this->db->where('coupon.status', 1);
            $couponDetails = $this->db->get('coupon')->result_array();

            $value->coupons = $couponDetails;

            $value->timings = $newTimingArr[strtolower($day)];

            $value->image = ($value->image) ? image_url . $value->image : '';
            $value->cover_image = ($value->cover_image) ? image_url . $value->cover_image : '';
            $value->rating = ($value->rating) ? number_format((float)$value->rating, 1, '.', '') : null;
            $value->applink = DEEP_LINK_BASE_URL . generateDeepLink('restaurant', $value->restuarant_id);

            if ($user_id) {
                $checkRecord = $this->getRecord('user_favourite_restaurants', 'user_id', $user_id);

                if ($checkRecord) {

                    $favourite_restaurants = unserialize($checkRecord->favourite_restaurants);

                    if (($key = array_search($value->restuarant_id, $favourite_restaurants)) !== false) {
                        $value->favourite_restaurant = 1;
                    } else {
                        $value->favourite_restaurant = 0;
                    }
                } else {
                    $value->favourite_restaurant = 0;
                }
            } else {
                $value->favourite_restaurant = 0;
            }

            $this->db->where('restaurant_id', $value->restuarant_id);
            $this->db->where('order_status', 'delivered');
            $no_of_orders = $this->db->get('order_master')->num_rows();

            $value->no_of_orders = $no_of_orders;
        }
        return $result;
    }
    //get populer item
    public function item_image($restaurant_id, $language_slug)
    {
        $this->db->select('image');
        $this->db->where('popular_item !=', 1);
        $this->db->where('image !=', '');
        if ($restaurant_id) {
            $this->db->where('restaurant_id', $restaurant_id);
        }
        $this->db->where('language_slug', $language_slug);
        $this->db->limit(10, 0);
        $result = $this->db->get('restaurant_menu_item')->result();
        foreach ($result as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
        }
        return $result;
    }
    //get items
    public function getMenuItem($restaurant_id, $food, $price, $language_slug, $popular)
    {
        // $ItemDiscount = $this->getItemDiscount(array('status' => 1, 'coupon_type' => 'discount_on_items'));
        // $couponAmount = $ItemDiscount['couponAmount'];
        // $ItemDiscount = (!empty($ItemDiscount['itemDetail'])) ? array_column($ItemDiscount['itemDetail'], 'item_id') : array();

        $this->db->select(
            'menu.is_deal,
            menu.entity_id as menu_id,
            menu.status,
            menu.name,
            menu.price,
            menu.menu_detail,
            menu.image,
            menu.is_veg,
            menu.recipe_detail,
            availability,
            c.name as category,
            c.entity_id as category_id,
            c.sort_value as sort_value,
            add_ons_master.add_ons_name,
            add_ons_master.add_ons_price,
            add_ons_category.name as addons_category,
            menu.check_add_ons,
            add_ons_category.entity_id as addons_category_id,
            add_ons_master.add_ons_id,
            add_ons_master.is_multiple,
            add_ons_master.variation_id,
            add_ons_master.has_variation,
            add_ons_master.max_choice,
            variations.variation_name,
            variations.variation_add_on,
            variations.variation_price'

        );
        $this->db->join('category as c', 'menu.category_id = c.entity_id', 'left');
        $this->db->join('add_ons_master', 'menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1', 'left');
        //$this->db->join('add_ons_category', 'add_ons_master.category_id = add_ons_category.entity_id', 'left');
        $this->db->join('add_ons_category', 'add_ons_master.category_id = add_ons_category.entity_id and add_ons_category.status = 1', 'left');
        $this->db->join('variations', 'add_ons_master.variation_id = variations.entity_id', 'left');
        // $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
        $this->db->where('menu.restaurant_id', $restaurant_id);
        $this->db->where('menu.status', 1);
        $this->db->where('c.status', 1);
        if ($popular == 1) {
            $this->db->where('popular_item', 1);
            $this->db->where('menu.image !=', '');
        } else {
            if ($price == 1) {
                $this->db->order_by('menu.price', 'desc');
            } else {
                $this->db->order_by('menu.price', 'asc');
            }
            if ($food != '') {
                $this->db->where('menu.is_veg', $food);
            }
        }
        $this->db->where('menu.language_slug', $language_slug);
        $result = $this->db->get('restaurant_menu_item as menu')->result();

        $result_copy = $result;

        foreach ($result_copy as $key => $value) {
            $menu_timing = $value->availability;

            if ($menu_timing && ($menu_timing != '' || $menu_timing != null)) {
                $unserialized_menu_timing = @unserialize($menu_timing);

                if ($unserialized_menu_timing && !empty($unserialized_menu_timing)) {
                    $break_count = 0;
                    foreach ($unserialized_menu_timing as $t_key => $t_value) {

                        if ($t_value['on'] != 0 && $t_value['open'] != '' && $t_value['close'] != '') {
                            $menuOpenTime = new DateTime($t_value['open']);
                            $menuCloseTime = new DateTime($t_value['close']);
                            if ((($menuOpenTime->diff(new DateTime)->format('%R') == '+') &&
                                ($menuCloseTime->diff(new DateTime)->format('%R') == '-'))) {
                                $break_count++;
                            }
                        }
                    }

                    if ($break_count == 0) {
                        unset($result_copy[$key]);
                    }
                }
            }
        }

        $result = array_values($result_copy);

        $menu = array();
        foreach ($result as $key => $value) {

            $offer_price = '';
            // if (in_array($value->menu_id, $ItemDiscount)) {
            //     if (!empty($couponAmount)) {
            //         if ($couponAmount[0]['max_amount'] < $value->price) {
            //             if ($couponAmount[0]['amount_type'] == 'Percentage') {
            //                 $offer_price = $value->price - round(($value->price * $couponAmount[0]['amount']) / 100);
            //             } else if ($couponAmount[0]['amount_type'] == 'Amount') {
            //                 $offer_price = $value->price - $couponAmount[0]['amount'];
            //             }
            //         }
            //     }
            // }
            // $offer_price = ($offer_price) ? $offer_price : '';
            if (!isset($menu[$value->category_id])) {
                $menu[$value->category_id] = array();
                $menu[$value->category_id]['category_id'] = $value->category_id;
                $menu[$value->category_id]['category_name'] = $value->category;
                $menu[$value->category_id]['sort_value'] = $value->sort_value;
            }
            $image = ($value->image && $value->image != '') ? image_url . $value->image : image_url . 'menu/400a67c7f2b91270860c732212131af9.jpg';
            $total = 0;
            if ($value->check_add_ons == 1) {
                if (!isset($menu[$value->category_id]['items'][$value->menu_id])) {
                    $menu[$value->category_id]['items'][$value->menu_id] = array();
                    $menu[$value->category_id]['items'][$value->menu_id] = array(
                        'menu_id'       => $value->menu_id,
                        'name'          => $value->name,
                        'price'         => $value->price,
                        'offer_price'   => $offer_price,
                        'menu_detail'   => $value->menu_detail,
                        'image'         => $image,
                        'recipe_detail' => $value->recipe_detail,
                        // 'availability'  => $value->availability,
                        'is_veg'        => $value->is_veg,
                        'is_customize'  => $value->check_add_ons,
                        'is_deal'       => $value->is_deal,
                        'has_variation' => $value->has_variation,
                        'status'        => $value->status
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

                    $menu[$value->category_id]['items'][$value->menu_id]['has_variation'] = 0;
                    if (!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]) && $value->addons_category) {
                        $i = 0;
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
                        $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
                    }
                    ($value->addons_category) ? $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name, 'add_ons_price' => $value->add_ons_price) : '';
                    $i++;
                }
            } else {
                $menu[$value->category_id]['items'][]  = array('menu_id' => $value->menu_id, 'name' => $value->name, 'price' => $value->price, 'offer_price' => $offer_price, 'menu_detail' => $value->menu_detail, 'image' => $image, 'recipe_detail' => $value->recipe_detail, 'availability' => $value->availability, 'is_veg' => $value->is_veg, 'is_customize' => $value->check_add_ons, 'is_deal' => $value->is_deal, 'status' => $value->status);
            }
        }

        // echo '<pre>';
        // print_r($menu);
        // exit();

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


                // if ($semifinal) {
                //     array_push($final, $items);
                // }


                // if ($items['is_customize'] == 0) {
                array_push($final, $items);
                // }
            }
            $va['items'] = $final;
            array_push($finalArray, $va);
        }
        return $finalArray;
    }
    //get resutarant review
    public function getRestaurantReview($restaurant_id)
    {
        $this->db->select("review.rating,review.review,users.first_name,users.last_name,users.image,review.created_date");
        $this->db->join('users', 'review.user_id = users.entity_id', 'left');
        $this->db->where('review.status', 1);
        $this->db->where('review.restaurant_id', $restaurant_id);
        $result =  $this->db->get('review')->result();

        foreach ($result as $key => $value) {
            $value->last_name = ($value->last_name) ? $value->last_name : '';
            $value->first_name = ($value->first_name) ? $value->first_name : '';
            $value->image = ($value->image) ? image_url . $value->image : '';
            $value->created_date = ($value->created_date) ? date("d-m-Y", strtotime($value->created_date)) : '';
        }
        return $result;
    }
    //get event restuarant
    public function getEventRestaurant($latitude, $longitude, $searchItem, $language_slug, $count, $page_no = 1)
    {
        if ($searchItem) {
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,address.address,address.landmark,address.city,address.zipcode,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code");
            $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
            $this->db->join('review', 'res.entity_id = review.restaurant_id', 'left');
            $this->db->join('currencies', 'res.currency_id = currencies.currency_id', 'left');
            $where = "(res.name like '%" . $searchItem . "%')";
            $this->db->where($where);
        } else {
            $this->db->select("res.content_id,res.entity_id as restuarant_id,res.name,res.timings,res.image,address.address,address.landmark,address.city,address.zipcode,AVG (review.rating) as rating, (6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance,currencies.currency_symbol,currencies.currency_code");
            $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
            $this->db->join('review', 'res.entity_id = review.restaurant_id', 'left');
            $this->db->join('currencies', 'res.currency_id = currencies.currency_id', 'left');
        }
        $this->db->where('res.language_slug', $language_slug);
        $this->db->limit($count, $page_no * $count);
        $this->db->group_by('res.entity_id');
        $result =  $this->db->get('restaurant as res')->result();
        foreach ($result as $key => $value) {
            $timing = $value->timings;
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
                        $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                    }
                }
            }
            $value->timings = $newTimingArr[strtolower($day)];
            $value->image = ($value->image) ? image_url . $value->image : '';
            $value->rating = ($value->rating) ? number_format((float)$value->rating, 1, '.', '') : null;
        }
        return $result;
    }
    // Login
    public function getLogin($phone, $password)
    {
        $enc_pass  = md5(SALT . $password);
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.mobile_number,users.image,users.notification,users.sms_otp');
        $this->db->where('mobile_number', $phone);
        $this->db->where('password', $enc_pass);
        $this->db->where('user_type', 'User');
        return $this->db->get('users')->first_row();
    }
    //get rating of user
    public function getRatings($userid)
    {
        $this->db->select('AVG(review.rating) as rating');
        $this->db->where('order_user_id', $userid);
        $this->db->group_by('review.order_user_id');
        return $this->db->get('review')->first_row();
    }
    // Update User
    public function updateUser($tableName, $data, $fieldName, $UserID)
    {
        $this->db->where($fieldName, $UserID);
        $this->db->update($tableName, $data);
    }
    // check token for every API Call
    public function checkToken($token, $userid)
    {
        return $this->db->get_where('users', array('mobile_number' => $token, ' entity_id' => $userid))->first_row();
    }
    // Common Add Records
    public function addRecord($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    // Common Add Records Batch
    public function addRecordBatch($table, $data)
    {
        return $this->db->insert_batch($table, $data);
    }
    public function deleteRecord($table, $fieldName, $where)
    {
        $this->db->where($fieldName, $where);
        return $this->db->delete($table);
    }
    public function checkEmailExist($emailID, $UserID)
    {
        $this->db->where('Email', $emailID);
        $this->db->where('UserID !=', $UserID);
        $this->db->where('deleteStatus', 0);
        return $this->db->get('users')->num_rows();
    }
    // get config
    public function getSystemOptoin($OptionSlug)
    {
        $this->db->select('OptionValue');
        $this->db->where('OptionSlug', $OptionSlug);
        return $this->db->get('system_option')->first_row();
    }
    //get record after registration
    public function getRegisterRecord($tblname, $UserID)
    {
        $this->db->select('entity_id,first_name,mobile_number');
        $this->db->where('entity_id', $UserID);
        return $this->db->get($tblname)->first_row();
    }
    //check email for user edit
    public function getExistingEmail($table, $fieldName, $where, $UserID)
    {
        $this->db->where($fieldName, $where);
        $this->db->where('UserID !=', $UserID);
        return $this->db->get($table)->first_row();
    }
    //get cms detail
    public function getCMSRecord($tblname, $cms_slug, $language_slug)
    {
        $this->db->select('content_id,entity_id,name,description');
        $this->db->where('CMSSlug', $cms_slug);
        $this->db->where('status', 1);
        $this->db->where('language_slug', $language_slug);
        return $this->db->get($tblname)->result();
    }
    //check booking availability
    public function getBookingAvailability($date, $people, $restaurant_id)
    {
        $date = date('Y-m-d H:i:s', strtotime($date));
        // $time = date('g:i A',strtotime($date));
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
            $this->db->select('SUM(no_of_people) as people');
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
    //get package
    public function getPackage($restaurant_id, $language_slug)
    {
        $this->db->select('entity_id as package_id,name,price,detail,availability');
        $this->db->where('restaurant_id', $restaurant_id);
        $this->db->where('language_slug', $language_slug);
        return $this->db->get('restaurant_package')->result();
    }
    //get event
    public function getBooking($user_id)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        //upcoming
        $this->db->select('event.entity_id as event_id,event.booking_date,event.no_of_people,event_detail.package_detail,event_detail.restaurant_detail,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('event_detail', 'event.entity_id = event_detail.event_id', 'left');
        $this->db->join('review', 'event.restaurant_id = review.restaurant_id', 'left');
        $this->db->join('restaurant', 'event.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('event.user_id', $user_id);
        $this->db->where('event.booking_date >', $currentDateTime);
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id', 'desc');
        $result = $this->db->get('event')->result();
        $upcoming = array();
        foreach ($result as $key => $value) {
            $package_detail = '';
            $restaurant_detail = '';
            if (!isset($value->event_id)) {
                $upcoming[$value->event_id] = array();
            }
            if (isset($value->event_id)) {
                $package_detail = unserialize($value->package_detail);
                $restaurant_detail = unserialize($value->restaurant_detail);
                $upcoming[$value->event_id]['entity_id'] =  $value->event_id;
                $upcoming[$value->event_id]['booking_date'] =  $value->booking_date;
                $upcoming[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $upcoming[$value->event_id]['currency_code'] =  $value->currency_code;
                $upcoming[$value->event_id]['currency_symbol'] =  $value->currency_symbol;

                $upcoming[$value->event_id]['package_name'] =  (!empty($package_detail)) ? $package_detail['package_name'] : '';
                $upcoming[$value->event_id]['package_detail'] = (!empty($package_detail)) ? $package_detail['package_detail'] : '';
                $upcoming[$value->event_id]['package_price'] = (!empty($package_detail)) ? $package_detail['package_price'] : '';

                $upcoming[$value->event_id]['name'] =  (!empty($restaurant_detail)) ? $restaurant_detail->name : '';
                $upcoming[$value->event_id]['image'] =  (!empty($restaurant_detail) && $restaurant_detail->image != '') ? image_url . $restaurant_detail->image : '';
                $upcoming[$value->event_id]['address'] =  (!empty($restaurant_detail)) ? $restaurant_detail->address : '';
                $upcoming[$value->event_id]['landmark'] =  (!empty($restaurant_detail)) ? $restaurant_detail->landmark : '';
                $upcoming[$value->event_id]['city'] =  (!empty($restaurant_detail)) ? $restaurant_detail->city : '';
                $upcoming[$value->event_id]['zipcode'] =  (!empty($restaurant_detail)) ? $restaurant_detail->zipcode : '';
                $upcoming[$value->event_id]['rating'] =  $value->rating;
            }
        }
        $finalArray = array();
        foreach ($upcoming as $key => $val) {
            $finalArray[] = $val;
        }
        $data['upcoming'] = $finalArray;
        //past
        $this->db->select('event.entity_id as event_id,event.booking_date,event.no_of_people,event_detail.package_detail,event_detail.restaurant_detail,AVG (review.rating) as rating,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('event_detail', 'event.entity_id = event_detail.event_id', 'left');
        $this->db->join('review', 'event.restaurant_id = review.restaurant_id', 'left');
        $this->db->join('restaurant', 'event.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('event.user_id', $user_id);
        $this->db->where('event.booking_date <', $currentDateTime);
        $this->db->group_by('event.entity_id');
        $this->db->order_by('event.entity_id', 'desc');
        $resultPast = $this->db->get('event')->result();
        $past = array();
        foreach ($resultPast as $key => $value) {
            if (!isset($value->event_id)) {
                $past[$value->event_id] = array();
            }
            if (isset($value->event_id)) {
                $package_detail = unserialize($value->package_detail);
                $restaurant_detail = unserialize($value->restaurant_detail);
                $past[$value->event_id]['entity_id'] =  $value->event_id;
                $past[$value->event_id]['booking_date'] =  $value->booking_date;
                $past[$value->event_id]['no_of_people'] =  $value->no_of_people;
                $past[$value->event_id]['currency_code'] =  $value->currency_code;
                $past[$value->event_id]['currency_symbol'] =  $value->currency_symbol;

                $past[$value->event_id]['package_name'] =  (!empty($package_detail)) ? $package_detail['package_name'] : '';
                $past[$value->event_id]['package_detail'] = (!empty($package_detail)) ? $package_detail['package_detail'] : '';
                $past[$value->event_id]['package_price'] = (!empty($package_detail)) ? $package_detail['package_price'] : '';

                $past[$value->event_id]['name'] =  (!empty($restaurant_detail)) ? $restaurant_detail->name : '';
                $past[$value->event_id]['image'] =  (!empty($restaurant_detail) && $restaurant_detail->image != '') ? image_url . $restaurant_detail->image : '';
                $past[$value->event_id]['address'] =  (!empty($restaurant_detail)) ? $restaurant_detail->address : '';
                $past[$value->event_id]['landmark'] =  (!empty($restaurant_detail)) ? $restaurant_detail->landmark : '';
                $past[$value->event_id]['city'] =  (!empty($restaurant_detail)) ? $restaurant_detail->city : '';
                $past[$value->event_id]['zipcode'] =  (!empty($restaurant_detail)) ? $restaurant_detail->zipcode : '';
                $past[$value->event_id]['rating'] =  $value->rating;
            }
        }
        $final = array();
        foreach ($past as $key => $val) {
            $final[] = $val;
        }
        $data['past'] = $final;
        return $data;
    }
    //get recipe
    public function getRecipe($searchItem, $food, $timing, $language_slug)
    {
        $this->db->select('entity_id as item_id,name,image,recipe_detail,menu_detail,recipe_time,is_veg');
        if ($searchItem) {
            $this->db->where("name like '%" . $searchItem . "%'");
        } else if ($food == '' && $timing == '') {
            $this->db->where("popular_item", 1);
        }
        if ($food != '') {
            $this->db->where('is_veg', $food);
        }
        if ($timing) {
            $this->db->where('recipe_time <=', $timing);
        }
        $this->db->where('language_slug', $language_slug);
        $result =  $this->db->get('restaurant_menu_item')->result();
        foreach ($result as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
        }
        return $result;
    }
    //check if item exist
    public function checkExist($item_id)
    {
        $this->db->select('price,image,name,is_veg,vat,sd');
        $this->db->where('entity_id', $item_id);
        return $this->db->get('restaurant_menu_item')->first_row();
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
        // $this->db->where('restaurant.entity_id', $restaurant_id);
        // return $this->db->get($tblname)->first_row();
        $this->db->where('restaurant.entity_id', $restaurant_id);
        $result = $this->db->get($tblname)->first_row();
        $result->image = ($result->image) ? image_url . $result->image : '';
        return $result;
    }
    //get address
    public function getAddress($tblname, $fieldName, $user_id)
    {
        $this->db->select('entity_id as address_id,address,landmark,latitude,longitude,city,zipcode');
        $this->db->where($fieldName, $user_id);
        return $this->db->get($tblname)->result();
    }
    //get order detail
    public function getOrderDetail($flag, $user_id, $count, $page_no = 1)
    {
        $this->db->select('order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant.timings,restaurant.image as res_image,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
        $this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
        $this->db->join('order_status as status', 'order_master.entity_id = status.order_id', 'left');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1', 'left');
        $this->db->join('users', 'order_driver_map.driver_id = users.entity_id', 'left');
        $this->db->join('driver_traking_map', 'order_driver_map.driver_id = driver_traking_map.driver_id', 'left');
        $this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $status_arr = array('delivered', 'cancel', 'not_delivered');
        $status_arr2 = array('delivered', 'cancel', 'preorder', 'not_delivered');

        if ($flag == 'process') {
            $this->db->where_not_in('order_master.order_status', $status_arr2);
        }
        if ($flag == 'past') {
            $this->db->where_in('order_master.order_status', $status_arr);
        }
        if ($flag == 'preorder') {
            $this->db->where('order_master.order_status', 'preorder');
        }
        $this->db->where('order_master.user_id', $user_id);
        $this->db->order_by('order_master.entity_id', 'desc');
        $this->db->group_by(array("status.order_id", "status.order_status"));

        /*if($flag == 'past'){
            $this->db->group_by('order_master.entity_id');
            $this->db->limit($count,$page_no*$count);
        }*/

        $result =  $this->db->get('order_master')->result();
        $items = array();
        foreach ($result as $key => $value) {
            $currency_symbol = $this->common_model->getCurrencySymbol($value->currency_id);

            if (!isset($items[$value->order_id])) {
                $items[$value->order_id] = array();
                $items[$value->order_id]['preparing'] = '';
                $items[$value->order_id]['onGoing'] = '';
                $items[$value->order_id]['delivered'] = '';
                $items[$value->order_id]['cancel'] = '';
                $items[$value->order_id]['not_delivered'] = '';
            }
            if (isset($items[$value->order_id])) {
                /*$type = ($value->tax_type == 'Percentage')?'%':'';    */
                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['restaurant_id'] = $value->restaurant_id;
                $items[$value->order_id]['order_accepted'] = ($value->status == 1) ? 1 : 0;
                $items[$value->order_id]['accept_order_time'] = date('g:i A', strtotime($value->accept_order_time));
                $restaurant_detail = unserialize($value->restaurant_detail);
                $items[$value->order_id]['restaurant_name'] = (isset($restaurant_detail->name)) ? $restaurant_detail->name : '';
                $items[$value->order_id]['restaurant_address'] = (isset($restaurant_detail->address)) ? $restaurant_detail->address : '';
                $items[$value->order_id]['restaurant_image'] = ($value->res_image) ? image_url . $value->res_image : '';

                if ($value->coupon_name) {
                    $discount = array('label' => $this->lang->line('discount') . '(' . $value->coupon_name . ')', 'value' => $value->coupon_discount, 'label_key' => "Discount");
                } else {
                    $discount = '';
                }

                if ($discount) {
                    $items[$value->order_id]['price'] = array(
                        array('label' => $this->lang->line('sub_total'), 'value' => $value->subtotal, 'label_key' => "Sub Total"),
                        $discount,
                        /* array('label'=>'Service Fee','value'=>$value->tax_rate.$type),*/
                        array('label' => $this->lang->line('vat'), 'value' => $value->vat, 'label_key' => "Vat"),
                        array('label' => $this->lang->line('sd'), 'value' => $value->sd, 'label_key' => "SD"),
                        array('label' => $this->lang->line('delivery_charge'), 'value' => $value->delivery_charge, 'label_key' => "Delivery Charge"),
                        // array('label' => $this->lang->line('coupon_amount'), 'value' => $value->coupon_amount, 'label_key' => "Coupon Amount"),
                        array('label' => $this->lang->line('total'), 'value' => $value->total_rate, 'label_key' => "Total"),
                    );
                } else {
                    $items[$value->order_id]['price'] = array(
                        array('label' => $this->lang->line('sub_total'), 'value' => $value->subtotal, 'label_key' => "Sub Total"),
                        /* array('label'=>'Service Fee','value'=>$value->tax_rate.$type),*/
                        array('label' => $this->lang->line('vat'), 'value' => $value->vat, 'label_key' => "Vat"),
                        array('label' => $this->lang->line('sd'), 'value' => $value->sd, 'label_key' => "SD"),
                        array('label' => $this->lang->line('delivery_charge'), 'value' => $value->delivery_charge, 'label_key' => "Delivery Charge"),
                        // array('label' => $this->lang->line('coupon_amount'), 'value' => $value->coupon_amount, 'label_key' => "Coupon Amount"),
                        array('label' => $this->lang->line('total'), 'value' => $value->total_rate, 'label_key' => "Total"),
                    );
                }
                $timing =  $value->timings;
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
                            $newTimingArr[strtolower($day)]['closing'] = (!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                        }
                    }
                    $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }
                $items[$value->order_id]['order_status'] = ucfirst($value->order_status);
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['extra_comment'] = $value->extra_comment;
                $items[$value->order_id]['placed'] = date('g:i a', strtotime($value->order_date));
                if ($value->ostatus == 'preparing') {
                    $items[$value->order_id]['preparing'] = ($value->time != "") ? date('g:i A', strtotime($value->time)) : '';
                }
                if ($value->ostatus == 'onGoing') {
                    $items[$value->order_id]['onGoing'] = ($value->time != "") ? date('g:i A', strtotime($value->time)) : '';
                }
                if ($value->ostatus == 'delivered') {
                    $items[$value->order_id]['delivered'] = ($value->time != "") ? date('g:i A', strtotime($value->time)) : '';
                }
                if ($value->ostatus == 'cancel') {
                    $items[$value->order_id]['cancel'] = ($value->time != "") ? date('g:i A', strtotime($value->time)) : '';
                }
                if ($value->ostatus == 'not_delivered') {
                    $items[$value->order_id]['not_delivered'] = ($value->time != "") ? date('g:i A', strtotime($value->time)) : '';
                }
                $items[$value->order_id]['order_date'] = date('Y-m-d H:i:s', strtotime($value->order_date));
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if (!empty($item_detail)) {
                    $data1 = array();
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $customization = array();
                        $this->db->select('image,is_veg,status');
                        $this->db->where('entity_id', $valuee['item_id']);
                        $data = $this->db->get('restaurant_menu_item')->first_row();

                        // get order availability count
                        if (!empty($data)) {
                            if ($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '') ? $data->image : '';
                        $data1['is_veg'] = (!empty($data) && $data->is_veg != '') ? $data->is_veg : '';
                        $valueee['image'] = (!empty($data) && $data->image != '') ? image_url . $data1['image'] : '';
                        $valueee['is_veg'] = (!empty($data) && $data->is_veg != '') ? $data1['is_veg'] : '';

                        if ($valuee['is_customize'] == 1) {
                            if ($valuee['has_variation'] == 1) {
                                foreach ($valuee['variation_list'] as $k => $each_variation) {

                                    if ($each_variation['addons_category_list']) {
                                        foreach ($each_variation['addons_category_list'] as $k => $val) {
                                            $addonscust = array();

                                            if ($val['addons_list']) {
                                                foreach ($val['addons_list'] as $m => $mn) {

                                                    $addonscust[] = array(
                                                        'add_ons_id' => $mn['add_ons_id'],
                                                        'add_ons_name' => $mn['add_ons_name'],
                                                        'add_ons_price' => $mn['add_ons_price'],
                                                    );
                                                }
                                            }
                                            $addons[] = array(
                                                'addons_category_id' => $val['addons_category_id'],
                                                'addons_category' => $val['addons_category'],
                                                'addons_list' => $addonscust
                                            );
                                            $addonscust = array();
                                        }
                                    }

                                    $customization[] = array(
                                        'variation_id'  => $each_variation['variation_id'],
                                        'variation_name'   => $each_variation['variation_name'],
                                        'variation_price'   => $each_variation['variation_price'],
                                        'addons_category_list'  => $addons
                                    );
                                    $addons = array();
                                }
                            } else {
                                foreach ($valuee['addons_category_list'] as $k => $val) {
                                    $addonscust = array();
                                    foreach ($val['addons_list'] as $m => $mn) {
                                        if ($valuee['is_deal'] == 1) {
                                            $addonscust[] = array(
                                                'add_ons_id' => ($mn['add_ons_id']) ? $mn['add_ons_id'] : '',
                                                'add_ons_name' => $mn['add_ons_name'],
                                            );
                                        } else {
                                            $addonscust[] = array(
                                                'add_ons_id' => ($mn['add_ons_id']) ? $mn['add_ons_id'] : '',
                                                'add_ons_name' => $mn['add_ons_name'],
                                                'add_ons_price' => $mn['add_ons_price']
                                            );
                                        }
                                    }

                                    $customization[] = array(
                                        'addons_category_id' => $val['addons_category_id'],
                                        'addons_category' => $val['addons_category'],
                                        'addons_list' => $addonscust
                                    );
                                    $addonscust = array();
                                }
                            }
                        }

                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['price'] = ($valuee['rate']) ? $valuee['rate'] : '';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price']) ? $valuee['offer_price'] : '';
                        $valueee['itemTotal'] = ($valuee['itemTotal']) ? $valuee['itemTotal'] : '';


                        if (!empty($customization)) {

                            $valueee['has_variation'] = $valuee['has_variation'] == 1 ? 1 : 0;
                            $valueee['has_variation']
                                ? $valueee['variation_list']  =  $customization
                                : $valueee['addons_category_list'] = $customization;
                        }
                        $value1[] =  $valueee;
                        $customization = array();
                    }
                }

                $user_detail = unserialize($value->user_detail);
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude'])) ? $user_detail['latitude'] : '';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude'])) ? $user_detail['longitude'] : '';
                $items[$value->order_id]['user_address'] = (isset($user_detail['landmark'])) ? $user_detail['landmark'] : '';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['transaction_id']  = $value->transaction_id;
                $items[$value->order_id]['order_type'] = ($value->transaction_id) ? 'paid' : 'cod';
                $items[$value->order_id]['payment_options'] = ($value->payment_option);
                $items[$value->order_id]['available'] = ($count == 0) ? 'true' : 'false';
                if ($value->first_name && $value->order_delivery == 'Delivery') {
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver['mobile_number'] =  $value->phone_code . $value->mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    $driver['image'] = ($value->image) ? image_url . $value->image : '';
                    $driver['driver_id'] = ($value->driver_id) ? $value->driver_id : '';
                    $items[$value->order_id]['driver'] = $driver;
                }
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery == 'Delivery') ? 'delivery' : 'pickup';
                $items[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                $items[$value->order_id]['currency_code'] = $value->currency_code;
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }
        /*if($flag == 'process'){
            $res['in_process'] = $finalArray;
        }
        if($flag == 'past'){
            $res['past'] = $finalArray;
        }*/
        return $finalArray;
    }
    //check coupon
    public function checkCoupon($coupon, $subtotal = null, $restaurant_id = null, $order_delivery = null, $user_id = null)
    {
        if (!$subtotal && !$restaurant_id && !$order_delivery && !$user_id) {
            $this->db->where('name', $coupon);
            $this->db->where('status', 1);
            return $this->db->get('coupon')->first_row();
        }

        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');

        $this->db->select('coupon.*');
        $this->db->join('coupon_restaurant_map', 'coupon.entity_id = coupon_restaurant_map.coupon_id', 'left');
        $this->db->join('restaurant', 'coupon_restaurant_map.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('max_amount <=', $subtotal);
        if ($restaurant_id)
            $this->db->where('coupon_restaurant_map.restaurant_id', $restaurant_id);
        $this->db->where('end_date >', $currentTime);
        $this->db->where('start_date <', $currentTime);
        $this->db->where('coupon.name', $coupon);

        $this->db->where('coupon.status', 1);
        //$this->db->where('(coupon_type = "discount_on_cart" OR coupon_type = "user_registration")');
        // if ($order_delivery == 'Delivery') {
        //     $this->db->or_where('coupon_type', "free_delivery");
        // }
        // return $this->db->get('coupon')->result();

        $coupons = $this->db->get('coupon')->first_row();

        return $coupons;
    }


    //use of a single coupon
    public function couponUseByUser($coupon_id, $user_id)
    {
        $this->db->select('coupon_id, user_id');
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('user_id', $user_id);
        $this->db->where("NOT (order_status = 'not_delivered' OR order_status = 'cancel')");
        return $this->db->get("order_master")->num_rows();
    }
    public function getResName($restaurant_id)
    {

        $this->db->select('restaurant_address.address, restaurant.name,restaurant.entity_id');
        $this->db->join('restaurant_address', 'restaurant.entity_id=restaurant_address.resto_entity_id', 'left');
        $this->db->where('restaurant.entity_id', $restaurant_id);
        $data = $this->db->get('restaurant')->result();
        return $data;
    }

    //get coupon list
    public function getcouponList($subtotal, $restaurant_id, $order_delivery, $user_id)
    {

        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');

        $this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,currencies.currency_symbol,currencies.currency_code,coupon.usablity,coupon.maximum_use,coupon.image');
        $this->db->join('coupon_restaurant_map', 'coupon.entity_id = coupon_restaurant_map.coupon_id', 'left');
        $this->db->join('restaurant', 'coupon_restaurant_map.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('max_amount <=', $subtotal);
        $this->db->where('coupon_restaurant_map.restaurant_id', $restaurant_id);
        $this->db->where('end_date >', $currentTime);
        $this->db->where('start_date <', $currentTime);

        $this->db->where('coupon.status', 1);
        $this->db->group_by('coupon.entity_id', 1);
        // $this->db->where('(coupon_type = "discount_on_cart" OR coupon_type = "user_registration")');
        // if ($order_delivery == 'Delivery') {
        //     $this->db->where('coupon_type', "free_delivery");
        // }
        //  return $this->db->get('coupon')->result();

        $coupons = $this->db->get('coupon')->result();
        //return $coupons[0]->coupon_type;

        foreach ($coupons as $key => $value) {
            //return $value->coupon_type;
            $value->image = ($value->image) ? image_url . $value->image : '';
            if ($value->maximum_use != 0) {
                $maximum_use = $value->maximum_use;
                $coupon_use = $this->couponUseByUser($value->coupon_id, $user_id);

                if ($maximum_use <= $coupon_use) {
                    unset($coupons[$key]);
                }
            }

            if ($value->coupon_type == 'selected_user') {
                $check = $this->checkCouponUser($value->coupon_id, $user_id);
                if ($check == null) {
                    unset($coupons[$key]);
                }
            }

            if ($value->usablity == 'onetime') {
                $check = $this->checkOneTimeUser($value->coupon_id, $user_id);
                if ($check > 0) {
                    unset($coupons[$key]);
                }
            }

            if ($value->coupon_type == 'user_registration') {

                if ($is_admin == 1) {
                    $rows = $this->db->get_where('order_master', array('user_id' => $user_id, 'coupon_id' => $value->coupon_id))->num_rows();
                } else {
                    $rows = $this->db->get_where('order_master', array('user_id' => $user_id))->num_rows();
                }

                if ($rows > 0) {
                    unset($coupons[$key]);
                }
            }

            if ($value->coupon_type == 'discount_on_items' || $value->coupon_type == 'gradual') {
                unset($coupons[$key]);
            }
        }

        return array_values($coupons);
    }

    public function checkCouponUser($id, $user)
    {
        return $this->db->get_where('coupon_user_map', array('user_id' => $user, 'coupon_id' => $id))->first_row();
    }

    public function checkOneTimeUser($id, $user)
    {
        $this->db->select('coupon_id, user_id');
        $this->db->where('coupon_id', $id);
        $this->db->where('user_id', $user);
        $this->db->where("NOT (order_status = 'not_delivered' OR order_status = 'cancel')");
        return $this->db->get("order_master")->num_rows();
        // $this->db->get_where('order_master', array('user_id' => $user, 'order_status' => 'delivered', 'coupon_id' => $id));
        // $this->db->where("NOT (order_status = 'not_delivered' OR order_status = 'cancel')");
        // return $this->db->get("order_master")->num_rows();
        //return $this->db->num_rows();
        //return $this->db->get_where('order_master', array('user_id' => $user, 'order_status' => 'delivered', 'coupon_id' => $id))->num_rows();
    }


    //get notification
    public function getNotification($user_id, $count, $page_no = 1)
    {
        $page_no = ($page_no > 0) ? $page_no - 1 : 0;
        // $this->db->select('notifications.notification_title,notifications.image,notifications.notification_description,notifications_users.notification_id');
        // $this->db->join('notifications', 'notifications_users.notification_id =  notifications.entity_id', 'left');
        // $this->db->limit($count, $page_no * $count);
        $this->db->select('count(map_id),created_date');
        $this->db->where('notifications_users.user_id', $user_id);
        $this->db->group_by('DAY(created_date)', 'desc');
        $this->db->order_by('notifications_users.notification_id', 'desc');
        $res1 = $this->db->get('notifications_users')->result();
        $noti = array();

        foreach ($res1 as $key => $value) {
            $this->db->select('notifications.notification_title,notifications.image,notifications_users.created_date,notifications.notification_description,notifications_users.notification_id');
            $this->db->join('notifications', 'notifications_users.notification_id =  notifications.entity_id', 'left');


            $this->db->where('notifications_users.user_id', $user_id);
            // $this->db->where('DATE(notifications_users.created_date)', DATE($value->created_date));
            $res2 = $this->db->get('notifications_users')->result();
            foreach ($res2 as $key => $value1) {
                $value1->image = ($value1->image) ? image_url . $value1->image : '';
            }
            //$noti[$value->created_date]=$res2;
            array_push($noti, $res2);
        }
        $data['noti'] = $noti;


        //  $data['result'] = $res1;
        $this->db->select('notifications.notification_title,notifications.image,,notifications.notification_description,notifications_users.notification_id');
        $this->db->join('notifications', 'notifications_users.notification_id =  notifications.entity_id', 'left');
        $this->db->where('notifications_users.user_id', $user_id);
        $data['count'] =  $this->db->count_all_results('notifications_users');

        return $data;
    }
    //check delivery is available
    public function checkOrderDelivery($users_latitude, $users_longitude, $user_id, $restaurant_id, $request, $order_id, $user_km = NULL, $driver_km = NULL)
    {
        $this->db->select('users.entity_id');
        $this->db->where('user_type', 'Driver');
        $driver = $this->db->get('users')->result_array();

        $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
        $this->db->join('users', 'driver_traking_map.driver_id = users.entity_id', 'left');
        $this->db->where('users.status', 1);
        $this->db->where('driver_traking_map.created_date = (SELECT
            driver_traking_map.created_date
        FROM
            driver_traking_map
        WHERE
            driver_traking_map.driver_id = users.entity_id
        ORDER BY
            driver_traking_map.created_date desc
        LIMIT 1)');
        if (!empty($driver)) {
            $this->db->where_in('driver_id', array_column($driver, 'entity_id'));
        }
        $detail = $this->db->get('driver_traking_map')->result();
        $flag = false;
        if (!empty($detail)) {
            foreach ($detail as $key => $value) {
                $longitude = $value->longitude;
                $latitude = $value->latitude;
                $this->db->select("(6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
                $this->db->where('restaurant.entity_id', $restaurant_id);
                if (!empty($driver_km)) {
                    $this->db->having('distance <', $driver_km);
                } else {
                    $this->db->having('distance <', DRIVER_NEAR_KM);
                }
                $result = $this->db->get('restaurant')->result();
                if ($request == 1) {
                    if (!empty($result)) {
                        if ($value->device_id) {
                            $flag = true;
                            //get langauge
                            $languages = $this->db->select('*')->get_where('languages', array('language_slug' => $value->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory);

                            $array = array(
                                'order_id' => $order_id,
                                'driver_id' => $value->driver_id,
                                'date' => date('Y-m-d H:i:s')
                            );
                            $id = $this->addRecord('order_driver_map', $array);
                            #prep the bundle
                            $fields = array();
                            $message = $this->lang->line('push_new_order');
                            $fields['to'] = $value->device_id; // only one user to send push notification
                            $fields['notification'] = array('body'  => $message, 'sound' => 'default');
                            $fields['data'] = array('screenType' => 'order');

                            $headers = array(
                                'Authorization: key=' . FCM_KEY,
                                'Content-Type: application/json'
                            );
                            #Send Reponse To FireBase Server
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                            $result = curl_exec($ch);
                            curl_close($ch);
                        }
                    }
                }
                if ($request == '') {
                    if (!empty($result)) {
                        if ($value->device_id) {
                            $flag = true;
                        }
                    }
                }
            }
        }


        if ($flag == false && $request == 1) {
            return true;
        }
        if ($flag == true && $request == '') {
            return true;
        }
    }
    // check restaurant availability
    public function checkRestaurantAvailability($users_latitude, $users_longitude, $user_id, $restaurant_id, $request, $order_id, $user_km = NULL, $driver_km = NULL)
    {
        $this->db->select("(6371 * acos ( cos ( radians($users_latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($users_longitude) ) + sin ( radians($users_latitude) ) * sin( radians( address.latitude )))) as distance");
        $this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
        $this->db->where('restaurant.entity_id', $restaurant_id);
        $user_result = $this->db->get('restaurant')->result();
        if (!empty($user_result)) {
            if (!empty($user_km)) {
                if ($user_result[0]->distance <= $user_km) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($user_result[0]->distance <= USER_NEAR_KM) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    //get driver location for traking
    public function getdriverTracking($order_id, $user_id)
    {
        $this->db->select('order_driver_map.order_id,order_master.total_rate,order_master.order_status,driver_traking_map.latitude as driverLatitude,driver_traking_map.longitude as driverLongitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,user_address.latitude as userLat,user_address.longitude as userLong,user_address.address,user_address.landmark,user_address.zipcode,user_address.state,user_address.city,driver.first_name,driver.last_name,driver.image,driver.mobile_number');
        $this->db->join('order_driver_map', 'driver_traking_map.driver_id = order_driver_map.driver_id', 'left');
        $this->db->join('order_master', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
        $this->db->join('user_address', 'order_master.address_id = user_address.entity_id', 'left');
        $this->db->join('users as driver', 'order_driver_map.driver_id = driver.entity_id', 'left');
        $this->db->where('order_master.entity_id', $order_id);
        $this->db->order_by('driver_traking_map.traking_id', 'desc');
        $detail = $this->db->get('driver_traking_map')->first_row();
        if (!empty($detail)) {
            $detail->image = ($detail->image) ? $detail->image : '';
        }
        return $detail;
    }
    //get addos data
    public function getAddonsPrice($add_ons_id)
    {
        $this->db->where('add_ons_id', $add_ons_id);
        return $this->db->get('add_ons_master')->first_row();
    }

    //get variations data
    public function getVariationDetails($variation_id)
    {
        $this->db->where('entity_id', $variation_id);
        return $this->db->get('variations')->first_row();
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
    //get order count of user
    public function checkUserCountCoupon($UserID)
    {
        $this->db->where('user_id', $UserID);
        $this->db->where('coupon_id', $coupon_id);
        return $this->db->get('order_master')->num_rows();
    }
    //get delivery charfes by lat long
    public function checkGeoFence($tblname, $fldname, $id)
    {
        $this->db->where($fldname, $id);
        return $this->db->get($tblname)->result();
    }
    // get restaurant currency
    public function getRestaurantCurrency($restaurant_id)
    {
        $this->db->select('currencies.currency_code,currencies.currency_symbol');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('restaurant.entity_id', $restaurant_id);
        return $this->db->get('restaurant')->result();
    }
    // method to get details by id
    public function getEditDetail($entity_id)
    {
        $this->db->select('order.*,res.name, address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode');
        $this->db->join('restaurant as res', 'order.restaurant_id = res.entity_id', 'left');
        $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
        $this->db->join('users as u', 'order.user_id = u.entity_id', 'left');
        $this->db->join('user_address as uaddress', 'u.entity_id = uaddress.user_entity_id', 'left');
        return  $this->db->get_where('order_master as order', array('order.entity_id' => $entity_id))->first_row();
    }
    //get invoice data
    public function getInvoiceMenuItem($entity_id)
    {
        $this->db->where('order_id', $entity_id);
        return $this->db->get('order_detail')->first_row();
    }

    public function discountedItem($restaurant_id)
    {
        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');

        $this->db->select('items.item_id,c.name');
        $this->db->join('coupon as c', 'c.entity_id = items.coupon_id', 'left');
        $this->db->join('coupon_restaurant_map as res', 'c.entity_id = res.coupon_id', 'left');
        $this->db->where('c.coupon_type', 'discount_on_items');
        $this->db->where('res.restaurant_id', $restaurant_id);
        $this->db->where('c.end_date >', $currentTime);
        $this->db->where('c.start_date <', $currentTime);
        $this->db->where('c.status', 1);

        return $this->db->get('coupon_item_map as items')->result();
    }

    public function getOrderAmountBoundary($data)
    {
        $this->db->select('OptionValue');
        return $this->db->get_where('system_option', array('OptionName' => $data))->first_row();
    }

    public function getMenuItems($latitude, $longitude, $searchItem, $zone_id)
    {
        $restaurants = $this->getRestaurantIdsInRadius($zone_id, $latitude, $longitude);
        $res_id = array_column($restaurants, 'restaurant_id');
        $distance = NEAR_KM;
        $this->db->select("res.entity_id restaurant_id,
                res.content_id content_id,
                res.name restaurant_name,
                cat.name category_name,
                menu.name menu_name,
                menu.price,
                menu.menu_detail description,
                menu.image,
                menu.check_add_ons is_customizable,

        ");
        $this->db->join('restaurant_menu_item menu', 'res.entity_id = menu.restaurant_id', 'inner');
        $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'inner');
        $this->db->join('category cat', 'menu.category_id = cat.entity_id', 'inner');
        $where = "(menu.name like '%" . $searchItem . "%' OR menu.menu_detail like '%" . $searchItem . "%'  OR cat.name like '%" . $searchItem . "%' ) ";

        $this->db->where($where);
        $this->db->where('res.status', 1);
        $this->db->where('menu.status', 1);
        $this->db->where('cat.status', 1);
        if ($res_id) {
            $this->db->where_in('res.entity_id', $res_id);
        }

        // $this->db->where('distance <', NEAR_KM);
        // $this->db->order_by('distance', 'asc');
        $this->db->order_by('menu.name', 'asc');
        return  $this->db->get('restaurant as res')->result();
    }

    // get active auto apply coupon
    public function getActiveAutoApply($res_id)
    {
        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');

        $this->db->select('c.*');
        $this->db->join('coupon_restaurant_map as map', 'map.coupon_id = c.entity_id', 'left');
        $this->db->where('map.restaurant_id', $res_id);
        $this->db->where('c.end_date >', $currentTime);
        $this->db->where('c.start_date <', $currentTime);
        $this->db->where('c.usablity', 'autoApply');
        $this->db->where('c.status', 1);
        return $this->db->get('coupon as c')->first_row();
    }


    public function getActiveGradual($res_id)
    {
        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');
        $this->db->join('coupon_restaurant_map as map', 'map.coupon_id = coupon.entity_id', 'left');
        $this->db->where('map.restaurant_id', $res_id);
        return $this->db->get_where('coupon', array('end_date >' => $currentTime, 'start_date <' => $currentTime, 'coupon_type' => "gradual", 'status' => 1))->result();
    }

    public function checkAllItems($coupon_id)
    {
        return $this->db->get_where('coupon_item_map', array('coupon_id' => $coupon_id))->num_rows();
    }

    public function gradualItem($coupon_id)
    {
        return $this->db->get_where('coupon_item_map', array('coupon_id' => $coupon_id))->result();
    }

    public function checkPreviousOrder($user_id, $coupon_id)
    {
        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');

        // $this->db->join('order_master as o', 'o.coupon_id = c.entity_id', 'left');
        // $this->db->where('c.coupon_type', 'gradual');
        // $this->db->where('c.end_date >', $currentTime);
        // $this->db->where('c.start_date <', $currentTime);
        // $this->db->where('c.status', 1);
        // $this->db->where('o.user_id', $user_id);
        // $this->db->where('o.coupon_id', $coupon_id);

        // return $this->db->get('coupon as c')->num_rows();

        return $this->db->get_where('order_master', array('coupon_id' => $coupon_id, 'user_id' => $user_id))->num_rows();
    }


    public function getSequence($coupon_id)
    {
        $this->db->order_by('sequence', 'asc');
        return $this->db->get_where('coupon_gradual', array('coupon_id' => $coupon_id))->result();
    }

    public function getSpecificGradual($restaurant_id)
    {
        $item_ids = array();

        $a = new DateTime();
        $currentTime = $a->format('Y-m-d H:i:s');
        $this->db->select('coupon.entity_id');
        $this->db->join('coupon_restaurant_map as map', 'map.coupon_id = coupon.entity_id', 'left');
        $this->db->where('map.restaurant_id', $restaurant_id);
        $result = $this->db->get_where('coupon', array('end_date >' => $currentTime, 'start_date <' => $currentTime, 'coupon_type' => "gradual", 'status' => 1, 'gradual_all_items' => 0))->result();

        foreach ($result as $key => $value) {
            $this->db->select('coupon_id,item_id');
            $ids = $this->db->get_where('coupon_item_map', array('coupon_id' => $value->entity_id))->result();

            foreach ($ids as $key => $val) {
                array_push($item_ids, $val);
            }
        }

        //$gradual_item_ids = array_column($item_ids, 'item_id');
        return $item_ids;
    }


    public function checkRecords($tblName, $user_id, $coupon_id)
    {
        return $this->db->get_where($tblName, array('user_id' => $user_id, 'coupon_id' => $coupon_id))->first_row();
    }

    public function getHighestSequence($coupon_id)
    {
        $this->db->select('sequence');
        $this->db->order_by('sequence', 'desc');
        return $this->db->get_where('coupon_gradual', array('coupon_id' => $coupon_id))->first_row();
    }

    public function changeTiming($open, $close)
    {
        $this->db->select('entity_id,timings');
        $result = $this->db->get('restaurant')->result();

        foreach ($result as $key => $value) {
            $timing = $value->timings;
            if ($timing) {
                $time =  unserialize(html_entity_decode($timing));
                foreach ($time as $keys => $values) {
                    if ($values['open'] && $values['close']) {

                        $time[$keys]['open'] = $open; //"8:00";
                        $time[$keys]['close'] = $close; //"20:00";
                    }
                }

                $changed_time  = serialize($time);

                //update to database
                $this->db->set('timings', $changed_time)->where('entity_id', $value->entity_id)->update('restaurant');
            }
        }
    }

    public function checkGeoFenceForZone($latitude, $longitude)
    {
        $result = $this->getZones();


        foreach ($result as $key => $value) {

            $lat_longs = $value->lat_long;
            $lat_longs =  explode('~', $lat_longs);
            $polygon = array();
            foreach ($lat_longs as $keys => $val) {
                if ($val) {
                    $val = str_replace(array('[', ']', ','), array('', '', ' '), $val);
                    //$polygon = explode(',', $val);
                    //$val = explode(',', $val);
                    $polygon[] = $val;
                }
            }
            if ($polygon[0] != $polygon[count($polygon) - 1])
                $polygon[count($polygon)] = $polygon[0];

            $points = array('' . $latitude . ' ' . $longitude . '');

            $polygon_result = $this->pointInPolygon($points, $polygon);

            if ($polygon_result == 'inside' || $polygon_result == 'vertex' || $polygon_result == 'boundary') {
                return $value->entity_id;
            }
        }

        return;
    }

    //check lat long exist in area
    // public function checkGeoFenceForZone($latitude, $longitude)
    // {
    //     $result = $this->getZones();

    //     $oddNodes = "";

    //     foreach ($result as $key => $value) {

    //         $lat_longs = $value->lat_long;
    //         $lat_longs =  explode('~', $lat_longs);
    //         $polygon = array();
    //         foreach ($lat_longs as $key => $val) {
    //             if ($val) {
    //                 $val = str_replace(array('[', ']',','), array('', '',' '), $val);
    //                 //$polygon = explode(',', $val);
    //                 //$val = explode(',', $val);
    //                 $polygon[] = $val;


    //             }
    //         }
    //         if ($polygon[0] != $polygon[count($polygon) - 1])
    //             $polygon[count($polygon)] = $polygon[0];
    //         $j = 0;
    //         $x = $longitude;
    //         $y = $latitude;
    //         $n = count($polygon);
    //         $intersections = 0;
    //         $status = "";
    //         for ($i = 0; $i < $n; $i++) {

    //             $j++;
    //             // if ($j == $n) {
    //             //     $j = 0;
    //             // }
    //             // if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
    //             //     $y))) {
    //             //     if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
    //             //         $polygon[$i][1]) < $x) {
    //             //         $oddNodes = $value->entity_id;
    //             //     }
    //             // }

    //             // $vertex1 = $vertices[$i - 1];
    //             // $vertex2 = $vertices[$i];
    //             if ($polygon[$i][0] == $y && $polygon[$i][1] == $x) {
    //                 $status = "vertex";
    //             }

    //             if ($polygon[$i][0] == $polygon[$j][0] && $polygon[$i][0] == $y && $x > min($polygon[$i][1], $polygon[$j][1]) && $x < max($polygon[$i][1], $polygon[$j][1])) { // Check if point is on an horizontal polygon boundary
    //                 $status = "boundary";
    //             }
    //             if ($y > min($polygon[$i][0], $polygon[$j][0]) && $y <= max($polygon[$i][0], $polygon[$j][0]) && $x <= max($polygon[$i][1], $polygon[$j][1]) && $polygon[$i][0] != $polygon[$j][0]) {
    //                 $xinters = ($y - $polygon[$i][0]) * ($polygon[$j][1] - $polygon[$i][1]) / ($polygon[$j][0] - $polygon[$i][0]) + $polygon[$i][1];
    //                 if ($xinters == $x) { // Check if point is on the polygon boundary (other than horizontal)
    //                     $status = "boundary";
    //                 }
    //                 if ($polygon[$i][1] == $polygon[$j][1] || $x <= $xinters) {
    //                     $intersections++;
    //                 }
    //             }
    //         }

    //         // If the number of edges we passed through is odd, then it's in the polygon.
    //         if ($intersections % 2 != 0 || $status == 'boundary' || $status == 'vertex') {
    //             //return "inside";
    //             $oddNodes = $value->entity_id;
    //             break;
    //         }
    //     }
    //     //$oddNodes = $value->entity_id;
    //     return $oddNodes;
    // }


    public function getZones()
    {
        return $this->db->select('entity_id,lat_long')->get_where('zone', array('status' => 1))->result();
    }

    public function getRestaurantIds($zone_id)
    {
        return $this->db->get_where('zone_res_map', array('zone_id' => $zone_id))->result();
    }

    public function getRestaurantIdsInRadius($zone_id, $user_lat, $user_long)
    {
        $this->db->select('zone.radius, zone_res_map.*, restaurant_address.latitude, restaurant_address.longitude');
        $this->db->join('zone', 'zone.entity_id = zone_res_map.zone_id');
        $this->db->join('restaurant_address', 'restaurant_address.resto_entity_id = zone_res_map.restaurant_id');
        $this->db->where('zone_id', $zone_id);
        $res =  $this->db->get('zone_res_map')->result_array();

        foreach ($res as $k => $v) {

            // https://stackoverflow.com/questions/27928/calculate-distance-between-two-latitude-longitude-points-haversine-formula

            $res_long = $v['longitude'];
            $res_lat = $v['latitude'];

            $R = 6371; // Radius of the earth in km
            $dLat = deg2rad($res_lat - $user_lat);  // deg2rad below
            $dLon = deg2rad($res_long - $user_long);
            $a =
                sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($user_lat)) * cos(deg2rad($res_lat)) *
                sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $d = $R * $c; // Distance in km

            if ($d > floatval($v['radius'])) {
                unset($res[$k]);
            }
        }

        return $res;
    }


    public function checkGeoFenceForDelivery($latitude, $longitude, $zone_id)
    {
        $value = $this->getRecord('zone', 'entity_id', $zone_id);

        $oddNodes = false;

        $lat_longs = $value->lat_long;
        $lat_longs =  explode('~', $lat_longs);
        $polygon = array();
        foreach ($lat_longs as $key => $val) {
            if ($val) {
                $val = str_replace(array('[', ']'), array('', ''), $val);
                $polygon[] = explode(',', $val);
            }
        }
        if ($polygon[0] != $polygon[count($polygon) - 1])
            $polygon[count($polygon)] = $polygon[0];
        $j = 0;
        $x = $longitude;
        $y = $latitude;
        $n = count($polygon);
        $intersections = 0;
        $status = "";
        for ($i = 0; $i < $n; $i++) {

            $j++;

            if ($polygon[$i][0] == $y && $polygon[$i][1] == $x) {
                $status = "vertex";
            }

            if ($polygon[$i][0] == $polygon[$j][0] && $polygon[$i][0] == $y && $x > min($polygon[$i][1], $polygon[$j][1]) && $x < max($polygon[$i][1], $polygon[$j][1])) { // Check if point is on an horizontal polygon boundary
                $status = "boundary";
            }
            if ($y > min($polygon[$i][0], $polygon[$j][0]) && $y <= max($polygon[$i][0], $polygon[$j][0]) && $x <= max($polygon[$i][1], $polygon[$j][1]) && $polygon[$i][0] != $polygon[$j][0]) {
                $xinters = ($y - $polygon[$i][0]) * ($polygon[$j][1] - $polygon[$i][1]) / ($polygon[$j][0] - $polygon[$i][0]) + $polygon[$i][1];
                if ($xinters == $x) { // Check if point is on the polygon boundary (other than horizontal)
                    $status = "boundary";
                }
                if ($polygon[$i][1] == $polygon[$j][1] || $x <= $xinters) {
                    $intersections++;
                }
            }


            // If the number of edges we passed through is odd, then it's in the polygon.
            if ($intersections % 2 != 0 || $status == 'boundary' || $status == 'vertex') {
                //return "inside";
                $oddNodes = true;
                break;
            }
        }
        //$oddNodes = $value->entity_id;
        return $oddNodes;
    }

    function pointInPolygon($point, $polygon, $pointOnVertex = true)
    {
        $this->pointOnVertex = $pointOnVertex;

        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point[0]);
        $vertices = array();
        foreach ($polygon as $vertex) {
            $vertices[] = $this->pointStringToCoordinates($vertex);
        }

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);

        for ($i = 1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i - 1];
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon.
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }

    function pointOnVertex($point, $vertices)
    {
        foreach ($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
    }

    function pointStringToCoordinates($pointString)
    {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
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
            $value->image = ($value->image) ? image_url . $value->image : '';
            $value->applink = DEEP_LINK_BASE_URL . generateDeepLink('campaign', $value->entity_id);
        }

        return $data;
    }


    public function getFavouriteRestaurants($restaurants, $zone_id, $latitude, $longitude)
    {
        $getResIds = $this->getRestaurantIds($zone_id);
        $res_ids = array_intersect($restaurants, array_column($getResIds, 'restaurant_id'));

        if ($res_ids) {
            $this->db->select("res.likes,res.content_id,res.is_popular,res.entity_id as restuarant_id,res.name,res.image,res.cover_image,res.price_range,res_map.coupon_id as coupons,address.address,address.landmark,AVG (review.rating) as rating,(6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
            $this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
            $this->db->join('review', 'res.entity_id = review.restaurant_id', 'left');
            $this->db->join('coupon_restaurant_map as res_map', 'res.entity_id = res_map.restaurant_id', 'left');
            $this->db->join('coupon', 'coupon.entity_id = res_map.coupon_id', 'left');
            $this->db->where('res.status', 1);
            $this->db->where_in('res.entity_id', $res_ids);
            $this->db->group_by('res.entity_id');
            $this->db->order_by('res.sort_value');
            $result =  $this->db->get('restaurant as res')->result();

            $a = new DateTime();
            $currentTime = $a->format('Y-m-d H:i:s');

            foreach ($result as $key => $value) {

                // no of orders
                $this->db->where('restaurant_id', $value->restuarant_id);
                $this->db->where('order_status', 'delivered');
                $no_of_orders = $this->db->get('order_master')->num_rows();

                $this->db->select('coupon.name,coupon.description,res_map.coupon_id');
                $this->db->join('coupon_restaurant_map as res_map', 'coupon.entity_id = res_map.coupon_id');

                $this->db->where('res_map.restaurant_id', $value->restuarant_id);
                $this->db->where('coupon.status', 1);
                $activeDates = ['start_date <=' => $currentTime, 'end_date >=' => $currentTime];
                $this->db->where($activeDates);
                $couponDetails = $this->db->get('coupon')->result_array();

                $value->coupons = $couponDetails;
                $value->image = ($value->image) ? image_url . $value->image : '';
                $value->cover_image = ($value->cover_image) ? image_url . $value->cover_image : '';
                $value->rating = ($value->rating) ? number_format((float)$value->rating, 1, '.', '') : null;
                $value->no_of_orders = $no_of_orders;
            }

            return $result;
        }
    }


    public function updateDriver($order_id)
    {
        //check re-rounting for 3 riders
        $check = $this->checkReRounting($order_id);
        if ($check >= ORDER_REROUTE_MAX_COUNT + 1) {
            $this->writeLog('CHECK OVER:' . $check . ' ');
            $array = array(
                'order_id' => $order_id,
                'driver_id' => 0,
            );
            $this->addDataDriver('order_driver_map', $array);
        } else {
            //get cancelled riders
            $this->writeLog('CHECK NOT OVER, UPDATING NEXT RIDER:' . $check . ' ');
            $this->db->select('driver_id');
            $cancelled_riders = $this->db->get_where('order_driver_map', array('order_id' => $order_id))->result_array();
            $this->writeLog('CANCELLED RIDERS COUNT:', count($cancelled_riders) . ' ');
            $detail = $this->getNearestRider($cancelled_riders, $order_id);

            //$check = NEAR_KM;

            if (!empty($detail)) {
                $this->writeLog('RIDER FOUND: ' . $detail->driver_id . 'DISTANCE: ' . $detail->DISTANCE . ' ');
                $selectedTime = date('Y-m-d H:i:s');

                $reroute_time = REROUTING_TIME;

                $endTime = strtotime('+' . $reroute_time . ' minutes', strtotime($selectedTime));


                $check = NEAR_KM;

                $comsn = 0;

                if ($check > 3) {
                    $this->db->select('OptionValue');
                    $comsn = $this->db->get_where('system_option', array('OptionSlug' => 'driver_commission_more'))->first_row();
                } else {
                    $this->db->select('OptionValue');
                    $comsn = $this->db->get_where('system_option', array('OptionSlug' => 'driver_commission_less'))->first_row();
                }


                $array = array(
                    'order_id' => $order_id,
                    'driver_id' => $detail->driver_id,
                    'date' => date('Y-m-d H:i:s'),
                    'distance' => $detail->DISTANCE,
                    'driver_commission' => $comsn->OptionValue,
                    'commission' => $comsn->OptionValue,
                    'timer' => date('Y-m-d H:i:s', $endTime)
                );
                $this->addDataDriver('order_driver_map', $array);

                #prep the bundle
                $fields = array();
                $message = $this->lang->line('push_new_order');
                $fields['to'] = $detail->device_id; // only one user to send push notification
                $fields['notification'] = array('body'  => $message, 'sound' => 'default');
                $fields['data'] = array('screenType' => 'order');

                $headers = array(
                    'Authorization: key=' . FCM_KEY,
                    'Content-Type: application/json'
                );
                #Send Reponse To FireBase Server
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                //Update driver status based on order count
                // $active_orders = $this->Sub_dashboard_model->order_count($detail->driver_id);
                // $this->db->select('OptionValue');
                // $max_orders = $this->db->get_where('system_option', array('OptionSlug' => 'rider_max_order'))->first_row();
                // if ($active_orders == $max_orders) {
                $this->db->set('engage', 1)->where('entity_id', $detail->driver_id)->update('users');
                // } else {
                //     $this->db->set('engage', 0)->where('entity_id', $detail->driver_id)->update('users');
                // }
                return;
            } else {
                $this->writeLog('RIDER NOT FOUND:');
                $array = array(
                    'order_id' => $order_id,
                    'driver_id' => 0,
                );
                $this->addDataDriver('order_driver_map', $array);
            }
        }
    }

    public function checkReRounting($order_id)
    {
        return $this->db->get_where('order_driver_map', array('order_id' => $order_id))->num_rows();
    }

    public function writeLog($message)
    {
        //echo file_put_contents("rerouting_log.txt","${message}\n", FILE_APPEND);
    }

    public function addDataDriver($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }

    public function getNearestRider($cancelled_riders, $order_id)
    {
        //get restaurant id
        $get_restaurant = $this->getRecord('order_master', 'entity_id', $order_id);
        $zone_id = $get_restaurant->zone_id;
        //get restaurant's latitude and longitude
        $res_lat_lng = $this->getRecord('restaurant_address', 'resto_entity_id', $get_restaurant->restaurant_id);

        //converting array to a single line
        $driver_ids = implode(',', array_column($cancelled_riders, 'driver_id'));
        //$where = array('users.status' => 1, 'users.user_type' => 'Driver', 'users.device_id !=' => '', 'users.onoff' => 1, 'users.suspend' => 1, 'users.engage' => 0);

        // $this->db->select("dtm.driver_id,dtm.latitude,dtm.longitude,MAX(dtm.created_date) as date,users.device_id,(6371 * acos ( cos ( radians(dtm.latitude) ) * cos( radians($res_lat_lng->latitude) ) * cos( radians($res_lat_lng->longitude) - radians(dtm.longitude) ) + sin ( radians(dtm.latitude) ) * sin( radians($res_lat_lng->latitude)))) as distance");
        // $this->db->join('users', 'users.entity_id = dtm.driver_id', 'left');
        // $this->db->where($where);
        // if (!empty($cancelled_riders)) {
        //     $this->db->where_not_in('dtm.driver_id', 'array_column($cancelled_riders, 'driver_id')');
        // }
        // $this->db->group_by('dtm.driver_id');
        // $this->db->having('distance <=', GET_DRIVER_KM);
        // $this->db->order_by('distance', 'asc');
        // $this->db->limit(1);

        $distance = $this->getRiderRadius();
        $last_update = DRIVER_ORDER_EXPIRE_MINUTE;

        if (!empty($cancelled_riders)) {
            $where = '
            u.user_type = "Driver"
            AND
             u.onoff = 1 AND u.status = 1
            AND
             u.device_id != ""
            AND
             u.suspend = 1
            AND
             u.engage = 0
            AND
             u.entity_id NOT IN (' . $driver_ids . ')
            AND
             u.zone_id = ' . $zone_id . '
             ';
        } else {
            $where = '
            u.user_type = "Driver"
            AND
             u.onoff = 1
            AND
             u.status = 1
            AND
             u.device_id != ""
            AND
             u.suspend = 1
            AND
             u.engage = 0
            AND
             u.zone_id = ' . $zone_id . '
             ';
        }

        $result = $this->db->query('SELECT
        driver_id,
        6371 * ACOS(
            COS(RADIANS(' . $res_lat_lng->latitude . ')) * COS(RADIANS(latitude)) * COS(
                RADIANS(longitude) - RADIANS(' . $res_lat_lng->longitude . ')
            ) + SIN(RADIANS(' . $res_lat_lng->latitude . ')) * SIN(RADIANS(latitude))
        ) AS DISTANCE,
        latitude,
        longitude,
        dtm.created_date,u.device_id,TIMESTAMPDIFF(MINUTE,dtm.created_date,NOW()) AS last_update
        FROM
            (
                SELECT DISTINCT
                temp_dtm.driver_id,
                temp_dtm.latitude,
                temp_dtm.longitude,
                temp_dtm.created_date
                FROM
                    (
                    SELECT
                        driver_id,
                        MAX(created_date) created_date
                    FROM
                        driver_traking_map
                    GROUP BY
                        driver_id
                ) GD,
                driver_traking_map temp_dtm
                WHERE
                    temp_dtm.driver_id = GD.driver_id AND temp_dtm.created_date = GD.created_date
        ) dtm
        JOIN users AS u

        ON
            dtm.driver_id = u.entity_id
        WHERE
            ' . $where . '
        HAVING DISTANCE < ' . $distance . ' AND last_update <= ' . $last_update . '
        ORDER BY DISTANCE ASC
        LIMIT 1')->first_row();

        // if ($result) {
        //     foreach ($result as $k => $v) {

        //         // auto routing will assign only 1 rider
        //         $order_count = $this->Sub_dashboard_model->order_count($v->driver_id);
        //         if ($order_count && $order_count != 0) {
        //             unset($result[$k]);
        //         }
        //     }
        // }
        return $result;
    }

    public function checkResponse($condition, $driver)
    {
        $this->db->set('no_response', 1)->where($condition)->update('order_driver_map');
        $this->db->set('engage', 0)->where('entity_id', $driver)->update('users');
    }


    public function getOrders()
    {
        $this->db->select('map.order_id as order_id,map.timer as timer,map.driver_id as driver');
        $this->db->join('order_master as o', 'o.entity_id = map.order_id');
        $this->db->where('o.order_status', 'placed');
        $this->db->where('o.order_delivery', 'Delivery');
        //$this->db->where('o.business_type', 1);
        $this->db->where('map.timer !=', NULL);
        $this->db->where('map.is_accept', 0);
        $this->db->where('map.cancel_reason', NULL);
        $this->db->where('map.cancel', 0);
        $this->db->where('map.no_response', 0);
        $this->db->where('map.driver_id !=', 0);
        return $this->db->get('order_driver_map as map')->result();
    }

    public function commission($id)
    {
        $this->db->select('commission');
        $this->db->where('entity_id', $id);
        return $this->db->get('restaurant')->first_row();
    }

    public function getRiderRadius()
    {
        // the radius is in km
        $res = $this->db->select('OptionValue')
            ->from('system_option')
            ->where('OptionSlug', 'rider_radius')
            ->get()
            ->first_row();

        return $res->OptionValue;
    }

    public function getOperationSettings()
    {
        $res = $this->db->select('name, value')
            ->from('operation_sytem_option')
            ->get()
            ->result();

        foreach ($res as $k => $v) {
            $new_arr[$v->name] = $v->value;
        }

        return $new_arr;
    }

    public function checkRestaurantOnOff($restaurant_id)
    {


        $result = new stdClass(); // equivalent to (object)[]

        $opsSetting = $this->getOperationSettings();

        $operation_timing = @unserialize(html_entity_decode($opsSetting['operation_timing']));

        if ($opsSetting['operation_on_off'] == 1) {


            $a = new DateTime();
            $currentTime = $a->format('Y-m-d H:i:s');


            $this->db->select("res.entity_id as restuarant_id,res.timings,res.break_timing");

            $this->db->where('res.entity_id', $restaurant_id);

            $result =  $this->db->get('restaurant as res')->result();
            foreach ($result as $key => $value) {

                $break_timing = $value->break_timing;
                $timing = $value->timings;
                if ($timing) {
                    $timing =  unserialize(html_entity_decode($timing));
                    $newTimingArr = array();
                    $day = date("l");
                    $count = 0;
                    loop:
                    foreach ($timing as $keys => $values) {
                        if ($keys == strtolower($day)) {

                            $count++;


                            if (empty($values['open']) && empty($values['close'])) {
                                if ($count == 8) {
                                    break;
                                } else if ($day == "Sunday") {
                                    $day = date("l", strtotime($day . "+1 days"));
                                    goto loop;
                                } else {
                                    $day = date("l", strtotime($day . "+1 days"));
                                }
                                // $newTimingArr[strtolower($day)]['open'] =  date('g:i A', strtotime($values['open']));
                            } else if ((date('H:i') < date('H:i', strtotime($values['open']))) || date(DATE_ATOM) < date(DATE_ATOM, strtotime($day . $values['open']))) {
                                $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                $newTimingArr[strtolower($day)]['off'] = 'close'; //(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                $newTimingArr[strtolower($day)]['closing'] = 'close'; //(!empty($values['close'])) ? ($values['close'] <= date('H:m')) ? 'close' : 'open' : 'close';
                            } else if (date('H:i') > date('H:i', strtotime($values['close']))) {
                                $day = date("l", strtotime($day . "+1 days"));
                            } else {

                                if (date("l") < $day) {
                                    $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                    $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                    $newTimingArr[strtolower($day)]['off'] = 'close'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                    $newTimingArr[strtolower($day)]['closing'] = 'close'; //(date("l") > $day) ? 'close':'open';
                                } else {
                                    $newTimingArr[strtolower($day)]['open'] =  date(DATE_ATOM, strtotime($day . $values['open']));
                                    $newTimingArr[strtolower($day)]['close'] = date(DATE_ATOM, strtotime($day . $values['close']));
                                    $newTimingArr[strtolower($day)]['off'] = 'open'; //(date("l") > $day) ? 'close':'open';//(!empty($values['open']) && !empty($values['close'])) ? 'open' : 'close';
                                    $newTimingArr[strtolower($day)]['closing'] = 'open'; //(date("l") > $day) ? 'close':'open';

                                }
                            }


                            if ($operation_timing && $operation_timing != '') {
                                $op_time_count = 0;
                                foreach ($operation_timing as $ot) {
                                    if ($ot['on'] == 1) {
                                        $opsStartTime = new DateTime($ot['open']);
                                        $opsCloseTime = new DateTime($ot['close']);

                                        if (!(($opsStartTime->diff(new DateTime)->format('%R') == '+') &&
                                            ($opsCloseTime->diff(new DateTime)->format('%R') == '-'))) {
                                            $newTimingArr[strtolower($day)]['off'] = 'close';
                                            $newTimingArr[strtolower($day)]['closing'] = 'close';
                                        } else {
                                            $op_time_count++;
                                        }

                                        if ($op_time_count > 0) {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $value->timings = $newTimingArr[strtolower($day)];
            }

            return $result;
        } else {
            return $result;
        }
    }

    //For burning Points
    function get_total_points($user_id)
    {
        $this->db->select("((select ifnull(sum(points),0) from reward_point where cost = 1)-(select ifnull(sum(points),0) from reward_point where cost = 2)) as points");
        $this->db->where('reward_point.user_id', $user_id);
        return $this->db->get('reward_point')->result();
    }
    function get_coupon_points($coupon_id)
    {
        return $this->db->select('*')->from('reward_point_setting')
            ->where('type is not null')
            ->where('status', 1)
            ->where('entity_id', $coupon_id)
            // ->where('type', 'Coupon')
            ->get()->result();
    }
    public function getrewardPoints($user_id)
    {
        $this->db->select('SUM(points) as earn');
        $this->db->where('cost', 1);
        $this->db->where('user_id', $user_id);
        $data1 = $this->db->get('reward_point')->result();

        $this->db->select('SUM(points) as burn');
        $this->db->where('cost', 2);
        $this->db->where('user_id', $user_id);
        $data2 = $this->db->get('reward_point')->result();


        $total_earning = $data1[0]->earn;
        $name = ["Bronze", "Silver", "Gold", "Diamond"];
        $this->db->select('name,value');
        $this->db->where('type', null);
        $this->db->where_in('name', $name);
        $data3 = $this->db->get('reward_point_setting')->result();
        $dataArr = [];
        $status = null;
        $rate = null;
        $color = "blue";
        foreach ($data3 as $key => $value) {

            $dataArr[$value->name] = $value->value;
        }
        if ($total_earning > $dataArr['Gold']) {
            $status = "Diamond";
            $r = (100 * ($total_earning - $dataArr['Gold'])) / ($dataArr['Diamond'] - $dataArr['Gold']);
            $rate = $r > 100 ? 100 : $r;
            $color = "#B9F2FF";
        } else if ($total_earning > $dataArr['Silver'] && $total_earning <= $dataArr['Gold']) {
            $status = "Gold";
            $rate = (100 * ($total_earning - $dataArr['Silver'])) / ($dataArr['Gold'] - $dataArr['Silver']);
            $color = "#FFD700";
        } else if ($total_earning > $dataArr['Bronze'] && $total_earning <= $dataArr['Silver']) {
            $status = "Silver";
            $rate = (100 * ($total_earning - $dataArr['Bronze'])) / ($dataArr['Silver'] - $dataArr['Bronze']);
            $color = "#C0C0C0";
        } else {
            $status = "Bronze";
            $rate = (100 * $total_earning) / $dataArr['Bronze'];
            $color = "#967444";
        }

        $type = ["Coupon", "Voucher"];
        $this->db->select('entity_id,name,value,cost,image');
        $this->db->where_in('type', $type);
        $this->db->where('status', 1);
        $data4 = $this->db->get('reward_point_setting')->result();
        foreach ($data4 as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
        }

        $result = array(
            'total_earning' => $data1[0]->earn,
            'total_points' => $data1[0]->earn - $data2[0]->burn,
            'status' => $status,
            'rate' => round($rate) . "%",
            'color' => $color,
            'coupons' => $data4
        );
        return $result;
    }
    public function addCouponData($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }
    public function getAllRestaurantID()
    {
        $data = $this->db->select('entity_id')
            ->from('restaurant')
            ->where('status', 1)
            ->get()
            ->result_array();


        return $data;
    }
    //insert batch
    public function insertBatch($tblname, $data, $id)
    {
        if ($id) {
            $this->db->where('coupon_id', $id);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname, $data);
        return $this->db->insert_id();
    }
    public function checkOrderStatus($address_id)
    {
        $this->db->select('order_master.entity_id as order_id,order_status');
        $this->db->group_start();
        $this->db->where('order_master.order_status !=', 'delivered');
        $this->db->where('order_master.order_status !=', 'not_delivered');
        $this->db->where('order_master.order_status !=', 'cancel');
        $this->db->group_end();

        $this->db->where('order_master.address_id', $address_id);
        $result =  $this->db->get('order_master')->result();
        return count($result);
    }
}
