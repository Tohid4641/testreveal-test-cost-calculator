<?php
add_action('wp_ajax_fpc_cal', 'fpc_cal');
add_action('wp_ajax_nopriv_fpc_cal', 'fpc_cal');

function fpc_cal()
{
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
  $PublicIP = $ipaddress;
  $location_json = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $PublicIP . "");
  if (($location_json !== false) && (!empty($location_json))) {
    $decode_location = json_decode($location_json, true);
    $user_location = '';
    if ((isset($decode_location['geoplugin_request'])) && ($decode_location['geoplugin_request'] != '') && ($decode_location['geoplugin_request'] != NULL)) {
      $user_ip = $decode_location['geoplugin_request'];
    }
    else {
      $user_ip = '';
    }

    if ((isset($decode_location['geoplugin_city'])) && ($decode_location['geoplugin_city'] != '') && ($decode_location['geoplugin_city'] != NULL)) {
      $user_city = $decode_location['geoplugin_city'];
      $user_location .= $user_city . ', ';
    }
    else {
      $user_city = '';
    }

    if ((isset($decode_location['geoplugin_region'])) && ($decode_location['geoplugin_region'] != '') && ($decode_location['geoplugin_region'] != NULL)) {
      $user_region = $decode_location['geoplugin_region'];
      $user_location .= $user_region . ', ';
    }
    else {
      $user_region = '';
    }

    if ((isset($decode_location['geoplugin_countryName'])) && ($decode_location['geoplugin_countryName'] != '') && ($decode_location['geoplugin_countryName'] != NULL)) {
      $user_CountryName = $decode_location['geoplugin_countryName'];
      $user_location .= $user_CountryName;
    }
    else {
      $user_CountryName = '';
    }

    if ((isset($decode_location['geoplugin_currencyCode'])) && ($decode_location['geoplugin_currencyCode'] != '') && ($decode_location['geoplugin_currencyCode'] != NULL)) {
      $user_currencyCode = $decode_location['geoplugin_currencyCode'];
      $currencyCode_set = 1;
    }
    else {
      $user_currencyCode = '';
      $currencyCode_set = 0;
    }

    if ((isset($decode_location['geoplugin_countryCode'])) && ($decode_location['geoplugin_countryCode'] != '') && ($decode_location['geoplugin_countryCode'] != NULL)) {
      $user_countryCode = $decode_location['geoplugin_countryCode'];
      $countryCode_set = 1;
    }
    else {
      $user_countryCode = '';
      $countryCode_set = 0;
    }

    if ((isset($decode_location['geoplugin_currencyConverter'])) && ($decode_location['geoplugin_currencyConverter'] != '') && ($decode_location['geoplugin_currencyConverter'] != NULL)) {
      $user_geoplugin_currencyConverter = $decode_location['geoplugin_currencyConverter'];
      $currency_set = 1;
    }
    else {
      $user_geoplugin_currencyConverter = '';
      $currency_set = 0;
    }

    if ((isset($decode_location['geoplugin_currencySymbol_UTF8'])) && ($decode_location['geoplugin_currencySymbol_UTF8'] != '') && ($decode_location['geoplugin_currencySymbol_UTF8'] != NULL)) {
      $user_currencysymbol = $decode_location['geoplugin_currencySymbol_UTF8'];
      $curr_sym_set = 1;
    }
    else {
      $user_currencysymbol = '';
      $curr_sym_set = 0;
    }

    if ((isset($decode_location['geoplugin_continentName'])) && ($decode_location['geoplugin_continentName'] != '') && ($decode_location['geoplugin_continentName'] != NULL)) {
      $user_ContinentName = $decode_location['geoplugin_continentName'];
    }
    else {
      $user_ContinentName = 'North America';
    }
  }
  else {
    $user_location = "Couldn't Find Location";
    $currencyCode_set = 0;
    $currency_set = 0;
    $location_set = 0;
    $user_city = "";
    $user_region = "";
    $user_CountryName = "";
    $user_countryCode = "";
  }
  if (trim($user_location) == '') 
{
    $user_location = "Couldn't Find Any Location";
    $currencyCode_set = 0;
    $currency_set = 0;
    $location_set = 0;
    $user_city = "";
    $user_region = "";
    $user_CountryName = "";
    $user_countryCode = "";
  }
  if (in_array('Others', $_POST['category'])) {
    $dis_category = $_POST['text01'];
    $db_category = $_POST['text01'];
  }
  else {
    $dis_category = implode(", ", $_POST['category']);
    $db_category = $dis_category;
  }

  $mail_title = "TEST EFFORT CALCULATOR";
  $no_of_screens = (int)$_POST["fpc_no_of_screens"];

  if ($_POST["ext_int_radio"][0] == "0-3") {
    $no_ext_interface = 3;
    $no_ext_interface_text = $_POST["ext_int_radio"][0];
    $complexity_arr = 24;
  } 
  elseif ($_POST["ext_int_radio"][0] == "4") {
    $no_ext_interface = 4;
    $no_ext_interface_text = $_POST["ext_int_radio"][0];
    $complexity_arr = 49;
  }
  elseif ($_POST["ext_int_radio"][0] == "5") {
    $no_ext_interface = 5;
    $no_ext_interface_text = $_POST["ext_int_radio"][0];
    $complexity_arr = 74;
  }
  elseif ($_POST["ext_int_radio"][0] == "5+") {
    $no_ext_interface = 6;
    $no_ext_interface_text = $_POST["ext_int_radio"][0];
    $complexity_arr = 100;
  }


  if (isset($_POST["kind_of_testing"])) {
    $mail_title = "Mobile App Testing Calculator";
    $estimation_for = "mobile_app_testing";
    $kind_of_testing = $_POST["kind_of_testing"];
    $kind_count = count($kind_of_testing);

    $dis_kind = implode(', ', $kind_of_testing);
    if (in_array('Functional Testing', $kind_of_testing)) {
      $kind_count = $kind_count - 1;
    }

    if ($kind_count != 0) {
      if (in_array('Manual Testing', $kind_of_testing)) {
        $kind_count = $kind_count - 1;
      }
    }

    $no_ext_interface = $no_ext_interface + $kind_count;
  }
  $platforms_arr = $_POST["fpc_platforms"];
  $no_of_cycles = (int)$_POST["cycles_radio"][0];

  if (in_array('Both', $platforms_arr)) {
    $key = array_search('Both', $platforms_arr);
    if (false !== $key) {
      unset($platforms_arr[$key]);
    }
    $no_of_platforms = 1;
    $dis_platfrom = implode(", ", $platforms_arr);
  }
  else {  
    $no_of_platforms = count($platforms_arr);
    $no_of_platforms = 0.5;
    $dis_platfrom = implode(", ", $platforms_arr);
  }


  if ($complexity_arr < 25) {
    $complex_text = "Simple";
    $complexity_value = 2;
    $complexity_factor = 1;
  }
  elseif ($complexity_arr < 50) {
    $complex_text = "Medium";
    $complexity_value = 3;
    $complexity_factor = 1.5;
  }
  elseif ($complexity_arr < 75) {
    $complex_text = "Complex";
    $complexity_value = 4;
    $complexity_factor = 2;
  }
  elseif ($complexity_arr <= 100) {
    $complex_text = "Very Complex";
    $complexity_value = 5;
    $complexity_factor = 2.5;
  }
  $functional_val = 14 * $complexity_value;
  $complexity_adjustment_factor = 0.65 + (0.01 * $functional_val);
  $unadjusted_functional_point = ($complexity_value * $no_of_screens) + ($complexity_value * $no_ext_interface) + ($complexity_value * $no_of_platforms);
  $functional_point = $unadjusted_functional_point * $complexity_adjustment_factor;
  $efforts_in_hr = $complexity_value * $functional_point;
  $efforts_in_day = $efforts_in_hr / 8;
  $efforts_in_month = $efforts_in_day / 20;
  $tot_efforts_in_hr = $efforts_in_hr * $no_of_platforms;
  $tot_efforts_in_day = $tot_efforts_in_hr / 8;
  $tot_efforts_in_month = $tot_efforts_in_day / 20;

  $test_env_setup = (28 / 50) * ($complexity_factor * 5.05);
  $test_data_preparation = (28 / 50) * ($complexity_factor * 5.05);
  $test_config_setup = (28 / 50) * ($complexity_factor * 5.05); //---------------------------------------------------
  $factor = 1.2;
  $one_minute = 1;
  $one_minute_in_hrs = 1 / 60;

  if ($complexity_arr < 25) {
    $factor_in_min = 4;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;
  }
  elseif ($complexity_arr < 50) {
    $factor_in_min = 6;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;
  }
  elseif ($complexity_arr < 75) {
    $factor_in_min = 8;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;
  }
  elseif ($complexity_arr <= 100) {
    $factor_in_min = 10;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min; 
}
  $defect_reporting = $one_minute_in_hrs * 15;
  $retesting_ten_mins = 0.166666666666667;
  $testcases = pow($functional_point, $factor);
  $total_time_in_hrs = $testcases * $factor_in_hrs;
  $total_time_defect_reporting = ($testcases / 2) * $defect_reporting;
  $total_effort_for_test_execution_cy1 = $total_time_defect_reporting + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy1 = ($testcases / 2) * $retesting_ten_mins;
  $total_time_defect_reporting_cy2 = ($testcases / 4) * $defect_reporting;
  $total_effort_for_test_execution_cy2 = $total_effort_for_test_execution_cy1;
  //=========Cycle 3============  $total_time_defect_reporting_cy3 = ($testcases / 4) * $defect_reporting;  $total_effort_for_test_execution_cy3 = $total_time_defect_reporting_cy3 + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy3 = ($retesting_testcases_failed_after_cy1 / 4) * $retesting_ten_mins;
  //=========Cycle 4 ============  $total_time_defect_reporting_cy4 = ($testcases / 8) * $defect_reporting;  $total_effort_for_test_execution_cy4 = $total_time_defect_reporting_cy4 + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy4 = ($retesting_testcases_failed_after_cy1 / 8) * $retesting_ten_mins;
  //=========Cycle 5 ==========  $total_time_defect_reporting_cy5 = ($testcases / 16) * $defect_reporting;  $total_effort_for_test_execution_cy5 = $total_time_defect_reporting_cy5 + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy5 = ($retesting_testcases_failed_after_cy1 / 16) * $retesting_ten_mins;
  $final_mail_result = '';
  $final_user_result = '<table class="table" style="border: 1px solid rgb(238, 234, 234);">
                        <thead>
                          <tr bgcolor="#dcdcdc">
                            <th style="vertical-align:middle;text-align: center;">Cycle</th>
                            <th style="vertical-align:middle;text-align: center;">Total Effort for Defect Reporting (in hrs)</th>
                            <th style="vertical-align:middle;text-align: center;">Total effort for Test Execution (in hrs)</th>
                            <th style="vertical-align:middle;text-align: center;">Retesting of Test Case failed (in hrs)</th>
                          </tr>
                        </thead>
                      <tbody>';


  
