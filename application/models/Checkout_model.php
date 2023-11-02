<?php
class Checkout_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function getDeliveryCharges()
    {
        $id = get_cookie('cart_restaurant');
        $this->db->select('price_charge');
        return $this->db->get_where('delivery_charge', array('restaurant_id' => $id))->first_row();
    }
    // get users address
    public function getUsersAddress($UserID)
    {
        return $this->db->get_where('user_address', array('user_entity_id' => $UserID))->result_array();
    }
    // get address latlong
    public function getAddressLatLng($entity_id)
    {
        $this->db->select('latitude,longitude');
        return $this->db->get_where('user_address', array('entity_id' => $entity_id))->first_row();
    }
    //get delivery charfes by lat long
    public function checkGeoFence($restaurant_id)
    {
        $this->db->where('restaurant_id', $restaurant_id);
        return $this->db->get('delivery_charge')->result();
    }
    //get coupon list
    public function getCouponsList($subtotal, $restaurant_id, $order_delivery, $user_id)
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

    public function couponUseByUser($coupon_id, $user_id)
    {
        $this->db->select('coupon_id, user_id');
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('user_id', $user_id);
        $this->db->where("NOT (order_status = 'not_delivered' OR order_status = 'cancel')");
        return $this->db->get("order_master")->num_rows();
    }


    // get coupon details
    public function getCouponDetails($entity_id)
    {
        return $this->db->get_where('coupon', array('entity_id' => $entity_id))->first_row();
    }
    //get order count of user
    public function checkUserCountCoupon($UserID)
    {
        $this->db->where('user_id', $UserID);
        return $this->db->get('order_master')->num_rows();
    }
    //get tax
    public function getRestaurantTax($restaurant_id)
    {
        $this->db->select('restaurant.name,restaurant.image,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code');
        $this->db->join('restaurant_address', 'restaurant.entity_id = restaurant_address.resto_entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('restaurant.entity_id', $restaurant_id);
        return $this->db->get('restaurant')->first_row();
    }
    //get address
    public function getAddress($entity_id)
    {
        $this->db->select('entity_id as address_id,address,landmark,latitude,longitude,city,zipcode');
        $this->db->where('entity_id', $entity_id);
        return $this->db->get('user_address')->first_row();
    }

    public function getResZone($res_id)
    {
        return $this->db->get_where('zone_res_map', ['restaurant_id' => $res_id])
            ->first_row()
            ->zone_id;
    }
}
