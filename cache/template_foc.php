<?php
/* Template Name: FPC Calculator */
get_header();
?>
<?php
$ipaddress = '';
if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
}
else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
}
else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
}
else if (isset($_SERVER['HTTP_FORWARDED'])) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
}
else if (isset($_SERVER['REMOTE_ADDR'])) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
}
else {
    $ipaddress = 'UNKNOWN';
}

// echo $ipaddress;
$PublicIP = $ipaddress;
// $PublicIP = '18.203.66.158';

// $location_json     = file_get_contents("http://www.geoplugin.net/json.gp?ip=92.251.255.11&base_currency=USD");
$location_json = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $PublicIP . "&base_currency=USD");
if (($location_json !== false) && (!empty($location_json))) {
    $decode_location = json_decode($location_json, true);
    if ((isset($decode_location['geoplugin_currencyCode'])) && ($decode_location['geoplugin_currencyCode'] != '') && ($decode_location['geoplugin_currencyCode'] != NULL)) {
        $user_currencyCode = $decode_location['geoplugin_currencyCode'];
        // echo $user_currencyCode;
        $currencyCode_set = 1;
    }
    else {
        $user_currencyCode = '';
        $currencyCode_set = 0;
    }
}
?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/roi_calc.css">
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/management-services.css">
<style type="text/css">
    #text01
    {
        background: none;
        margin: 1em 0;
        height: 50px !important;
        width: 100%;
        border: 1px solid #b9b9b9;
        outline: none;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d
    {
        margin: 2em 0;

    }

     ._t-ms-a-da2 {
    margin: 3em 0 0;
    text-align: center;
    float: left;
    width: 100%;
}
    ._t-ms-a-da2 ._ms_nbtn {
    display: inline-block;
    position: relative;
    margin: 0 5px;
    font-size: 14px;
}
    ._t-ms-a-da2 ._ms_nbtn:after {
    position: absolute;
    content: url(<?php bloginfo('stylesheet_directory'); ?>/images/ms_n_arrow.png);
    width: 18px;
    height: 14px;
    right: 16px;
    top: 8px;
}
    ._t-ms-a-da2 input[type=button] {
    border: 0;
    color: #fff;
    width: 110px;
    padding: 6px 23px;
    position: relative;
    display: inline-block;
    border-radius: 25px;
    background: #f66c40;
    background: -moz-linear-gradient(left, #f66c40 0%, #cd3606 100%);
    background: -webkit-linear-gradient(left, #f66c40 0%, #cd3606 100%);
    background: linear-gradient(to right, #f66c40 0%, #cd3606 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f66c40', endColorstr='#cd3606',GradientType=1 );
    text-align: left;
}
    ._t-ms-w ._t-ms-a ._t-ms-a-c ul li
    {
        width: 20%;
    }
    ._3cc-s0
    {
        margin: 2em 0 0;
    }
    ._3cc-f0 input[type="radio"] + label ._rbf {
    display: inline-block;
    width: 100%;
    height: 40px;
    margin: 0 4px 0 0;
    vertical-align: middle;
    cursor: pointer;
    border-radius: 40px;
    padding: 5px 20px;
    background: #fff8f5;
    border: 1px solid #f74d00;
}
 ._3cc-f0 input[type="radio"] {
    display: none;
}

._3cc-f0 input[type="radio"] + label {
    color: #292321;
    font-family: Arial, sans-serif;
    font-size: 14px;
    width: 100%;
    margin: 0;
}
._3cc-f0 input[type="radio"] + label ._rbf p {
    margin: 6px 0 0 0;
}
 ._3cc-f0 input[type="radio"]:checked + label ._rbf {
    border: 1px solid #f74d00;
    background: #f74d00;
    color: #fff;
}
._3cc-f0 input[type="radio"]:checked + label ._rbf p
    {
        color: #fff;
    }
._3cc-f0 {
    margin: 0 0 15px;
}
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da1 input[type="radio"] {
    background: #fff8f5;
    border: 1px solid #f74d00;
    width: 100%;
    height: 38px;
    border-radius: 25px;
    padding: 0 50px;
}
/*end*/
#tst_cycl img
{
    width: 450px;
    height: auto;
    margin: -63px auto;
}
.ext_radio
{
    display: block;
    width: 100%;
}
.ext_radio ul
{
    text-align: center;
}
.ext_radio ul li
{
    display: inline-block;
    margin: 0 15px;
}
.ext_radio [type="radio"]:checked,
.ext_radio [type="radio"]:not(:checked) {
    position: absolute;
    left: -9999px;
}
.ext_radio [type="radio"]:checked + label,
.ext_radio [type="radio"]:not(:checked) + label
{
    position: relative;
    padding-left: 28px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #000;
}
.ext_radio [type="radio"]:checked + label:before,
.ext_radio [type="radio"]:not(:checked) + label:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 20px;
    height: 20px;
    border: 1px solid #b3b3b3;
    border-radius: 100%;
    background: #fff;
}
.ext_radio [type="radio"]:checked + label:after,
.ext_radio [type="radio"]:not(:checked) + label:after {
    content: '';
    width: 12px;
    height: 12px;
    background: #f0653a;
    position: absolute;
    top: 4px;
    left: 4px;
    border-radius: 100%;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
}
.ext_radio [type="radio"]:not(:checked) + label:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
}
.ext_radio [type="radio"]:checked + label:after {
    opacity: 1;
    -webkit-transform: scale(1);
    transform: scale(1);
}

._t-ms-w ._t-ms-a ._t-ms-a-hg h1 {
    margin: 0;
    color: #f74d00;
    font-weight: bold;
    font-size: 35px;
    font-family: inherit;
}
._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 ._ms_pbtn:after
{
    top: 14px !important;
}
._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 .fpc_sub_btn:after
{
    content: none !important;
}
#fpc_try_again
{
    width: 146px;
}
   
    #test_cost_form{
        border: none;
    }
._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 input[type=submit] {
    border: 0;
    color: #fff;
    width: 130px;
    text-align: center;
    padding: 6px 23px;
    position: relative;
    display: inline-block;
    border-radius: 25px;
    background: #f66c40;
    background: -moz-linear-gradient(left, #f66c40 0%, #cd3606 100%);
    background: -webkit-linear-gradient(left, #f66c40 0%, #cd3606 100%);
    background: linear-gradient(to right, #f66c40 0%, #cd3606 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f66c40', endColorstr='#cd3606',GradientType=1 );
}
    #fpc_platform_dv img{
        width: auto !important;
    }
    ._ra-wrap {
    width:100%;
    margin:0 auto;
}



.range-slider {
}

.rs-range {
  margin-top: 29px;
  width: 100%;
  -webkit-appearance: none;
  padding: 0;
  margin: 8px 0 0 !important;    
  border: 0;
}
.rs-range:focus {
  outline: none;
}
.rs-range::-webkit-slider-runnable-track {
  width: 100%;
  height: 8px;
  cursor: pointer;
  box-shadow: none;
  border-radius: 0px;
  border: 0px solid #010101;
  border-radius: 6px;
  background: #f36f43;
    background: -moz-linear-gradient(left, #a4dbda 0%, #97a73a 33%, #a74024 68%, #f36f43 100%);
    background: -webkit-gradient(left top, right top, color-stop(0%, #f36f43), color-stop(33%, #a74024), color-stop(68%, #97a73a), color-stop(100%, #a4dbda));
    background: -webkit-linear-gradient(left, #a4dbda 0%, #97a73a 33%, #a74024 68%, #f36f43 100%);
    background: -o-linear-gradient(left, #a4dbda 0%, #97a73a 33%, #a74024 68%, #f36f43 100%);
    background: -ms-linear-gradient(left, #a4dbda 0%, #97a73a 33%, #a74024 68%, #f36f43 100%);
    background: linear-gradient(to right, #a4dbda 0%, #97a73a 33%, #a74024 68%, #f36f43 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f36f43', endColorstr='#a4dbda', GradientType=1 );
}

.rs-range::-webkit-slider-thumb {
  box-shadow: none;
  border: 2px solid #f74d00;
    box-shadow: 0 3px 9px rgba(0, 0, 0, 0.32);
  height: 23px;
  width: 23px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -5px;
}
.rs-range::-moz-range-thumb {
  box-shadow: none;
  border: 2px solid #f74d00;
    box-shadow: 0 3px 9px rgba(0, 0, 0, 0.32);
  height: 23px;
  width: 23px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -5px;
}
.rs-range::-moz-focus-outer {
  border: 0;
}

.rs-label {
position: relative;
    transform-origin: center center;
    display: block;
    max-width: 100px;
    background: transparent;
    border-radius: 3px;
    line-height: 30px;
    text-align: center;
    font-weight: bold;
    box-sizing: border-box;
    margin-top: 20px;
    margin-left: -38px;
    left: attr(value);
    color: #fff;
    font-style: normal;
    font-weight: normal;
    line-height: normal;
    font-size: 12px;
    background: #f74d00;    
    padding: 5px 0;
}
    ._t-ms-a-da{
    }
    .range-slider{
        position: initial !important;
    }
    .range-slider__value{
       position: absolute;
        left: 60px;
       /*  top: -32px;
        padding: 0 19px;
        left: -25px;
        border: 1px solid red;
        text-align: center;*/
    }	
input[type="radio"]{
	display: none;
}
.calc_otr h1
{
  margin: 10px 0;
}
.nt-sbt input[type=button]
{
  font-size: 15px;
}
.fpc_outer
{
	float: left;
	width: 100%;
}
@media (min-width: 992px)
{
    ._3cc-s0 {
        padding: 0 20px;
    }
}
@media (max-width: 991px)
{
    ._3cc-s0 {
        padding: 0 20px;
    }
}
@media(max-width: 767px)
{
  ._3cc-s0 ._nbr {
    margin: -31px 0;
  }
}
@media (max-width: 575px)
{
    ._t-ms-w ._t-ms-a ._t-ms-a-hg h1 {
        font-size: 20px;
    }
    #tst_cycl img {
    width: 395px;
    margin: -30px auto;
    }
}
@media(max-width: 480px)
{
    .ext_radio ul li {
    display: inline-block;
    float: left;
    margin: 10px 0;
    width: 50%;
    }
    .ext_radio ul {
    text-align: left;
    }
    .rs-label
    {
        margin-top: 87px;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 input[type=button]
    {
        width: 92px;
        padding: 6px 19px;
        font-size: 15px;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 ._ms_pbtn:after {
        left: 11px;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 ._ms_nbtn:after
    {
        height: 14px;
        right: 9px;
        top: 5px;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 ._ms_pbtn
    {
        margin: 0;
    }
    #fpc_platform_dv img {
        width: 47px !important;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-fr ul li input[type=checkbox]+label {
        width: 83px;
        height: 83px;
    }
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-da2 input[type=submit] {
        width: 111px;
        padding: 4px 23px;
    }

}
@media(max-width: 420px)
{
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-fr ul li input[type=checkbox]+label {
        width: 77px;
        height: 77px;
    }
}
@media(max-width: 380px)
{
    ._t-ms-w ._t-ms-a ._t-ms-a-d ._t-ms-a-da ._t-ms-a-fr ul li input[type=checkbox]+label {
        width: 83px;
        height: 83px;
    }
}

</style>

    <section class="_t-ms-w" id="test_cost_form">
        <form method="post" id="fpc_form">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="_t-ms-a">
                            <div class="_t-ms-a-hg">
                                <h1>Test Effort Calculation</h1>
                            </div>

                            <div id="fpc_domain_dv">
                                <div class="_t-ms-a-c">
                                    <ul>
                                        <p>1/5</p>
                                        <li class="active"><span></span></li>
                                        <li><span></span></li>
                                        <li><span></span></li>
                                        <li><span></span></li>
                                        <li><span></span></li>
                                    </ul>
                                </div>
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg">
                                        <h3>Please mention the domain your software is based on?</h3>
                                    </div>
                                    <div class="_3cc-s0">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio01" name="category[]" value="Banking and finance" />
                                                <label for="radio01">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-a">
                                                        </div>
                                                        <p>Banking and finance</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio02" name="category[]" value="E-commerce" />
                                                <label for="radio02">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-b">
                                                        </div>
                                                        <p>E-commerce</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio03" name="category[]" value="E-learning" />
                                                <label for="radio03">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-c">
                                                        </div>
                                                        <p>E-learning</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio04" name="category[]" value="Healthcare" />
                                                <label for="radio04">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-d">
                                                        </div>
                                                        <p>Healthcare</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio05" name="category[]" value="Insurance" />
                                                <label for="radio05">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-e">
                                                        </div>
                                                        <p>Insurance</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="transport_id" name="category[]" value="IT and consulting" />
                                                <label for="transport_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-tr" id="extra_catt">
                                                        </div>
                                                        <p>IT and consulting</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="_3cc-f0">
                                                <input type="radio" id="fooddelivery_id" name="category[]" value="Retail" />
                                                <label for="fooddelivery_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-fdel" id="extra_catt">
                                                        </div>
                                                        <p>Retail</p>
                                                    </div>
                                                </label>
                                            </div>
                                             <div class="_3cc-f0">
                                                <input type="radio" id="radio06" name="category[]" value="News and publication" />
                                                <label for="radio06">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-f">
                                                        </div>
                                                        <p>News and publication</p>
                                                    </div>
                                                </label>
                                            </div>
                                            
                                            
                                        </div>
                                        <div class="col-sm-6">
                                            
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio07" name="category[]" value="Telecom" />
                                                <label for="radio07">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-g">
                                                        </div>
                                                        <p>Telecom</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="radio08" name="category[]" value="Automotive industry" />
                                                <label for="radio08">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-h">
                                                        </div>
                                                        <p>Automotive industry</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0">
                                                <input type="radio" id="donation_id" name="category[]" value="Travel and Logistics" />
                                                <label for="donation_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-don">
                                                        </div>
                                                        <p>Travel and Logistics</p>
                                                    </div>
                                                </label>
                                            </div>

                                            
                                            <div class="_3cc-f0">
                                                <input type="radio" id="realestate_id" name="category[]" value="Manufacturing" />
                                                <label for="realestate_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-rest" id="extra_catt">
                                                        </div>
                                                        <p>Manufacturing</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="_3cc-f0">
                                                <input type="radio" id="productivity_id" name="category[]" value="GPS" />
                                                <label for="productivity_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-pdt" id="extra_catt">
                                                        </div>
                                                        <p>GPS</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="_3cc-f0">
                                                <input type="radio" id="fashion_id" name="category[]" value="Fashion" />
                                                <label for="fashion_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-pdt" id="extra_catt">
                                                        </div>
                                                        <p>Fashion</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="_3cc-f0" id="3b_txt">
                                                <input type="radio" id="sugg_id" name="category[]" value="Others" />
                                                <label for="sugg_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-i" style="background-image: none;">
                                                        </div>
                                                        <p>Others</p>
                                                    </div>
                                                </label>
                                                <input type="text" name="text01" id="text01" style="display: none;" placeholder="Type your specific domain" />
                                            </div>

                                            <!-- <div class="_3cc-f0">
                                                <input type="radio" id="Gaming_id" name="category[]" value="Gaming" />
                                                <label for="Gaming_id">
                                                    <div class="_rbf">
                                                        <div class="_ricon _ricon-pdt" id="extra_catt">
                                                        </div>
                                                        <p>Gaming</p>
                                                    </div>
                                                </label>
                                            </div> -->

                                        </div>
                                    </div>
                                </div>
                                <div class="_t-ms-a-da2">
                                            <div class="_ms_nbtn">
                                                <input type="button" value="NEXT" id="fpc_nxt_btn0">
                                            </div>

                                        </div>
                                </div>
                            </div>
                            
                            <div id="fpc_screens_dv" style="display: none;">
                                <div class="_t-ms-a-c">
                                    <ul>
                                        <p>2/5</p>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li><span></span></li>
                                        <li><span></span></li>
                                        <li><span></span></li>
                                        
                                    </ul>
                                </div>
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg">
                                        <h3>How many screens do you want to test?</h3>
                                    </div>
                                    <div class="_t-ms-a-da">
                                        <div class="_t-ms-a-da1">
                                            <input type="number" id="fpc_no_of_screens" name="fpc_no_of_screens" min="1" max="100" value="" placeholder="Type...">
                                        </div>
                                        <div class="_t-ms-a-da2">
                                            <div class="_ms_pbtn">
                                                <input type="button" value="PREV" id="fpc_prv_btn0">
                                            </div>
                                            <div class="_ms_nbtn">
                                                <input type="button" value="NEXT" id="fpc_nxt_btn1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="fpc_ext_interface_dv" style="display: none;">
                                <div class="_t-ms-a-c">
                                    <ul>
                                        <p>3/5</p>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li><span></span></li>
                                        <li><span></span></li>
                                       
                                    </ul>
                                </div>
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg">
                                        <h3>Number Of External Interface</h3>
                                        <p style="margin-top: 6px;">(*How many third party integrations are there?)</p>
                                    </div>
                                    <div class="_t-ms-a-da">
                                        <!-- <div class="_t-ms-a-da1">
                                            <input type="number" id="fpc_no_of_ext_interface" name="fpc_no_of_ext_interface" max="5000" value="" placeholder="Type...">
                                        </div> -->
                                        <div class="ext_radio">
                                            <ul>
                                                <li>
                                                     <input type="radio" id="ext_int_id1" name="ext_int_radio[]" value="0-3">
                                                        <label for="ext_int_id1">0-3</label>
                                                </li>

                                                <li>
                                                      <input type="radio" id="ext_int_id2" name="ext_int_radio[]" value="4">
                                                        <label for="ext_int_id2">4</label>
                                                </li>

                                                <li>
                                                     <input type="radio" id="ext_int_id3" name="ext_int_radio[]" value="5">
                                                    <label for="ext_int_id3">5</label>
                                                </li>

                                                <li>
                                                    <input type="radio" id="ext_int_id4" name="ext_int_radio[]" value="5+">
                                                        <label for="ext_int_id4">5+</label>
                                                </li>
                                            </ul>
                                           
                                         </div>

                                         <div class="_ra-wrap">
                                                <div class="range-slider">
                                                    <span id="rs-bullet" class="rs-label">Simple</span>
                                                    <input id="rs-range-line" class="rs-range" name="fpc_complexity" type="range" value="0" min="0" max="100">
                                                </div>
                                            </div>


                                        <div class="_t-ms-a-da2">
                                            <div class="_ms_pbtn">
                                                <input type="button" value="PREV" id="fpc_prv_btn1">
                                            </div>
                                            <div class="_ms_nbtn">
                                                <input type="button" value="NEXT" id="fpc_nxt_btn2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="fpc_platform_dv" style="display: none;">
                                <div class="_t-ms-a-c">
                                    <ul>
                                        <p>4/5</p>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li><span></span></li>
                                        
                                    </ul>
                                </div>
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg">
                                        <h3>Platform</h3>
                                        <p>(select atleast one option)</p>
                                    </div>
                                    <div class="_t-ms-a-da _ns-p2">
                                        <div class="_t-ms-a-fr">
                                            <ul>
                                                <li>
                                                    <input class="form-check-input" type="checkbox" id="fpc_web_platform" name="fpc_platforms[]" value="Web">
                                                    <label for="fpc_web_platform">
                                                        <img src="<?php bloginfo('stylesheet_directory'); ?>/images/webdessign.png" alt="">
                                                        <p>Web</p>
                                                    </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" value="Mobile" name="fpc_platforms[]" id="fpc_mobile_platform">
                                                    <label for="fpc_mobile_platform">
                                                        <img src="<?php bloginfo('stylesheet_directory'); ?>/images/mobile.png" alt="">
                                                        <p>Mobile</p>
                                                    </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" value="Both" name="fpc_platforms[]" id="fpc_both_platform">
                                                    <label for="fpc_both_platform">
                                                        <img src="<?php bloginfo('stylesheet_directory'); ?>/images/select-all.png" alt="">
                                                        <p>Both</p>
                                                    </label>
                                                </li>
                                            </ul>
                                             
                                        </div>
                                        <div class="_t-ms-a-da2">
                                            <div class="_ms_pbtn">
                                                <input type="button" value="PREV" id="fpc_prv_btn2">
                                            </div>
                                            <div class="_ms_nbtn">
                                                <input type="button" value="NEXT" id="fpc_nxt_btn3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="fpc_complex_dv" style="display: none;">
                                <div class="_t-ms-a-c">
                                    <ul>
                                        <p>5/5</p>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                    </ul>
                                </div>
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg">
                                        <h3>How Complex is the application</h3>
                                        <p>(select atleast one option)</p>
                                    </div>
                                    <div class="_t-ms-a-da">
                                        <div class="_t-ms-a-da1">
                                            <!-- <div class="range-slider" style="position: relative;">
                                              <input class="range-slider__range" type="range" value="0" min="0" max="100" data-toggle="tooltip" data-placement="top" title="Tooltip on top" id="fpc_complexity" name="fpc_complexity" value="0">
                                              <span class="range-slider__value">Simple</span>
                                            </div> -->
                                            <div class="_ra-wrap">
                                                <div class="range-slider">
                                                    <span id="rs-bullet" class="rs-label">Simple</span>
                                                    <input id="rs-range-line" class="rs-range" name="fpc_complexity" type="range" value="0" min="0" max="100">
                                                </div>
                                            </div>
                                            <!-- <div class="range-control">
                                                <input id="inputRange" type="range" min="1" max="100" value="1" data-thumbwidth="1">
                                                <output name="rangeVal">Simple</output>
                                            </div> -->
                                            <!-- <div class="__range __range-step __range-step-popup">
                                                <input value="1" type="range" max="4" min="1" step="1" list="ticks2">
                                                <datalist id="ticks2">
                                                    <option value="1">Simple</option>
                                                    <option value="2">Medium</option>
                                                    <option value="3">Complex</option>
                                                    <option value="4">Very Complex</option>  
                                                </datalist>
                                                <output class="__range-output-square"></output>
                                            </div> -->
                                        </div>
                                        <div class="_t-ms-a-da2">
                                            <div class="_ms_pbtn">
                                                <input type="button" value="PREV" id="fpc_prv_btn3">
                                            </div>
                                            <div class="_ms_nbtn">
                                                <input type="button" value="NEXT" id="fpc_nxt_btn4">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="fpc_cycles_dv" style="display: none;">
                                <div class="_t-ms-a-c">
                                    <ul>
                                        <p>5/5</p>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        <li class="active"><span></span></li>
                                        
                                    </ul>
                                </div>
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg" id="tst_cycl">
                                        <h3>How Many Cycles of Testing is required?</h3>
                                        <img class="img-responsive cycles_tst_img" src="<?php bloginfo('stylesheet_directory'); ?>/images/one-test-cycle.png" alt="image"/>
                                    </div>
                                    <div class="_t-ms-a-da">
                                        <!-- <div class="_t-ms-a-da1">
                                            <input type="number" id="fpc_no_cycles" name="fpc_no_cycles" min="2" max="5" value="" placeholder="Maximum 5 Cycles" value="2">
                                        </div> -->
                                        <div class="ext_radio">
                                            <ul>
                                                <li>
                                                     <input type="radio" id="cycles_id1" name="cycles_radio[]" value="2">
                                                        <label for="cycles_id1">2</label>
                                                </li>

                                                <li>
                                                      <input type="radio" id="cycles_id2" name="cycles_radio[]" value="3">
                                                        <label for="cycles_id2">3</label>
                                                </li>

                                                <li>
                                                     <input type="radio" id="cycles_id3" name="cycles_radio[]" value="4">
                                                    <label for="cycles_id3">4</label>
                                                </li>

                                                <li>
                                                    <input type="radio" id="cycles_id4" name="cycles_radio[]" value="5">
                                                        <label for="cycles_id4">5</label>
                                                </li>
                                            </ul>
                                           
                                         </div>

                                        <div class="_t-ms-a-da2">
                                            <div class="_ms_pbtn">
                                                <input type="button" value="PREV" id="fpc_prv_btn4">
                                            </div>
                                            <div class="_ms_nbtn">
                                                <input type="button" value="NEXT" id="fpc_nxt_btn5">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="fpc_enquiry_dv" style="display: none;">
                                <div class="_t-ms-a-d">
                                    <div class="_t-ms-a-d-hg">
                                        <img src="<?php bloginfo('stylesheet_directory'); ?>/images/open-email.png" alt="" style="width: 100px;" >
                                        <h3>ALMOST DONE</h3>
                                        <p>Let us know where we should send your final estimation</p>
                                    </div>
                                    <div class="_t-ms-a-da">
                                        <div class="_t-ms-a-da1">
                                            <div class="form-group">
                                                <input type="text" placeholder="Name" name="fpc_user_name" required="">
                                            </div>
                                            <div class="form-group">
                                                <input type="email" placeholder="Email" name="fpc_user_email" required="">
                                            </div>
                                            <div class="form-group">
                                                <input id="fpc_user_mob" type="text" placeholder="Phone Number" name="fpc_user_mobile" minlength="7" maxlength="15" pattern="\d*" required="">
                                            </div>
                                            <div class="form-group">
                                                <textarea rows="6" placeholder="Comments" name="fpc_user_message"></textarea>
                                            </div>
                                            <?php if ($user_currencyCode == "INR") { ?>
                                            <div class="form-group" style="display: flex;width: 100%;align-items: center;">
                                                <input type="checkbox" class="form-check-input" id="exampleCheck1" name="sms_check" style="float: left;display: block !important;" checked="checked" value="yes" >
                                                <label class="form-check-label" for="exampleCheck1" style="float: left;width: 75%;">
                                                    <p class="sms_btn_hv" style="float: left;margin: 11px 10px 0;font-weight: bold;">Share cost via SMS</p></label>
                                            </div>  
                                            <?php
}?>
                                        </div>
                                        <div class="_t-ms-a-da2 _mt-1">
                                                <div class="_ms_pbtn">
                                                    <input type="button" value="PREV" id="fpc_prv_btn5">
                                                </div>
                                            <div class="_ms_nbtn fpc_sub_btn">
                                                <input type="submit" value="SUBMIT" id="fpc_nxt_btn6">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="fpc_roi_success" style="color: #31ad08;float: left;width: 100%;margin: 5px 0;"></div>
                            </div>

                            <div id="fpc_result_div" style="display: none;float: left;width: 100%;">
                                <div class="_t-ms-a-d" style="margin: 0;">
                                    <div class="_t-ms-a-d-hg">
                                        <img src="<?php bloginfo('stylesheet_directory'); ?>/images/open-email.png" alt="" style="width: 100px;" >
                                        <h3>Success</h3>
                                        <div id="fpc_ajax_result" style="min-height: .01%;overflow-x: auto;width: 100%;margin-bottom: 15px;overflow-y: hidden;"></div>
                                    </div>
                                    <div class="_t-ms-a-da">
                                        <div class="_t-ms-a-da2 _mt-1">
                                            <div class="_ms_pbtn try_agn">
                                                <input type="button" value="Try Again" id="fpc_try_again">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </form>
    </section>
    <div id="ajax_result">
    	
    </div>
<?php get_footer(); ?>
<script type="text/javascript">
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        // alert("hi");
        $(document).on('click','#fpc_nxt_btn0',function()
        {
            var category_select = $('input[name="category[]"]').serializeArray();
            if(category_select.length == 0){
                alert('please select any one option');
            }else{

                var catsel_val = $('input[name="category[]"]:checked').val();
                if(catsel_val == 'Others')
                {
                    if($("#text01").val() == ''){
                         alert('please fill the field');
                    }else{
                        $("#fpc_screens_dv").show();
                        $("#fpc_domain_dv").hide();
                    }
                }else
                {
                    $("#fpc_screens_dv").show();
                    $("#fpc_domain_dv").hide();
                }
                
            }
        });

        $(document).on('click','input[name="category[]"]',function()
        {
            var cat_field = $(this).attr('id');
            // var txt_field = $("#text01").val();
            if((cat_field == 'sugg_id') || (cat_field == 'text01'))
            {
                $("#text01").css("display","block");
            }
            else
            {
                $("#text01").css("display","none");
            }
        }); 

        $(document).on('click','#fpc_nxt_btn1',function(){
            var fpc_no_of_screens = $("#fpc_no_of_screens").val();
            if((fpc_no_of_screens == "") || (fpc_no_of_screens <= 0)){
                alert("Please Fill Valid Input");
            }
            else if(fpc_no_of_screens >= 101){
				alert("Please enter a value below 100");
			}
            else{
                // alert(fpc_no_of_screens);
                $("#fpc_screens_dv").hide();
                $("#fpc_ext_interface_dv").show();
            }           
        });

        $(document).on('click','#fpc_nxt_btn2',function(){
            // var fpc_no_of_ext_interface = $("#fpc_no_of_ext_interface").val();

            // if(fpc_no_of_ext_interface == "" || fpc_no_of_ext_interface < 0){
            //     alert("Please Fill Valid Input");
            // }else{
            //     $("#fpc_ext_interface_dv").hide();
            //     $("#fpc_platform_dv").show();
            // }  
            var ext_inp_dv = $('input[name="ext_int_radio[]"]').serializeArray();
            if(ext_inp_dv.length == 0)
            {
                alert("Please select any one option");
            }else{
                $("#fpc_ext_interface_dv").hide();
                $("#fpc_platform_dv").show();
            }       
        });

        $('input[name="ext_int_radio[]"]').change(function()
        {
            // alert("complex level");
            var ext_int_level = $('input[name="ext_int_radio[]"]:checked').val();
            // alert(ext_int_level);
            var slider_val = $("#rs-range-line").val();
            var bullet_val = $("#rs-bullet").val();
            if((ext_int_level == "0-3"))
            {
                // alert(slider_val);
                rangeBullet.innerHTML = "Simple";
                $("#rs-range-line").val(24);
                var bulletPosition = (24 /100);
                rangeBullet.style.left = (bulletPosition * 93) + "%";
                
            }
            else if((ext_int_level == "4"))
            {
                rangeBullet.innerHTML = "Medium";
                $("#rs-range-line").val(49);
                var bulletPosition = (49 /100);
                rangeBullet.style.left = (bulletPosition * 93) + "%";
            }
            else if((ext_int_level == "5"))
            {
                rangeBullet.innerHTML = "Complex";
                $("#rs-range-line").val(74);
                var bulletPosition = (74 /100);
                rangeBullet.style.left = (bulletPosition * 93) + "%";
            }
            else
            {
                rangeBullet.innerHTML = "Very Complex";
                $("#rs-range-line").val(100);
                var bulletPosition = (100/100);
                rangeBullet.style.left = (bulletPosition * 93) + "%";
            }
        });

        $(document).on('click','#fpc_nxt_btn3',function(){
            var platform_select = $('input[name="fpc_platforms[]"]').serializeArray();
            if(platform_select.length == 0){
                alert("Select Atleast One Option");
            }else{
                $("#fpc_platform_dv").hide();
                // $("#fpc_complex_dv").show();
                $("#fpc_cycles_dv").show();
            }           
        });

        $(document).on('click','input[name="fpc_platforms[]"]',function(){
            var platform_val = $(this).val();
            if(platform_val == 'Both'){
                if($("#fpc_both_platform").is(':checked')){
                    $("#fpc_web_platform").prop('checked', true);
                    $("#fpc_mobile_platform").prop('checked', true);
                }else{
                    $("#fpc_web_platform").prop('checked', false);
                    $("#fpc_mobile_platform").prop('checked', false);
                }
            }else{
                if(($("#fpc_web_platform").is(':checked')) && ($("#fpc_mobile_platform").is(':checked'))){
                    $("#fpc_both_platform").prop('checked',true);
                }else{
                    $("#fpc_both_platform").prop('checked',false);
                }
            }      
        });

        $(document).on('click','#fpc_nxt_btn4',function(){      
            $("#fpc_complex_dv").hide();
            // $("#fpc_enquiry_dv").show(); 
            $("#fpc_cycles_dv").show();
        });

        $(document).on('click','#fpc_nxt_btn5',function(){  
        // var fpc_no_cycles = $("#fpc_no_cycles").val();
        //     if((fpc_no_cycles <= 5) && (fpc_no_cycles > 1)){
        //         $("#fpc_cycles_dv").hide();
        //         $("#fpc_enquiry_dv").show();
        //     }else{
        //         alert("Cycles Should Be Within 2 to 5");
        //     }         
            var no_cycles_req = $('input[name="cycles_radio[]"]').serializeArray();
            if(no_cycles_req.length == 0)
            {
                alert("Please select any one option");
            }else
            {
                $("#fpc_cycles_dv").hide();
                $("#fpc_enquiry_dv").show();
            }  
        });

        //Form Submission

        $("#fpc_form").submit(function(e){
            e.preventDefault();
            // $("#fpc_test_cost_form").hide();
            $("#fpc_roi_success").show();
            $.ajax({
                beforeSend:function(){
                    $('input[type="submit"]').prop('disabled', true);
                    $('input[type="submit"]').css('background','#ffffff');
                    $('input[type="submit"]').css('color','#ef2c1a');
                    $('input[type="submit"]').css('cursor','not-allowed');
                    $("#fpc_roi_success").html('Sending...');
                },
                type:'POST',
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                data: $('#fpc_form').serialize() + "&action=fpc_cal",
                // dataType:'json',
                success:function(res){
                    var result = $.trim(res);
                    var req_res = JSON.parse(result);
                    $("#fpc_roi_success").html('');
                    window.scroll(0,250);
                    
                    $("#fpc_result_div").show();
					$("#fpc_ajax_result").html(req_res['status']);
                    $("#fpc_enquiry_dv").hide();
            }
        });
        });

        // Backward
        $(document).on('click','#fpc_prv_btn0',function(){
            $("#fpc_screens_dv").hide();
            $("#fpc_domain_dv").show();
        });
        $(document).on('click','#fpc_prv_btn1',function(){
            $("#fpc_ext_interface_dv").hide();
            $("#fpc_screens_dv").show();
        });

        $(document).on('click','#fpc_prv_btn2',function(){
            $("#fpc_ext_interface_dv").show();
            $("#fpc_platform_dv").hide();
        });

        $(document).on('click','#fpc_prv_btn3',function(){
            $("#fpc_platform_dv").show();
            $("#fpc_complex_dv").hide();
        });

        $(document).on('click','#fpc_prv_btn4',function(){
            // $("#fpc_complex_dv").show();
            $("#fpc_cycles_dv").hide();
            $("#fpc_platform_dv").show();
        });
        $(document).on('click','#fpc_prv_btn5',function(){
            $("#fpc_cycles_dv").show();
            $("#fpc_enquiry_dv").hide();
        });

        $(document).on('click',"#fpc_try_again",function(){
            $("#fpc_form").trigger("reset");
            $("#fpc_ajax_result").html('');
            // $("#final_user_result_div").html('');
             // $("#final_user_result_div").hide();
            $("#rs-bullet").css('left','0%');
            $("#rs-bullet").text('Simple');
            window.scroll(0,0);
            $("#fpc_result_div").hide();
            $("#fpc_domain_dv").show();
        });

    });
</script>
<script>

var rangeSlider = document.getElementById("rs-range-line");
var rangeBullet = document.getElementById("rs-bullet");

rangeSlider.addEventListener("input", showSliderValue, false);

function showSliderValue() {
    if(rangeSlider.value < 25){
        rangeBullet.innerHTML = "Simple";
        document.getElementById("ext_int_id1").checked = true;
    }else if(rangeSlider.value < 50){
        rangeBullet.innerHTML = "Medium";
        document.getElementById("ext_int_id2").checked = true;
    }else if(rangeSlider.value < 75){
        rangeBullet.innerHTML = "Complex";
        document.getElementById("ext_int_id3").checked = true;
    }else if(rangeSlider.value <= 100){
        rangeBullet.innerHTML = "Very Complex";
        document.getElementById("ext_int_id4").checked = true;
    }
  
  var bulletPosition = (rangeSlider.value /rangeSlider.max);
  rangeBullet.style.left = (bulletPosition * 93) + "%";
}
</script>
<?php
$this_user = wp_get_current_user();
if (!in_array("administrator", $this_user->roles)) {
?>
<script type="text/javascript">
    $(document).bind("contextmenu",function(e) {
        e.preventDefault();
    }); 
    $(document).keydown(function(e){
        if(e.which === 123){
           return false;
        }
    });
    $(document).keydown(function(e) {
        if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode === 117)) {
            alert('not allowed');
            return false;
        }
    });
</script>
<?php
}
?>