$col_tot_exe = 0;
  $col_tot_defect = 0;
  $col_tot_retest = 0;
  $col_prepare = 0;
  $divider = round(($no_of_cycles * (5 - $complexity_factor)), 2);
  $prepare_for_each_cycle = round((($tot_efforts_in_hr + $test_env_setup + $test_data_preparation + $test_config_setup) / $divider), 2);
  if ($no_of_cycles >= 1) {
    $final_retesting = $retesting_testcases_failed_after_cy1;
    $final_defect_reporting = $total_time_defect_reporting;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy1;

    $col_tot_exe = $col_tot_exe + $final_total_effort_text_excution;
    $col_tot_defect = $col_tot_defect + $final_defect_reporting;
    $col_tot_retest = $col_tot_retest + $final_retesting;
    $col_prepare = $col_prepare + $prepare_for_each_cycle;

    $final_user_result .= '<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">1</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_defect_reporting, 2) . '</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_total_effort_text_excution, 2) . '</td>
                          <td style="text-align: center;">' . round($final_retesting, 2) . '</td>
                        </tr>';
    $final_mail_result .= '<tr>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">1</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $prepare_for_each_cycle . '</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_total_effort_text_excution, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_defect_reporting, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_retesting, 2) . '</h3>           
                                </td>
                            </tr>';
  }
  if ($no_of_cycles >= 2) {
    $final_retesting = $retesting_testcases_failed_after_cy3;
    $final_defect_reporting = $total_time_defect_reporting_cy2;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy2;

    $col_tot_exe = $col_tot_exe + $final_total_effort_text_excution;
    $col_tot_defect = $col_tot_defect + $final_defect_reporting;
    $col_tot_retest = $col_tot_retest + $final_retesting;
    $col_prepare = $col_prepare + $prepare_for_each_cycle;

    $final_user_result .= '<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">2</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_defect_reporting, 2) . '</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_total_effort_text_excution, 2) . '</td>
                          <td style="text-align: center;">' . round($final_retesting, 2) . '</td>
                        </tr>';
    $final_mail_result .= '<tr style="background: #efefef;">
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">2</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $prepare_for_each_cycle . '</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_total_effort_text_excution, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_defect_reporting, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_retesting, 2) . '</h3>           
                                </td>
                            </tr>';
  }
  if ($no_of_cycles >= 3) {
    $final_retesting = $retesting_testcases_failed_after_cy4;
    $final_defect_reporting = $total_time_defect_reporting_cy3;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy3;

    $col_tot_exe = $col_tot_exe + $final_total_effort_text_excution;
    $col_tot_defect = $col_tot_defect + $final_defect_reporting;
    $col_tot_retest = $col_tot_retest + $final_retesting;
    $col_prepare = $col_prepare + $prepare_for_each_cycle;

    $final_user_result .= '<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">3</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_defect_reporting, 2) . '</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_total_effort_text_excution, 2) . '</td>
                          <td style="text-align: center;">' . round($final_retesting, 2) . '</td>
                        </tr>';
    $final_mail_result .= '<tr>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">3</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $prepare_for_each_cycle . '</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_total_effort_text_excution, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_defect_reporting, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_retesting, 2) . '</h3>           
                                </td>
                            </tr>';
  }
  if ($no_of_cycles >= 4) {
    $final_retesting = $retesting_testcases_failed_after_cy5;
    $final_defect_reporting = $total_time_defect_reporting_cy4;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy4;

    $col_tot_exe = $col_tot_exe + $final_total_effort_text_excution;
    $col_tot_defect = $col_tot_defect + $final_defect_reporting;
    $col_tot_retest = $col_tot_retest + $final_retesting;
    $col_prepare = $col_prepare + $prepare_for_each_cycle;

    $final_user_result .= '<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">4</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_defect_reporting, 2) . '</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_total_effort_text_excution, 2) . '</td>
                          <td style="text-align: center;">' . round($final_retesting, 2) . '</td>
                        </tr>';
    $final_mail_result .= '<tr style="background: #efefef;">
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">4</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $prepare_for_each_cycle . '</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_total_effort_text_excution, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_defect_reporting, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_retesting, 2) . '</h3>           
                                </td>
                            </tr>';
  }
  if ($no_of_cycles >= 5) {
    $final_retesting = $retesting_testcases_failed_after_cy5 / 2;
    $final_defect_reporting = $total_time_defect_reporting_cy5;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy5;

    $col_tot_exe = $col_tot_exe + $final_total_effort_text_excution;
    $col_tot_defect = $col_tot_defect + $final_defect_reporting;
    $col_tot_retest = $col_tot_retest + $final_retesting;

    $col_prepare = $col_prepare + $prepare_for_each_cycle;

    $final_user_result .= '<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">5</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_defect_reporting, 2) . '</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($final_total_effort_text_excution, 2) . '</td>
                          <td style="text-align: center;">' . round($final_retesting, 2) . '</td>
                        </tr>';

    $final_mail_result .= '<tr>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">5</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $prepare_for_each_cycle . '</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_total_effort_text_excution, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_defect_reporting, 2) . '</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($final_retesting, 2) . '</h3>           
                                </td>
                            </tr>';
  }

  $final_user_result .= '<tr bgcolor="#dcdcdc">
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">Total</td>                        
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($col_tot_exe, 2) . '</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">' . round($col_tot_defect, 2) . '</td>
                          <td style="text-align: center;">' . round($col_tot_retest, 2) . '</td>
                        </tr>';
  $final_user_result .= '</tbody>
                  </table>';

  $final_mail_result .= '<tr style="background: #ef4d30;">
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">Total</h3>           
                          </td>
                          <td class="heding-table" align="center">
                            <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">' . round($col_prepare, 2) . '</h3>
                          </td>
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">' . round($col_tot_exe, 2) . '</h3>           
                          </td>
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">' . round($col_tot_defect, 2) . '</h3>           
                          </td>
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">' . round($col_tot_retest, 2) . '</h3>           
                          </td>
                      </tr>';

  $mail_currency = "USD";
  $mail_currency_sym = "$";
  $timeline_value = round(((($col_tot_exe + $col_tot_defect + $col_tot_retest + $col_prepare) / 8) / 20), 2);
  $total_effort_mail = round(($col_tot_exe + $col_tot_defect + $col_tot_retest + $col_prepare), 2);
  $total_effort_mail_display = '<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">' . $total_effort_mail . ' ' . '<B style="font-size: 19px; margin-left: -18px;color: #585858;font-weight: 500;">&nbsp; Hrs.</B></h2>';
  $pri_api_url = "https://softbreaksapi.azurewebsites.net/api/JobPosts/MarketPriceForJobs";


  
