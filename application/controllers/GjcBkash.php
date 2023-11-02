 <?php
  $id_token = "abcd";
  defined('BASEPATH') or exit('No direct script access allowed');
  require APPPATH . 'libraries/REST_Controller.php';
  class GjcBkash extends CI_Controller
  {
    var    $session1 = "Alpha";
    var $session2 = "Beeta";

    function __construct()
    {
      // Construct the parent class
      parent::__construct();
      $this->load->model('user_model');
      $this->load->library('form_validation');
    }


    public function bKashGJC()
    {
    //   $strJsonFileContents = file_get_contents("https://foodaani.com/bKashConfig.json");
    //   $array = json_decode($strJsonFileContents, true);
    //   $data = json_decode($this->input->raw_input_stream, true);
    //   $total_amount = $data['total_amount'];
      $total_amount=10;
      $session_Id = $data['session_Id']?$data['session_Id']:"asdfghjkloi98";
      $user_Id = $data['user_Id']?$data['user_Id']:1234;
      $session1 = $session_Id;

      echo ' <html>
        <head> 
          <meta name="viewport" content="width=device-width" ,="" initial-scale="2.0/">
          <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script> 
          <script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script> 
        </head>
        <body>
          <button id="bKash_button"  style="opacity:0;">Pay With Bkash</button>
        </body>
        <script>
        document.getElementById("bKash_button").style.visibility="hidden";
        let grantTokenUrl = "https://foodibd/GjcBkash/bkash_Get_Token/' . $session1 . '/' . $user_Id . '";
        let createCheckoutUrl = "https://foodibd/GjcBkash/createPayment/' . $session1 . '/' . $user_Id . '/' .$total_amount. '";
        let executeCheckoutUrl = "https://foodibd/GjcBkash/executePayment/' . $session1 . '/' . $user_Id . '";
        let queryCheckoutUrl = "https://foodibd/GjcBkash/queryPayment/' . $session1 . '/' . $user_Id . '";
        
        $(document).ready(function () {
            var xhr = new XMLHttpRequest();
            
            xhr.addEventListener("readystatechange", function () {
              if (this.readyState === this.DONE) {
                initBkash();
              }
            });
            
            xhr.open("POST", grantTokenUrl);
            xhr.setRequestHeader("accept", "application/json");
            xhr.setRequestHeader("content-type", "application/json");
            xhr.send();

        });
        function initBkash() {
            bKash.init({
              paymentMode: "checkout", // Performs a single checkout.
              paymentRequest: {"amount" : ' . $total_amount . ',  "currency":"BDT","merchantInvoiceNumber": "MI015454"},
              test1:"",
        
              createRequest: function (request) {
                $.ajax({
                  url: createCheckoutUrl,
                  type: "POST",
                  data: JSON.stringify(request),
                  success: function (data) { 
                  console.log("PAYMENT CREATED::: ",JSON.parse(data));
                    data = JSON.parse(data).resultdata
                    console.log("PAYMENT CREATED::: ",data.paymentID);
                    if (data && data.paymentID != null) {
                      paymentID = data.paymentID;
                      console.log("paymentid",paymentID)
                      bKash.create().onSuccess(data);
                      test1=data;
                    } 
                    else {
                      bKash.create().onError(); // Run clean up code
                      alert(data.message);
                    }
        
                  },
                  error: function (e) {
                    bKash.create().onError(); // Run clean up code
                    // alert(e);
                    data1 = {
                      message: e,
                      errorCode:"Error",
                    }
                    window.ReactNativeWebView.postMessage(JSON.stringify(data1));
                    bKash.execute().onError();//run clean up code
                  }
                });
              },
              executeRequestOnAuthorization: function () {
             
                $.ajax({
                  url: executeCheckoutUrl,
                  type: "POST",
                  "contentType": "application/json",
                  data: JSON.stringify({
                    "paymentID": paymentID,
                  }),
                  timeout: 30000,
                  success: function (data) {
                  console.log("execute",JSON.parse(data).resultdata)
                    data = JSON.parse(data).resultdata
                    if (data && data.paymentID != null ) {
                   
                    
                    // On success, perform your desired action
                      // QUERY PAYMENT
                      window.ReactNativeWebView.postMessage(JSON.stringify({data : data, errorCode:null}));
                    
                    } else {
                      data1 = {
                        message:"Error:payment failed\n " +  data.errorMessage,
                        errorCode:data.errorCode,
                        data:data
                      }
                      window.ReactNativeWebView.postMessage(JSON.stringify(data1));
                      bKash.execute().onError();//run clean up code
                    }
                  },
                  error: function (e,textstatus,m,data=test1) {
                  if(textstatus==="timeout")
                  {
                    $.ajax({
                        url: queryCheckoutUrl,
                        type: "POST",
                        "contentType": "application/json",
                        data: JSON.stringify({
                            "paymentID": paymentID,
                        }),
                        success: function (data) {
    
                            data0 = JSON.parse(data).resultdata;
                            if (data0 && data0.paymentID != null && data0.transactionStatus=="completed") {
                                window.ReactNativeWebView.postMessage(JSON.stringify({data : data0, errorCode:null}));
                            } else {
                                data2 = {
                                  message:"Error while processing transaction",
                                  errorCode:"Error",
                                }
                                window.ReactNativeWebView.postMessage(JSON.stringify(data2));
                                bKash.execute().onError(); // Run clean up code
                            }
            
                        },
                        error: function (e) {
                            // alert("An alert has occurred during execute");
                            console.log("ERROR",e)
                            data = {
                              message:"Error while processing transaction",
                              errorCode:"Error",
                            }
                            window.ReactNativeWebView.postMessage(JSON.stringify(data));
                            bKash.execute().onError(); // Run clean up code
                        }
                    });     
                }
                  else{
                    // alert("An alert has occurred during execute");
                    data = {
                      message:"Error while processing transaction",
                      errorCode:"Error",
                    }
                    window.ReactNativeWebView.postMessage(JSON.stringify(data));
                    bKash.execute().onError(); // Run clean up code
                  }
                  }
                });
              },
              onClose: function () {
                // alert("User has clicked the close button");
                data = {
                  message: "Cancelled",
                  errorCode:"Error",
                  
                  
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
              }
            });
        
            // $("#bKash_button").removeAttr("disabled");
            $("#bKash_button").click();
            //  document.getElementById("loader").style.visibility="hidden";
            // document.getElementById("bKash_button").style.visibility="visible";
        }
        
        </script>
</html>   
';
    }
    public function bkash_Get_Token($Q, $R)
    {

       $body=array();
              $body['app_key']='4c4140pqb9p7trqc1rfp86p1k8';
               $body['app_secret']='afldd3eth4u60kkl090l01easksotr1pll4dvn4echkk8in8c7t';
        $proxy = "";
       $header = array(
            'Content-Type:application/json',
          );
        $url = curl_init();
        curl_setopt( $url,CURLOPT_URL, 'https://foodibd/v1/driver_api/bksh' );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($url, CURLOPT_POSTFIELDS,  json_encode($body));
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata1 = curl_exec($url);
        curl_close($url);
        $resultdata2 = json_decode($resultdata1, true);
        $id_token = $resultdata2["id_token"];
        $data =  array(
          'title' => 'API Grant',
          'url' => $array["tokenURL"],
        );
        $this->saveToken($R,$id_token);
        echo json_encode($data);
    }
    public function tokenValidation($userid, $tokenuser)
    {
      $conn = new mysqli('localhost', $this->db->username, $this->db->password, $this->db->database);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } else {

        $sql = "select session_id from users  where entity_id = '" . $userid . "'";

        $result = $conn->query($sql);
        while ($row = $result->fetch_array()) {
          $session2 = $row['session_id'];
        }
        if ($session2 == $tokenuser) {
          return true;
        } else {
          return false;
        }
      }
    }
    public function saveToken($userid, $token)
    {
      $conn = new mysqli('localhost', $this->db->username, $this->db->password, $this->db->database);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } else {

        //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        $sql = "update cart_amount set id_token='" . $token . "'  where user_id = '" . $userid . "'";

        $result = $conn->query($sql);
      }
    }
    public function getToken($userid)
    {
      $token_id = "";
      $conn = new mysqli('localhost', $this->db->username, $this->db->password, $this->db->database);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } else {

        //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        $sql = "select id_token from cart_amount  where user_id = '" . $userid . "'";

        $result = $conn->query($sql);
        while ($row = $result->fetch_array()) {
          $token_id = $row['id_token'];
        }
        return  $token_id;
      }
    }
    public function amountValidation($userid, $amount, $saleIntent)
    {

      $total_amount = 0;
      $conn = new mysqli('localhost', $this->db->username, $this->db->password, $this->db->database);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } else {

        $sql = "select cart_total from cart_amount  where user_id = '" . $userid . "'";

        $result = $conn->query($sql);
        while ($row = $result->fetch_array()) {
          $total_amount = $row['cart_total'];
        }
        if (floatval($total_amount) == floatval($amount) && $saleIntent = "sale") {

          return true;
        } else {

          return false;
        }
      }
    }
    public function createPayment($q, $r,$total_amount)
    {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $strJsonFileContents = file_get_contents("https://foodaani.com/bKashConfig.json");
        $array = json_decode($strJsonFileContents, true);
        $data = json_decode($this->input->raw_input_stream, true);
        $amount = $total_amount;
        $token = $this->getToken($r);
        $invoice = $randomString; // must be unique
        $intent = "sale";
        $proxy = "";
          $body=array();
          $body['id_token']=$token;
          $body['amount']=$amount;

          $header = array(
            'Content-Type:application/json',
          );
          $url = curl_init();
        curl_setopt( $url,CURLOPT_URL, 'https://foodibd/v1/driver_api/create' );
          curl_setopt($url, CURLOPT_HTTPHEADER, $header);
          curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($url, CURLOPT_POSTFIELDS, json_encode($body));
          curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
          //curl_setopt($url, CURLOPT_PROXY, $proxy);

          $resultdata = curl_exec($url);
          $resultdata2=json_decode($resultdata);
          $resultdata3=$resultdata2->resultdata;
          curl_close($url);
          $data =  array(
            'title' => 'Create Payment',
            'url' => "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/create",
            'resultdata' => json_decode($resultdata3)
          );
          echo json_encode($data);
    }
    public function executePayment($a, $b)
    {

        $data = json_decode($this->input->raw_input_stream, true);
        $paymentID = $data["paymentID"];
        $token = $this->getToken($b);
        $proxy = "";
          $body=array();
          $body['payment_id']=$paymentID;
          $body['token']=$token;

          $header = array(
            'Content-Type:application/json',
          );
          $url = curl_init();
          curl_setopt( $url,CURLOPT_URL, 'https://foodibd/v1/driver_api/execute' );
          curl_setopt($url, CURLOPT_HTTPHEADER, $header);
          curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($url, CURLOPT_POSTFIELDS, json_encode($body));
          curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
          //curl_setopt($url, CURLOPT_PROXY, $proxy);

          $resultdata = curl_exec($url);
          $resultdata2=json_decode($resultdata);
          $resultdata3=$resultdata2->resultdata;
          curl_close($url);
        $data =  array(
          'title' => 'Execute Payment',
          'url' => "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/execute/". $paymentID,
          'resultdata' => json_decode($resultdata3)
        );
        echo json_encode($data);

    }

    public function queryPayment($c, $d)
    {

        $data = json_decode($this->input->raw_input_stream, true);
        $paymentID = $data["paymentID"];
        $token = $this->getToken($b);
        $proxy = "";
          $body=array();
          $body['payment_id']=$paymentID;
          $body['token']=$token;

          $header = array(
            'Content-Type:application/json',
          );
          $url = curl_init();
          curl_setopt( $url,CURLOPT_URL, 'https://foodibd/v1/driver_api/query' );
          curl_setopt($url, CURLOPT_HTTPHEADER, $header);
          curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($url, CURLOPT_POSTFIELDS, json_encode($body));
          curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
          //curl_setopt($url, CURLOPT_PROXY, $proxy);

          $resultdata = curl_exec($url);
          $resultdata2=json_decode($resultdata);
          $resultdata3=$resultdata2->resultdata;
          curl_close($url);
        $data =  array(
          'title' => 'Execute Payment',
          'url' => "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/execute/". $paymentID,
          'resultdata' => json_decode($resultdata3)
        );
        echo json_encode($data);
    }
    public function searchTransaction()
    {
      $strJsonFileContents = file_get_contents("https://foodaani.com/bKashConfig.json");
      $array = json_decode($strJsonFileContents, true);
      // $data = json_decode($this->input->raw_input_stream, true);
      // $paymentID = $data["paymentID"];
      $proxy = $array["proxy"];

      $url = curl_init($array["searchURL"] . "7J1202U52S");

      $header = array(
        'Content-Type:application/json',
        'authorization:eyJraWQiOiJmalhJQmwxclFUXC9hM215MG9ScXpEdVZZWk5KXC9qRTNJOFBaeGZUY3hlamc9IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiI4ZGU4ZjBlMC1mY2RjLTQyNzMtYjY4YS1iNDAwOWNjZjc3ZDEiLCJhdWQiOiI1bmVqNWtlZ3VvcGo5Mjhla2NqM2RuZThwIiwiZXZlbnRfaWQiOiIxN2UyZDZhYi0zNmUzLTQ5ZTMtOWNhYS01ZGU0YzBhZjZkMDMiLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTYwMTU1MzYxOCwiaXNzIjoiaHR0cHM6XC9cL2NvZ25pdG8taWRwLmFwLXNvdXRoZWFzdC0xLmFtYXpvbmF3cy5jb21cL2FwLXNvdXRoZWFzdC0xX2tmNUJTTm9QZSIsImNvZ25pdG86dXNlcm5hbWUiOiJ0ZXN0ZGVtbyIsImV4cCI6MTYwMTU1NzIxOCwiaWF0IjoxNjAxNTUzNjE4fQ.H4i3-H73CzYha0Ln_OnHlILAQqjQV15_WnpHtAl9xwtyZep4qYcocQ-YOKGmib6a_q7GrYvrFy9fSidDeOwq83Y27ieymws6vJ9HXCqKuJ_y_OAEODpji3yaFCPhZnYs0yuKzdiMpfDf-B9Otl-H36-llRghy3xSG8ZZChwxWVchh93aLp2Fhu0B4LZBe7JF7AOyo7y6seGHj1l7D-Cfr17aE8vWtXYosPhK1TUB7GsZy-K7NXHZPv1F0PR4wdFkLSfusZnl3VFzq9moWkaZcWJyus8wVz-Ykm5J3krHfIF7G3PEOVvU_YrgjR0Ws0VErUnr1hUXmzRfKwa9OnPy4g',
        'x-app-key:' . $array["app_key"]
      );

      curl_setopt($url, CURLOPT_HTTPHEADER, $header);
      curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
      //curl_setopt($url, CURLOPT_PROXY, $proxy);

      $resultdata = curl_exec($url);
      curl_close($url);
      $data =  array(
        'title' => 'Query Payment',
        'url' => $array["searchURL"] . "7J1202U52S",
        //'header'=> $header,
        'resultdata' => json_decode($resultdata)
      );
      echo print_r($resultdata);
    }
  }