$pri_data = array(
    "SkillID" => 28,
);
  $ch = curl_init($pri_api_url);
  $payload = json_encode($pri_data);


  
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $price_result = curl_exec($ch);
  curl_close($ch);
  $pri_res = json_decode($price_result, true);
  if (($pri_res["JobSkillsList"][0]["MarketPriceRange"]["MinRange"] != "") && ($pri_res["JobSkillsList"][0]["MarketPriceRange"]["MaxRange"] != "")) {
    $min_cost = $pri_res["JobSkillsList"][0]["MarketPriceRange"]["MinRange"];
    $max_cost = $pri_res["JobSkillsList"][0]["MarketPriceRange"]["MaxRange"];
  }
  else {
    $min_cost = 3.78;
    $max_cost = 13.33;
  }
  $total_cost_min_db = round(($total_effort_mail * $min_cost), 2);
  $total_cost_max_db = round(($total_effort_mail * $max_cost), 2);
  $total_cost_min = round(($total_effort_mail * $min_cost), 2);
  $total_cost_max = round(($total_effort_mail * $max_cost), 2);
  if (($currency_set == 1) && ($currencyCode_set == 1)) 
{
    $total_cost_min = round($total_cost_min_db * $user_geoplugin_currencyConverter);
    $total_cost_max = round($total_cost_max_db * $user_geoplugin_currencyConverter);
    if ($curr_sym_set == 1) {
      $mail_currency_sym = $user_currencysymbol;
    }

    $mail_currency = $user_currencyCode;
  }
  $grandtotalEfforts = '';
  $category = '';
  $dis_kind = '';
  $effort_details = '';
  $cost_range = number_format($total_cost_min) . ' - ' . number_format($total_cost_max);
  $mail_currency_sym = "";
  if ($mail_currency_sym != '') {
    $mail_total_cost_display = '<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">' . $mail_currency_sym . number_format($total_cost) . '</h2>';
  }
  else {
    $mail_total_cost_display = '<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">' . $cost_range . ' ' . '<B style="font-size: 19px; margin-left: -30px;color: #585858;font-weight: 500;">&nbsp; ' . $mail_currency . '</B></h2>';

    $user_total_cost_display = '<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">' . $cost_range . ' ' . '<B style="font-size: 19px; margin-left: -18px;color: #585858;font-weight: 500;">&nbsp; ' . $mail_currency . '</B></h2>';
  }
  $db_country_cost = number_format($total_cost_min) . ' ' . $mail_currency . ' - ' . number_format($total_cost_max) . ' ' . $mail_currency;
  $exact_result = '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#000000;font-size:14px;font-family:Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;">
    <tr>
      <td style="padding: 10px 0 10px 0;">

        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;font-family:Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;box-shadow: 0 2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)!important;">
          <tbody>
            <tr>
              <td>
                <table>
                  <tbody>
                    <tr>
                      <td></td>
                    </tr>
                    <tr>
                      <td></td>
                      <tr>
                        <td style="padding: 0px 17px;">
                          <a href="#"><img src="' . get_template_directory_uri() . '/images/logo1.png" alt="Thank you for trusting us to help you find a solution" width="170" height="50"
                            style="display: block;"></a>
                        </td>
                      </tr>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td align="right" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                <table style="border-collapse: collapse;">
                  <tbody>
                    <tr></tr>
                    <tr>
                      <td>
                        <img src="' . get_template_directory_uri() . '/images/Graphic.png" alt="Thank you for trusting us to help you find a solution">
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <tr>
                        <td>
                        </td>
                      </tr>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right" style="padding: 0px 30px;font-size: 12px;FONT-WEIGHT: 500;">
                <h2 class="title" style="margin: 7px 0px;font-weight: 500;font-size: 20px;color: #585858;text-align:center;">' . $mail_title . '</h2>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <table width="100%">
                  <tbody>
                    <tr>
                      <td style="width: 117px;"></td>
                        <td align="right" style="border-top: 2px solid #f74d00;">
                        </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="    padding: 7px;">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
               
                <h3 style="color: #f74d00;font-weight: normal;line-height: 3px;font-size: 18px;">Greetings from Testbytes!</h3>
                <p style="font-size: 17px;color: #989696;line-height: 25px;padding: 1px 20px;font-weight: 500;text-align: left;">Dear ' . $_POST["fpc_user_name"] . ' </p>
                <p style="font-size: 17px;color: #989696;line-height: 25px;padding: 1px 20px;font-weight: 500;">
                  Thank you for using our cost estimation calculator. The estimated amount and required time frame for testing your software has been attached to this mail.
                </p>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                &nbsp;
              </td>
            </tr>
            <tr style="background: #f74d00;color: #ffffff;">
              <td class="heding-table" align="center" width="50%">
               <h3 style="font-weight: 200;font-size: 16px;border-right: 1px solid;padding: 14px 0;margin: 0;color: #fff;">SPECIFICATIONS</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: 200;font-size: 16px;padding: 14px 0;margin: 0;color: #fff;">USER PREFERENCES</h3>
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Domain</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . ucwords($dis_category) . '</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of Screens</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $no_of_screens . '</h3> 
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of External Interface</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $no_ext_interface_text . '</h3> 
              </td>
            </tr>';

  if (isset($_POST["kind_of_testing"])) {
    $exact_result .= '<tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> Testing type</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $dis_kind . '</h3> 
              </td>
            </tr>';
  }

  $exact_result .= '<tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> Platforms</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $dis_platfrom . '</h3> 
              </td>
            </tr>
             <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">How Complex</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $complex_text . '</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of Cycles</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $no_of_cycles . '</h3> 
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of Test Cases</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($testcases) . '</h3> 
              </td>
            </tr>
            <tr>
              <td colspan="2" style="    PADDING: 0px;">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2" style="    PADDING: 0px;">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center" style="padding: 0px 30px;font-size: 12px;FONT-WEIGHT: 500;">
                <h2 class="title" style="margin: 0px 0px;font-weight: 500;font-size: 20px;color: #585858;">Test Preparation Effort</h2>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="    PADDING: 0px;">
                &nbsp;
              </td>
            </tr>
            
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Case/Scenario Preparation Effort (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($tot_efforts_in_hr, 2) . '</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Environment Setup (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($test_env_setup, 2) . '</h3> 
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Data Preparation (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($test_data_preparation, 2) . '</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Configuration Setup (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . round($test_config_setup, 2) . '</h3> 
              </td>
            </tr>
            <tr style="background: #ef4d30;">
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">Total Test Preparation Effort (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">' . round(($tot_efforts_in_hr + $test_env_setup + $test_data_preparation + $test_config_setup), 2) . '</h3> 
              </td>
            </tr>
            <tr>
              <td colspan="2" style="    PADDING: 0px;">
                &nbsp;
              </td>
            </tr>';



  $exact_result .= '

            <!-- // Added 12 more line as per the tabel -->
            <tr>
              <td colspan="2" style="    PADDING: 0px;">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center" style="padding: 0px 30px;font-size: 12px;FONT-WEIGHT: 500;">
                <h2 class="title" style="margin: 0px 0px;font-weight: 500;font-size: 20px;color: #585858;">Test Execution Effort</h2>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;font-family:Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;box-shadow: 0 2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)!important;">
                        <tbody>
                            <tr style="background: #f74d00;color: #ffffff;">
                                <td class="heding-table" width="10%" align="center" style="border-right:1px solid #bfbfbf;padding: 0 10px;">
                                    <h3 style="font-weight: 200;font-size: 16px;/* border-right: 1px solid; */padding: 21px 0;margin: 0;color:#fff;">Cycles</h3>
                                </td>
                                <td class="heding-table" width="20%" align="center" style="border-right:1px solid #bfbfbf;padding: 0 10px;">
                                    <h3 style="font-weight: 200;font-size: 16px;/* border-right: 1px solid; */padding: 21px 0;margin: 0;color:#fff;">Test Preparation Effort (in hrs)</h3>
                                </td>
                                <td class="heding-table" width="20%" align="center" style="border-right:1px solid #bfbfbf;padding: 0 10px;"><h3 style="font-weight: 200;font-size: 16px;/* border-right: 1px solid; */padding: 21px 0;margin: 0;color:#fff;">Total effort for Test Execution (in hrs)</h3>
                                </td>
                                <td class="heding-table" width="20%" align="center" style="border-right:1px solid #bfbfbf;padding: 0 10px;"><h3 style="font-weight: 200;font-size: 16px;/* border-right: 1px solid; */padding: 21px 0;margin: 0;color:#fff;">Total effort for Defect Reporting (in hrs)</h3>
                                </td>
                                <td class="heding-table" width="20%" align="center" style="padding: 0 10px;"><h3 style="font-weight: 200;font-size: 16px;/* border-right: 1px solid; */padding: 21px 0;margin: 0;color:#fff;">Retesting of Testcase Failed(in hrs)</h3>
                                </td>
                            </tr>
                            ' . $final_mail_result . '
                            </tbody>
                    </table>
                </td>
            </tr>
            <tr>
              <td colspan="2">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2">
                &nbsp;
              </td>
            </tr>';

  $grand_cost = '
                  <tr>
                    <td class="heding-table" align="center" style="padding:8px;">                
                      <h3 style="font-weight: 500;font-size: 16px;color: #616161;"></h3>                   
                    </td>                  
                    <td class="heding-table" align="center">                  
                      <h3 style="font-weight: 500;font-size: 16px;color: #616161;"></h3>                   
                    </td>                  
                  </tr>
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" >
                      <table style="width:100%;">
                        <tbody>
                          <tr>
                            <td style="width: 42px;"></td>
                              <td align="right" style="float: right;background: #ececec;border-bottom-left-radius: 50px;border-top-left-radius: 50px;padding: 12px;border: 1px solid #d5312d;border-right: none;">';
  if (isset($grandtotalEfforts)) {

    if ($grandtotalEfforts < 800) {
      $timeline_val = 1;
    }
    elseif (($grandtotalEfforts >= 800) && ($grandtotalEfforts <= 1500)) {
      $timeline_val = 2;
    }
    elseif ($grandtotalEfforts > 1500) {
      $timeline_val = 3;
    }
  }
  $grand_cost .= '<h2 class="title" style="margin: 10px 0px;font-weight: 100;font-size: 22px;color: #000000;padding: 0px 16px;">TIMELINE  &nbsp; <b style="color:#000000;font-weight: 500;">' . $timeline_value . ' Month (approx.)</b></h2>
                                
                              </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center" style="padding: 15px 27px 0px;font-size: 12px;FONT-WEIGHT: 500;">
                      <h2 class="title" style="font-weight: 500;font-size: 20px;color: #585858;margin: 10px 3px 0px;">TOTAL AMOUNT IS</h2>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center" style="FONT-WEIGHT: 500;">';
  $grand_cost .= $mail_total_cost_display . '</td>                  
                  </tr>

                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>

                  <tr>
                      <td colspan="2" align="center">
                        <p style="font-size: 17px;color: #989696;line-height: 25px;padding: 1px 20px;font-weight: 500;">*We are showcasing cost range because, variation can happen with respect to employee experience and resource used for the project)</p>
                      </td>
                  </tr>
                  
                  <tr>
                    <td colspan="2" align="center" style="padding: 15px 27px 0px;font-size: 12px;FONT-WEIGHT: 500;">
                      <h2 class="title" style="font-weight: 500;font-size: 20px;color: #585858;margin: 10px 3px 0px;">TOTAL EFFORT IS</h2>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center" style="padding: 0px 100px;FONT-WEIGHT: 500;">';
  $grand_cost .= $total_effort_mail_display . '</td>                  
                  </tr>
                  
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>

                  <tr>
                      <td colspan="2" align="center">
                        <p style="font-size: 17px;color: #989696;line-height: 25px;padding: 1px 20px;font-weight: 500;">If you have any query regarding the estimation feel free to contact us at </p>
                      </td>
                  </tr>

                  <tr>
                    <td style="padding: 0px 15px 6px;">
                    <table>
                      <tbody>
                      
                        <tr>
                          <td>
                            <img src="' . get_template_directory_uri() . '/images/web_mail_icn.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
                          </td>
                          <td>
                            <h4 style="font-size: 17px;color: #444343;font-weight: 500;margin: 0px 7px 4px;">www.testbytes.net</h4>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    </td>
                    <td style="padding: 0px 15px 6px;">
                    <table>
                      <tbody>
                        <tr>
                          <td>
                            <img src="' . get_template_directory_uri() . '/images/phone_mail_icn.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
                          </td>
                          <td>
                            <h4 style="font-size: 17px;color: #444343;font-weight: 500;margin: 0px 7px 4px;">+1 929 552 0053</h4>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 0px 15px 6px;">              
                      <table>
                        <tbody>
                          <tr>
                            <td>
                              <img src="' . get_template_directory_uri() . '/images/whatsapp_icn.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
                            </td>
                            <td>
                              <h4 style="font-size: 17px;color: #444343;font-weight: 500;    margin: 0px 7px 4px;">+91 8113 865000</h4>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                    <td style="padding: 0px 15px 6px;">
                      <table>
                        <tbody>
                          <tr>
                            <td>
                            <a href="https://is.gd/uEt4xD">
                              <img src="' . get_template_directory_uri() . '/images/skype.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
                            </a>
                            </td>
                            <td>
                            <a href="https://is.gd/uEt4xD">
                              <h4 style="font-size: 17px;color: #444343;font-weight: 500;margin: 0px 7px 4px;">live:vishnu_457</h4>
                            </a> 
                            </td>
                          </tr>
                        </tbody>
                      </table>                  
                    </td>
                  </tr>

                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                      <table>
                        <tbody>
                          <tr>                            
                            <td>
                              <h4 style="font-size: 20px;color:#444343;font-weight:500;margin:0px 7px 4px;">We are now 45K+ on </h4>
                            </td>
                          <td>
                              <img src="https://www.testbytes.net/wp-content/themes/testbytes/images/mail_linkedin.png" alt="" style="background:white;border:1px solid transparent;border-radius:50px" class="CToWUd">
                            </td></tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td align="center" colspan="2"><a href="https://in.linkedin.com/company/softwaretestingcompany" style="background: #ef4d30;display: inline-block;margin: 10px 0 10px;padding: 15px 40px;text-transform: uppercase;color: #fff;font-size: 17px;font-weight: bold;border-radius: 8px;text-decoration: none;">Join Us</a>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      &nbsp;
                    </td>
                  </tr>';

  $user_details = '<tr>
                  <td colspan="2" align="center" style="padding: 0px 30px;font-size: 12px;FONT-WEIGHT: 500;">
                    <h2 class="title" style="margin: 0px 0px;font-weight: 500;font-size: 20px;color: #585858;">USER DETAILS</h2>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    &nbsp;
                  </td>
                </tr>
                <tr style="background: #efefef;">
                  <td class="heding-table" align="center">
                   <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Name</h3> 
                  </td>
                  <td class="heding-table" align="center">
                   <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $_POST["fpc_user_name"] . '</h3> 
                  </td>
                </tr>
                <tr>
                  <td class="heding-table" align="center">
                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Mail</h3> 
                  </td>
                  <td class="heding-table" align="center">
                    <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $_POST["fpc_user_email"] . '</h3> 
                  </td>
                </tr>
                <tr style="background: #efefef;">
                  <td class="heding-table" align="center">
                   <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Mobile</h3> 
                  </td>
                  <td class="heding-table" align="center">
                   <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $_POST["fpc_user_mobile"] . '</h3> 
                  </td>
                </tr>
                <tr>
                  <td class="heding-table" align="center">
                   <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Location</h3> 
                  </td>
                  <td class="heding-table" align="center">
                   <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $user_location . '</h3> 
                  </td>
                </tr> 
                <tr style="background: #efefef;">
                  <td class="heding-table" align="center">
                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Message</h3> 
                  </td>
                  <td class="heding-table" align="center">
                    <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">' . $_POST["fpc_user_message"] . '</h3> 
                  </td>
                </tr>
                <tr>
                <td colspan="2" style="    PADDING: 0px;">
                  &nbsp;
                </td>
              </tr>';

  $result_footer = '<tr>
                  <td colspan="2">
                    &nbsp;
                  </td>
                </tr>



                 <tr>
                    <td colspan="2" style="padding: 1px 17px;">
                      <h1 style="border-bottom:1px solid #f74d00"></h1>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2" align="left">
                      <h3 style="color: #f74d00;font-weight: normal;line-height: 3px;font-size: 19px;    padding: 1px 17px;">DISCLAIMER</h3>
                      <p style="font-size: 16px;color: #989696;line-height: 25px;padding: 1px 17px;font-weight: 500;">
                      The estimation included in the mail has been possible as a result of predictive analysis and complex algorithm execution. Slight changes are a possibility.
                      </p>
                      <p style="font-size: 13px;color: #f74d00;line-height: 25px;padding: 1px 17px;font-weight: 500;font-style: italic;">
                       Note: This email is generated from Test Effort Calculator page - Testbytes
                      </p>
                    </td>
                  </tr>
                 
                  <tr>
                    <td style="padding: 5px;"></td>
                  </tr>
                  <tr>
                    <td>
                     <img src="' . get_template_directory_uri() . '/images/Graphic.png" alt="" style="transform: rotate(180deg);">
                    </td>
                    <td>
                      <table>
                        <tbody>
                          <tr>
                            <td align="right" style="    padding: 0px 6px;">
                              <p style="color:#636262;font-size:15px">
                                125/2,Sainiketan Colony, Kalas road,<br>
                                Vishrantwadi, Pune,<br>
                                Maharashtra-411015
                              </p>
                            </td>
                            <td>
                              <img src="' . get_template_directory_uri() . '/images/location.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16);">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                style="border: 1px solid #cccccc; border-collapse: collapse;">



              </table>
            </td>
          </tr>
        </table>
      </body>';
  if (isset($effort_details)) {
    $admin_mail = $exact_result . $effort_details . $grand_cost . $user_details . $result_footer;

    
$customer_mail = $exact_result . $effort_details . $grand_cost . $result_footer;

  
}
  else {
    $admin_mail = $exact_result . $grand_cost . $user_details . $result_footer;

    
$customer_mail = $exact_result . $grand_cost . $result_footer;

  
}
  $json_res_status = '

<p style="color: #159a15;font-size: 18px;">Details you entered has been submitted successfully. Please refer below for results.</p>
    <table class="table" border="0" cellpadding="0" cellspacing="0" style="width:100%" id="cust_det">
        <tbody>                                         
           <tr>
            <td style="padding:10px;border:none;"><table class="table" width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid rgb(238, 234, 234);">
              <tbody>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Domain </td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . ucwords($dis_category) . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Screens</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_screens . '</td>
                </tr> 
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of External Interface</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_ext_interface_text . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Platforms</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $dis_platfrom . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">How Complex</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $complex_text . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Cycles</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_cycles . '</td>
                </tr>
                                
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Test Cases</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($testcases) . '</td>
                </tr>
                <tr>
                   <td colspan="2" align="center" style="padding:20px 30px;font-size:12px;FONT-WEIGHT:500">
                    <h3 style="font-weight: bold;font-size: 22px;color: #616161;padding: 14px 0;margin: 0;">Test Preparation Effort</h3> 
                   </td>
                 </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Case/Scenario Preparation effort (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_hr, 2) . '</td>
                </tr>    
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Environment Setup (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($test_env_setup, 2) . '</td>
                </tr> 
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Data Preparation (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($test_data_preparation, 2) . '</td>
                </tr> 
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Configuration Setup (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($test_config_setup, 2) . '</td>
                </tr> 
                <tr>
                  <td bgcolor="#dcdcdc" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Configuration Setup (in hrs)</td>
                  <td bgcolor="#dcdcdc" style="padding:15px;width:50%;color: #f74d00;">' . round(($tot_efforts_in_hr + $test_env_setup + $test_data_preparation + $test_config_setup), 2) . '</td>
                </tr> 
              </tbody>
            </table></td>
                        
                         </tr> 
                         <tr>
                           <td colspan="2" align="center" style="padding:20px 30px;font-size:12px;FONT-WEIGHT:500;border:none;">
                            <h3 style="font-weight: bold;font-size: 22px;color: #616161;padding: 14px 0;margin: 0;">Test Execution Effort</h3> 
                           </td>
                         </tr>   
                         <tr>
                          <td style="border:none;">' . $final_user_result . '</td>
                         </tr>

                         <tr>
                           <td colspan="2" style="border: none;">
                             <table style="width:100%">
                              <tbody>
                                <tr>
                                  <td style="width:42px"></td>
                                    <td align="right" style="float:right;background:#ececec;border-bottom-left-radius:50px;border-top-left-radius:50px;padding:12px;border:1px solid #d5312d;border-right:none"><h2 style="margin:10px 0px;font-weight:100;font-size:22px;color:#000000;padding:0px 16px">TIMELINE  &nbsp; <b style="color:#000000;font-weight:500">' . $timeline_value . ' Month (approx.)</b></h2>
                                      
                                    </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                          </tr> 

                          <tr><td colspan="2" style="border: none;"> &nbsp;</td></tr>                                

                          <tr>
                            <td colspan="2" align="center" style="padding:15px 27px 0px;font-size:12px;FONT-WEIGHT:500;border: none;">
                              <h2 style="font-weight:500;font-size:20px;color:#585858;margin:10px 3px 0px">APPROX. COST IS</h2>
                            </td>
                          </tr>

                          <tr>
                          <td colspan="2" align="center" style="padding:0px 100px;FONT-WEIGHT:500;border: none;">' . $user_total_cost_display . '</td>                  
                        </tr>
                        <tr>
                          <td colspan="2" align="center" style="padding:15px 27px 0px;font-size:12px;FONT-WEIGHT:500;border: none;">
                            <h2 style="font-weight:500;font-size:20px;color:#585858;margin:10px 3px 0px">GRAND TOTAL EFFORT IS</h2>
                          </td>
                        </tr>

                        <tr>
                          <td colspan="2" align="center" style="padding:0px 100px;FONT-WEIGHT:500;border: none;">' . $total_effort_mail_display . '</td>
                        </tr>
                    </tbody>
                </table>';
  $unique_user_id = sanitize_title($_POST["fpc_user_name"]) . "_" . time();
  $client_mail = $_POST["fpc_user_email"];
  $unique_mobile = $_POST["fpc_user_mobile"];
  $unique_user_name = $_POST["fpc_user_name"];
  $to = 'info@testbytes.net';
  $admin_ID = "info@testbytes.net";
  
//=====Customer mail sent ============      $headers = 'From:' . $to . "\r\n";  $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n" .
  'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
    'MIME-Version: 1.0' . "\r\n\r\n";
  if (isset($_POST["kind_of_testing"])) {
    $subject = 'Testbytes - Mobile App Testing Calculation for ' . $client_mail;
  }
  else {
    $subject = 'Testbytes - Test Effort Calculation for ' . $client_mail;
  }
  $customer_sent = sendgrid_email_send($unique_user_name, $client_mail, $subject, $customer_mail, $to, $unique_user_id, $unique_mobile);
  if (($customer_sent == 200) || ($customer_sent == 202) || ($customer_sent == "200") || ($customer_sent == "202")) {
    $mail_sent = 1;
    $json_result = '<p style="color: #159a15;font-size: 18px;">Please Check Your Mail.</p>';
  }
  else {
    $mail_sent = 0;
    $json_result = '<p style="color: #159a15;font-size: 18px;">Something Went wrong.</p>';
  }
  $admin_sent = sendgrid_email_send($unique_user_name, $admin_ID, $subject, $admin_mail, $to, $unique_user_id, $unique_mobile);
  if (($admin_sent == 200) || ($admin_sent == 202) || ($admin_sent == "200") || ($admin_sent == "202")) {
    $mail_sent = 1;
  }
  else {
    $mail_sent = 0;
  }
  if ($mail_sent == 1) {
    //=====Admin mail sent ============
    $headers = 'From:' . $_POST['fpc_user_email'] . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n" .
      'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
      'MIME-Version: 1.0' . "\r\n\r\n";
    if (isset($_POST["kind_of_testing"])) {
      $subject = 'Testbytes - Mobile App Testing Calculation for ' . $client_mail;
    }
    else {
      $subject = 'Testbytes - Test Effort Calculation for ' . $client_mail;
    }
}
  $category = isset($category) ? $category : '';
  $dis_kind = isset($dis_kind) ? $dis_kind : '';
  global $wpdb;
  $table_name = "wp_testcost_enquiries";
  $wpdb->insert($table_name, array(
    "app_category" => $category,
    "user_name" => $_POST["fpc_user_name"],
    "user_email" => $_POST["fpc_user_email"],
    "user_mobile" => $_POST["fpc_user_mobile"],
    "user_comment" => $_POST["fpc_user_message"],
    "user_location" => $user_location,
    "user_city" => $user_city,
    "user_state" => $user_region,
    "user_country" => $user_CountryName,
    "user_country_code" => $user_countryCode,
    "app_category" => $db_category,
    "no_of_screens" => $no_of_screens,
    "no_ext_interface" => $no_ext_interface_text,
    "kind_of_testing" => $dis_kind,
    "tot_no_ext_interface" => $no_ext_interface,
    "choosen_platform" => $dis_platfrom,
    "complexity" => $complex_text,
    "no_of_cycles" => $no_of_cycles,
    "no_of_testcases" => round($testcases, 2),
    "test_preparation_effort" => round($tot_efforts_in_hr, 2),
    "timeline" => $timeline_value,
    "min_cost" => $total_cost_min_db,
    "max_cost" => $total_cost_max_db,
    "country_cost" => $db_country_cost,
    "grand_tot_effort" => $total_effort_mail,
    "mail_sent" => $mail_sent,
    "sendgrid_unique_id" => $unique_user_id, ));


  $enq_id = $wpdb->insert_id;
  $sms_res = "pdf api is not working";
  if (isset($_POST['sms_check'])) {
    $sms_check = $_POST["sms_check"];
    if ($sms_check == 'yes') {
      $pdf_user_name = $_POST["fpc_user_name"];
      $pdf_user_mobile = $_POST["fpc_user_mobile"];
      $t = time();
      $file_name = sanitize_title($pdf_user_name) . $t;
      $file = TEMPLATEPATH . '/images/test-cost/' . $file_name . '.pdf';
      $sms_file_path = get_template_directory_uri() . '/images/test-cost/' . $file_name . '.pdf';
      $fh = fopen(TEMPLATEPATH . '/images/test-cost/' . $file_name . '.pdf', "w");
      if ($fh == false) {
        $pdf_created = 0;
      }
      else {
        $pdf_created = 1;
      }
      fclose($fh);
      if ($pdf_created == 1) {
        $userpdf = $file;
        $apikey = '1c95f68b-d10e-4199-8b6d-ed13e18abb15';
        $pdf_content_user = $customer_mail;
        $postdata_user = http_build_query(
          array(
          'apikey' => $apikey,
          'value' => $pdf_content_user,
          'MarginBottom' => '30',
          'MarginTop' => '20'
        )
        );
        $opts_user = array('http' =>
            array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata_user
          )
        );
        $context_user = stream_context_create($opts_user);
        $pdf_result_user = file_get_contents('http://api.html2pdfrocket.com/pdf', false, $context_user);
        if (file_put_contents($userpdf, $pdf_result_user) === false) {
          $sms_res = "failed to write content in pdf";
        }
        else {
          $today = date('Y-m-d H:i:s');
          $expire_date = date('Y-m-d H:i:s', strtotime($today . ' + 10 days'));
          $pdf_row = $wpdb->get_row("SELECT * FROM `wp_testcost_pdf` WHERE `enquiry_id` = '" . $enq_id . "'");
          $table_name_pdf = 'wp_testcost_pdf';
          if (empty($pdf_row)) {
            $wpdb->insert($table_name_pdf, array(
              "enquiry_id" => $enq_id,
              "pdf_file_path" => $sms_file_path,
              "expire_date" => $expire_date
            ));
          }
          else {
            $wpdb->update($table_name_pdf, array(
              "pdf_file_path" => $sms_file_path,
              "expire_date" => $expire_date
            ), array("enquiry_id" => $enq_id));
          }
          $api_username = "itsupport@technoallianceindia.com";
          $api_hash = "ca6f41d6d3deadb8522f745f5b3ec673d87ce05fdef7724799cb3fc34053ba33";
          $api_test = "0";
          $api_sender = "TXTLCL";
          $api_numbers = $pdf_user_mobile;
          $api_message = "Hi " . $pdf_user_name . " , %0a find high-level Effort and Test Cost estimation based on your preferences here " . $sms_file_path . "%0a Testbytes";
          $api_message = urlencode($api_message);
          $api_data = "username=" . $api_username . "&hash=" . $api_hash . "&message=" . $api_message . "&sender=" . $api_sender . "&numbers=" . $api_numbers . "&test=" . $api_test;
          $ch = curl_init('http://api.textlocal.in/send/?');
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $api_data);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $api_result = curl_exec($ch);
          curl_close($ch);
          $decoded_api_res = json_decode($api_result);
          $sms_res = "sms result is " . isset($decoded_api_res->status) ? $decoded_api_res->status : '';
        }
      }
      else {
        $sms_res = "failed to create pdf";
      }
    }
    else {
      $sms_res = "sms not selected";
    }
  }
  else {
    $sms_res = "sms not there";
  }
  $noti_email = $_POST["fpc_user_email"];
  if ($enq_id != 0) {
  }
  $req_res = array('status' => $json_result, 'enq_id' => $enq_id, 'sms_res' => $sms_res);
  echo json_encode($req_res);
  die;
}

add_action('wp_ajax_fpc_cal_test', 'fpc_cal_test');
add_action('wp_ajax_nopriv_fpc_cal_test', 'fpc_cal_test');

function fpc_cal_test()
{
  $no_of_screens = (int)$_POST["fpc_no_of_screens"];
  $no_ext_interface = (int)$_POST["fpc_no_of_ext_interface"];
  $platforms_arr = $_POST["fpc_platforms"];
  $complexity_arr = 74;
  $no_of_cycles = (int)$_POST["fpc_no_cycles"];
  $no_of_cycles = 5;
  if (in_array('Both', $platforms_arr)) {
    $key = array_search('Both', $platforms_arr);
    if (false !== $key) {
      unset($platforms_arr[$key]);
    }
    $no_of_platforms = 1;
    $dis_platfrom = implode(", ", $platforms_arr);
  }
  else {
    $no_of_platforms = count($platforms_arr);
    $no_of_platforms = 0.5;
    $dis_platfrom = implode(", ", $platforms_arr);
  }


  if ($complexity_arr < 25) {
    $complex_text = "Simple";
    $complexity_value = 2;
  }
  elseif ($complexity_arr < 50) {
    $complex_text = "Medium";
    $complexity_value = 3;
  }
  elseif ($complexity_arr < 75) {
    $complex_text = "Complex";
    $complexity_value = 4;
  }
  elseif ($complexity_arr <= 100) {
    $complex_text = "Very Complex";
    $complexity_value = 5;
  }
  $functional_val = 14 * $complexity_value;
  $complexity_adjustment_factor = 0.65 + (0.01 * $functional_val);
  $unadjusted_functional_point = ($complexity_value * $no_of_screens) + ($complexity_value * $no_ext_interface) + ($complexity_value * $no_of_platforms);
  $functional_point = $unadjusted_functional_point * $complexity_adjustment_factor;
  $efforts_in_hr = $complexity_value * $functional_point;
  $efforts_in_day = $efforts_in_hr / 8;
  $efforts_in_month = $efforts_in_day / 20;
  $tot_efforts_in_hr = $efforts_in_hr * $no_of_platforms;
  $tot_efforts_in_day = $tot_efforts_in_hr / 8;
  $tot_efforts_in_month = $tot_efforts_in_day / 20;


  //---------------------------------------------------
  $factor = 1.2;
  $one_minute = 1;
  $one_minute_in_hrs = 1 / 60;

  if ($complexity_arr < 25) {
    $factor_in_min = 4;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;
  }
  elseif ($complexity_arr < 50) {
    $factor_in_min = 6;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;
  }
  elseif ($complexity_arr < 75) {
    $factor_in_min = 8;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;
  }
  elseif ($complexity_arr <= 100) {
    $factor_in_min = 10;
    $factor_in_hrs = $one_minute_in_hrs * $factor_in_min;

  
}
  $defect_reporting = $one_minute_in_hrs * 15;
  $retesting_ten_mins = 0.166666666666667;
  $testcases = pow($functional_point, $factor);
  $total_time_in_hrs = $testcases * $factor_in_hrs;
  $total_time_defect_reporting = ($testcases / 2) * $defect_reporting;
  $total_effort_for_test_execution_cy1 = $total_time_defect_reporting + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy1 = ($testcases / 2) * $retesting_ten_mins;
  $total_time_defect_reporting_cy2 = ($testcases / 4) * $defect_reporting;
  $total_effort_for_test_execution_cy2 = $total_effort_for_test_execution_cy1;
  //=========Cycle 3============  $total_time_defect_reporting_cy3 = ($testcases / 4) * $defect_reporting;  $total_effort_for_test_execution_cy3 = $total_time_defect_reporting_cy3 + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy3 = ($retesting_testcases_failed_after_cy1 / 4) * $retesting_ten_mins;
  //=========Cycle 4 ============  $total_time_defect_reporting_cy4 = ($testcases / 8) * $defect_reporting;  $total_effort_for_test_execution_cy4 = $total_time_defect_reporting_cy4 + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy4 = ($retesting_testcases_failed_after_cy1 / 8) * $retesting_ten_mins;
  //=========Cycle 5 ==========  $total_time_defect_reporting_cy5 = ($testcases / 16) * $defect_reporting;  $total_effort_for_test_execution_cy5 = $total_time_defect_reporting_cy5 + $total_time_in_hrs;
  $retesting_testcases_failed_after_cy5 = ($retesting_testcases_failed_after_cy1 / 16) * $retesting_ten_mins;
  $final_user_result = '<table>
                        <thead>
                          <tr>
                            <th>Cycle</th>
                            <th>Total Effort for Defect Reporting (in hrs)</th>
                            <th>Total effort for Test Execution (in hrs)</th>
                            <th>Retesting of Test Case failed (in hrs)</th>
                          </tr>
                        </thead>
                      <tbody>';


  
if ($no_of_cycles >= 1) {
    $final_retesting = "";
    $final_defect_reporting = $total_time_defect_reporting;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy1;
    $final_user_result .= '<tr>
                          <td>1<td>
                          <td>' . round($final_defect_reporting, 2) . '<td>
                          <td>' . round($final_total_effort_text_excution, 2) . '<td>
                          <td>' . round($final_retesting, 2) . '<td>
                        </tr>';
  }
  if ($no_of_cycles >= 2) {
    $final_retesting = $retesting_testcases_failed_after_cy1;
    $final_defect_reporting = $total_time_defect_reporting_cy2;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy2;
    $final_user_result .= '<tr>
                          <td>2<td>
                          <td>' . round($final_defect_reporting, 2) . '<td>
                          <td>' . round($final_total_effort_text_excution, 2) . '<td>
                          <td>' . round($final_retesting, 2) . '<td>
                        </tr>';
  }
  if ($no_of_cycles >= 3) {
    $final_retesting = $retesting_testcases_failed_after_cy3;
    $final_defect_reporting = $total_time_defect_reporting_cy3;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy3;
    $final_user_result .= '<tr>
                          <td>3<td>
                          <td>' . round($final_defect_reporting, 2) . '<td>
                          <td>' . round($final_total_effort_text_excution, 2) . '<td>
                          <td>' . round($final_retesting, 2) . '<td>
                        </tr>';
  }
  if ($no_of_cycles >= 4) {
    $final_retesting = $retesting_testcases_failed_after_cy4;
    $final_defect_reporting = $total_time_defect_reporting_cy4;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy4;
    $final_user_result .= '<tr>
                          <td>4<td>
                          <td>' . round($final_defect_reporting, 2) . '<td>
                          <td>' . round($final_total_effort_text_excution, 2) . '<td>
                          <td>' . round($final_retesting, 2) . '<td>
                        </tr>';
  }
  if ($no_of_cycles >= 5) {
    $final_retesting = $retesting_testcases_failed_after_cy5;
    $final_defect_reporting = $total_time_defect_reporting_cy5;
    $final_total_effort_text_excution = $total_effort_for_test_execution_cy5;

    $final_user_result .= '<tr>
                          <td>5<td>
                          <td>' . round($final_defect_reporting, 2) . '<td>
                          <td>' . round($final_total_effort_text_excution, 2) . '<td>
                          <td>' . round($final_retesting, 2) . '<td>
                        </tr>';
  }
  $final_user_result .= '</tbody>
</table>';
  echo $final_user_result;
  die;
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
  $PublicIP = $ipaddress;
  $location_json = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $PublicIP . "");
  if (($location_json !== false) && (!empty($location_json))) {
    $decode_location = json_decode($location_json, true);
    $user_location = '';
    if ((isset($decode_location['geoplugin_request'])) && ($decode_location['geoplugin_request'] != '') && ($decode_location['geoplugin_request'] != NULL)) {
      $user_ip = $decode_location['geoplugin_request'];
    }
    else {
      $user_ip = '';
    }

    if ((isset($decode_location['geoplugin_city'])) && ($decode_location['geoplugin_city'] != '') && ($decode_location['geoplugin_city'] != NULL)) {
      $user_city = $decode_location['geoplugin_city'];
      $user_location .= $user_city . ', ';
    }
    else {
      $user_city = '';
    }

    if ((isset($decode_location['geoplugin_region'])) && ($decode_location['geoplugin_region'] != '') && ($decode_location['geoplugin_region'] != NULL)) {
      $user_region = $decode_location['geoplugin_region'];
      $user_location .= $user_region . ', ';
    }
    else {
      $user_region = '';
    }

    if ((isset($decode_location['geoplugin_countryName'])) && ($decode_location['geoplugin_countryName'] != '') && ($decode_location['geoplugin_countryName'] != NULL)) {
      $user_CountryName = $decode_location['geoplugin_countryName'];
      $user_location .= $user_CountryName;
    }
    else {
      $user_CountryName = '';
    }
  }
  else {
    $user_location = "Couldn't Find Location";
  }
  if (trim($user_location) == '') 
{
    $user_location = "Couldn't Find Any Location";
  }

  $result = '<table align="center" border="0" cellpadding="0" cellspacing="0" class="email-container" style="margin:auto; width:600px"><!-- Hero Image, Flush : BEGIN -->
    <tbody>
        <tr>
            <td style="background-color:rgb(239, 239, 239); text-align:center">&nbsp;</td>
        </tr>
        <tr>
            <td style="background-color:rgb(239, 239, 239)">&nbsp;</td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td style="background-color:rgb(239, 239, 239)">
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
                <tbody>
                    <tr>
                        <td style="width:30px">&nbsp;</td>
                        <td>
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
                            <tbody>
                                <tr>
                                    <td style="text-align:center; vertical-align:top"><img alt="Logo" src="https://www.testbytes.net/wp-content/themes/testbytes/images/logo1.png" style="width:25%" /></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:100%">
                                    <p style="font-weight:bold;font-size: 15px;">Hi ' . $_POST["fpc_user_name"] . ',</p>

                                    <p style="font-size: 15px;">Thank you for your valuable time. The below mentioned calculation is near accurate and for further explanation and details our representative will contact you as soon as possible.</p>

                                    <p style="font-size: 15px;">Please check the details below.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
                                        <tbody>                                         
                                           

                                         <td bgcolor="#efefef" style="padding:20px"><table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid rgb(216, 221, 228);">
                                              <tbody>
                                                <tr>
                                                  <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;font-size: 15px;">User Name</td>
                                                  <td bgcolor="#f7f7f7" style="padding:15px;font-size: 15px;">' . $_POST["fpc_user_name"] . '</td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;font-size: 15px;">User Email</td>
                                                  <td bgcolor="#ffffff" style="padding:15px;font-size: 15px;">' . $_POST["fpc_user_email"] . '</td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;font-size: 15px;">User Phone Number</td>
                                                  <td bgcolor="#f7f7f7" style="padding:15px;font-size: 15px;">' . $_POST["fpc_user_mobile"] . '</td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;font-size: 15px;">Message</td>
                                                  <td bgcolor="#ffffff" style="padding:15px;font-size: 15px;">' . $_POST["fpc_user_message"] . '</td>
                                                </tr>
                                                <tr>
                                                  <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;font-size: 15px;">Location</td>
                                                  <td bgcolor="#f7f7f7" style="padding:15px;font-size: 15px;">' . $user_location . '</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">&nbsp;</td>
                                                 </tr>
                                                 <tr>
                                                    <td colspan="3">&nbsp;</td>
                                                </tr>

                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Domain</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $dis_category . '</td>
                                              </tr> 
                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Screens</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_screens . '</td>
                                              </tr> 
                                              <tr>
                                                <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No OF External Interface</td>
                                                <td bgcolor="#f7f7f7" style="padding:15px;width:50%;color: #f74d00;">' . $no_ext_interface . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Platforms</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_platforms . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;"> Platforms</td>
                                                <td bgcolor="#f7f7f7" style="padding:15px;width:50%;color: #f74d00;">' . $dis_platfrom . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">How Complex</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $complex_text . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Cycles</td>
                                                <td bgcolor="#f7f7f7" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_cycles . '</td>
                                              </tr>
                                              
                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Preparation effort (in hrs)</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_hr, 2) . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Preparation effort (in days)</td>
                                                <td bgcolor="#f7f7f7" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_day, 2) . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Preparation effort (in months)</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_month, 2) . '</td>
                                              </tr>

                                              <tr>
                                                <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Retesting of Test Case failed (in hrs)</td>
                                                <td bgcolor="#f7f7f7" style="padding:15px;width:50%;color: #f74d00;">' . round($final_retesting, 2) . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Total Effort for Defect Reporting (in hrs)</td>
                                                <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($final_defect_reporting, 2) . '</td>
                                              </tr>
                                              <tr>
                                                <td bgcolor="#f7f7f7" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Total effort for Test Execution (in hrs)</td>
                                                <td bgcolor="#f7f7f7" style="padding:15px;width:50%;color: #f74d00;">' . round($final_total_effort_text_excution, 2) . '</td>
                                              </tr>
  
                                              </tbody>
                                            </table></td>
                                            
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="font-size: 15px;">Thank you,</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                            </tr>                                           
                                        </tbody>
                                    </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </td>
                        <td style="width:30px">&nbsp;</td>
                    </tr>
                </tbody>
            </table>

            <table align="center" border="0" cellpadding="0" cellspacing="0" style="color:rgb(136, 136, 136); font-family:sans-serif; font-size:12px; line-height:140%; max-width:680px; width:100%">
                <tbody>
                    <tr>
                        <td style="background-color:rgb(216, 221, 228); text-align:center; width:100%"><br />
                        <em>Copyright &copy; ' . date("Y") . ' testbytes.net. </em><br />
                        &nbsp;</td>
                    </tr>
                </tbody>
            </table>
            </td>
        </tr>
    </tbody>
</table>
<!--[if gte mso 9]>                 </v:textbox>                    </v:rect>                   <![endif]--><!-- Email Body : END --><!-- Email Footer : BEGIN --><!-- Email Footer : END --><!-- Full Bleed Background Section : BEGIN --><!-- Full Bleed Background Section : END --><!--[if mso | IE]>    </td>    </tr>    </table>    <![endif]-->

<p>&nbsp;</p>';
  echo '<p style="color: #159a15;font-size: 18px;">Details you entered has been submitted successfully. Please refer below for results.</p>
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%" id="cust_det">
        <tbody>                                         
           
         <td style="padding:10px"><table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid rgb(238, 234, 234);">
              <tbody>
                
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Screens</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_screens . '</td>
                </tr> 
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No OF External Interface</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_ext_interface . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Platforms</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_platforms . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Platforms</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $dis_platfrom . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">How Complex</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $complex_text . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">No Of Cycles</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . $no_of_cycles . '</td>
                </tr>
                                
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Preparation effort (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_hr, 2) . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Preparation effort (in day)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_day, 2) . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Test Preparation effort (in month)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($tot_efforts_in_month, 2) . '</td>
                </tr>  

                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Retesting of Test Case failed (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($final_retesting, 2) . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Total Effort for Defect Reporting (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($final_defect_reporting, 2) . '</td>
                </tr>
                <tr>
                  <td bgcolor="#ffffff" style="padding:15px;border-right:1px solid #eeeaea;width:50%;color: #060606;font-weight: 700;">Total effort for Test Execution (in hrs)</td>
                  <td bgcolor="#ffffff" style="padding:15px;width:50%;color: #f74d00;">' . round($final_total_effort_text_excution, 2) . '</td>
                </tr>              
              </tbody>
            </table></td>                       
                                                                
                    </tbody>
                </table>
                </td>
            </tr>
        </tbody>
    </table>';
  $headers = 'From:' . $_POST['fpc_user_email'] . "\r\n";
  $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
    'MIME-Version: 1.0' . "\r\n\r\n";
  $subject = 'Testbytes - Test Execution Effort Calculation';
  $to = 'info@testbytes.net';
  die;
}
?>