const getGeoDetails = require("../apiCalls/geoplugingApiCall");
const requestIp = require("request-ip");
const number_format = require("number_format-php");
const {
  getUserLocationDetails,
} = require("../serviceControllers/getUserLocationDetails");
const {
  getMarketPriceFromSkillID,
} = require("../serviceControllers/getMarketPriceFromSkillID");

exports.nextFunction = async (req, res) => {
  //Find IP address of user
  let ipAddress =
    requestIp.getClientIp(req) ||
    req.header["x-forwarded-for"] ||
    req.socket.remoteAddress ||
    "UNKNOWN";
  try {
    // Find location details from IP address by geoPlugin api call
    getGeoDetails(
      // ::1 It is the equivalent of the IPV4 address 127.0. 0.1
      ipAddress == "::1" ? "127.0. 0.1" : ipAddress,
      function async(err, response) {
        if (err) {
          throw err;
        } else {
          // Make custom variables for location details
          let user_location = "";
          let user_ip = "";
          let user_currencyCode;
          let currencyCode_set;
          let user_countryCode;
          let countryCode_set;
          let user_geoplugin_currencyConverter;
          let currency_set;
          let user_currencysymbol;
          let curr_sym_set;
          let user_ContinentName;
          let location_set = "";
          let user_city = "";
          let user_region = "";
          let user_CountryName = "";

          // Here response is geoPlugin api call response which gives object of location details
          if (response !== false && response !== "") {
            // Iterate response object
            for (const i in response) {
              // Check if response is not null
              if (response[i] !== null && response[i] !== "") {
                if (i == "geoplugin_request") {
                  user_ip = response[i] ? response[i] : "";
                } else if (i == "geoplugin_city") {
                  user_city = response[i] ? response[i] : "";
                  user_location += user_city;
                } else if (i == "geoplugin_region") {
                  user_region = response[i] ? response[i] : "";
                  user_location += user_region;
                } else if (i == "geoplugin_countryName") {
                  user_CountryName = response[i] ? response[i] : "";
                  user_location += user_CountryName;
                } else if (i == "geoplugin_currencyCode") {
                  user_currencyCode = response[i] ? response[i] : "";
                  currencyCode_set = response[i] ? 1 : 0;
                } else if (i == "geoplugin_countryCode") {
                  user_countryCode = response[i] ? response[i] : "";
                  countryCode_set = response[i] ? 1 : 0;
                } else if (i == "geoplugin_currencyConverter") {
                  user_geoplugin_currencyConverter = response[i]
                    ? response[i]
                    : "";
                  currency_set = response[i] ? 1 : 0;
                } else if (i == "geoplugin_currencySymbol_UTF8") {
                  user_currencysymbol = response[i] ? response[i] : "";
                  curr_sym_set = response[i] ? 1 : 0;
                } else if (i == "user_ContinentName") {
                  user_ContinentName = response[i]
                    ? response[i]
                    : "North America";
                }
              }
            }
          } else {
            user_location = "Couldn't Find Location";
            currencyCode_set = 0;
            currency_set = 0;
            location_set = 0;
            user_city = "";
            user_region = "";
            user_CountryName = "";
            user_countryCode = "";
          }
          if (user_location.trim() == "") {
            user_location = "Couldn't Find Any Location";
            currencyCode_set = 0;
            currency_set = 0;
            location_set = 0;
            user_city = "";
            user_region = "";
            user_CountryName = "";
            user_countryCode = "";
          }
          // Final api response
          res.status(200).json({
            result: true,
            error: false,
            message: "success",
            data: {
              user_location_data: {
                user_location,
                currencyCode_set,
                currency_set,
                location_set,
                user_city,
                user_region,
                user_CountryName,
                user_countryCode,
              },
              fpc_calculation_data: {
                user_geoplugin_currencyConverter,
                currency_set,
                curr_sym_set,
              },
            },
          });
        }
      }
    );
  } catch (error) {
    res.status(error.statusCode || 500).json({
      result: false,
      error: true,
      message: error.message,
      data: null,
    });
  }
};

exports.nextFunction2 = async (req, res) => {
  // get all inputs fields
  let {
    category,
    text01,
    fpc_no_of_screens,
    kind_of_testing,
    ext_int_radio,
    fpc_platforms,
    cycles_radio,
    fpc_user_name,
    fpc_user_email,
    fpc_user_mobile,
    fpc_user_message,
  } = req.body;

  // testCostCalculator 1/5
  if (category.includes("Others")) {
    var dis_category = text01;
    var db_category = text01;
  } else {
    var dis_category = category.join();
    var db_category = dis_category;
  }

  // testCostCalculator 2/5
  let mail_title = "TEST EFFORT CALCULATOR";
  let no_of_screens = parseInt(fpc_no_of_screens);

  // testCostCalculator 3/5
  if (ext_int_radio[0] == "0-3") {
    var no_ext_interface = 3;
    var no_ext_interface_text = ext_int_radio[0];
    var complexity_arr = 24;
  } else if (ext_int_radio[0] == "4") {
    var no_ext_interface = 4;
    var no_ext_interface_text = ext_int_radio[0];
    var complexity_arr = 49;
  } else if (ext_int_radio[0] == "5") {
    var no_ext_interface = 5;
    var no_ext_interface_text = ext_int_radio[0];
    var complexity_arr = 74;
  } else if (ext_int_radio[0] == "5+") {
    var no_ext_interface = 6;
    var no_ext_interface_text = ext_int_radio[0];
    var complexity_arr = 100;
  }

  // complexity
  if (complexity_arr < 25) {
    var complex_text = "Simple";
    var complexity_value = 2;
    var complexity_factor = 1;
  } else if (complexity_arr < 50) {
    var complex_text = "Medium";
    var complexity_value = 3;
    var complexity_factor = 1.5;
  } else if (complexity_arr < 75) {
    var complex_text = "Complex";
    var complexity_value = 4;
    var complexity_factor = 2;
  } else if (complexity_arr <= 100) {
    var complex_text = "Very Complex";
    var complexity_value = 5;
    var complexity_factor = 2.5;
  }

  // Testing
  if (kind_of_testing && kind_of_testing !== null) {
    mail_title = "Mobile App Testing Calculator";
    var estimation_for = "mobile_app_testing";
    var kind_count = kind_of_testing.length;
    var dis_kind = kind_of_testing.join();
    console.log("kind_count", kind_count);

    if (kind_of_testing.includes("Functional Testing")) {
      kind_count -= 1;
    }

    if (kind_count != 0) {
      if (kind_of_testing.includes("Manual Testing")) {
        kind_count -= 1;
      }
    }

    no_ext_interface = no_ext_interface + kind_count;
    console.log("no_ext_interface", no_ext_interface);
  }

  let platforms_arr = [...fpc_platforms];
  let no_of_cycles = parseInt(cycles_radio[0]);

  if (platforms_arr.includes("Both")) {
    var index = platforms_arr.indexOf("Both");
    if (index > -1) {
      platforms_arr.splice(index, 1);
    }
    var no_of_platforms = 1;
    var dis_platfrom = platforms_arr.join();
  } else {
    var no_of_platforms = platforms_arr.length;
    var no_of_platforms = 0.5;
    var dis_platfrom = platforms_arr.join();
  }

  /* 
    Function Point Analysis(FPA) = TotalCounts(un-adjusted FP) x complexity_adjustment_factor
    where : 
      - TotalCounts(un-adjusted FP) = Σ[counts/inputs/infoDomainValues x Avarage table values]

      - Here, counts/inputs/infoDomainValues : 1.no_of_screens, 2.no_ext_interface, 3.no_of_platforms

        Function Units	Low  Avg	 High
        --------------------------------
          EI	           3	  4	    6
          EO	           4	  5	    7
          EQ	           3	  4	    6
          ILF	           7	  10 	  15
          EIF	           5	  7	    10
        --------------------------------

      - complexity_adjustment_factor = [0.65 + 0.01 Σ(fi)] 
      
      - functional_val Σ(fi) = 14 questions x complexity_value 

      - complexity_value : predefind in our system different for simple, medium, complex and v.complex
  */
  let functional_val = 14 * complexity_value;
  let complexity_adjustment_factor = 0.65 + 0.01 * functional_val;
  console.log("complexity_value", complexity_value);
  console.log("no_of_screens", no_of_screens);
  console.log("no_ext_interface", no_ext_interface);
  console.log("no_of_platforms", no_of_platforms);
  let unadjusted_functional_point =
    complexity_value * no_of_screens +
    complexity_value * no_ext_interface +
    complexity_value * no_of_platforms;
  console.log("unadjusted_functional_point", unadjusted_functional_point);
  let functional_point =
    unadjusted_functional_point * complexity_adjustment_factor;
  console.log("functional_point = ", functional_point);
  let efforts_in_hr = complexity_value * functional_point;
  let efforts_in_day = efforts_in_hr / 8;
  console.log("efforts_in_hr = ", efforts_in_hr);
  let efforts_in_month = efforts_in_day / 20;
  console.log("efforts_in_month = ", efforts_in_month);
  let tot_efforts_in_hr = efforts_in_hr * no_of_platforms;
  let tot_efforts_in_day = tot_efforts_in_hr / 8;
  let tot_efforts_in_month = tot_efforts_in_day / 20;

  let test_env_setup = (28 / 50) * (complexity_factor * 5.05);
  console.log("test_env_setup", test_env_setup);
  let test_data_preparation = (28 / 50) * (complexity_factor * 5.05);
  console.log("test_data_preparation", test_data_preparation);
  let test_config_setup = (28 / 50) * (complexity_factor * 5.05); //---------------------------------------------------
  console.log("test_config_setup", test_config_setup);
  let factor = 1.2;
  let one_minute = 1;
  let one_minute_in_hrs = 1 / 60;

  if (complexity_arr < 25) {
    var factor_in_min = 4;
    var factor_in_hrs = one_minute_in_hrs * factor_in_min;
  } else if (complexity_arr < 50) {
    var factor_in_min = 6;
    var factor_in_hrs = one_minute_in_hrs * factor_in_min;
  } else if (complexity_arr < 75) {
    var factor_in_min = 8;
    var factor_in_hrs = one_minute_in_hrs * factor_in_min;
  } else if (complexity_arr <= 100) {
    var factor_in_min = 10;
    var factor_in_hrs = one_minute_in_hrs * factor_in_min;
  }

  let defect_reporting = one_minute_in_hrs * 15;
  let retesting_ten_mins = 0.166666666666667;
  let testcases = Math.pow(functional_point, factor);
  console.log("testcases", testcases);
  let total_time_in_hrs = testcases * factor_in_hrs;
  let total_time_defect_reporting = (testcases / 2) * defect_reporting;
  // =========Cycle 1============
  let total_effort_for_test_execution_cy1 =
    total_time_defect_reporting + total_time_in_hrs;
  let retesting_testcases_failed_after_cy1 =
    (testcases / 2) * retesting_ten_mins;
  // =========Cycle 2============
  let total_time_defect_reporting_cy2 = (testcases / 4) * defect_reporting;
  let total_effort_for_test_execution_cy2 = total_effort_for_test_execution_cy1;
  // =========Cycle 3============
  let total_time_defect_reporting_cy3 = (testcases / 4) * defect_reporting;
  let total_effort_for_test_execution_cy3 =
    total_time_defect_reporting_cy3 + total_time_in_hrs;
  let retesting_testcases_failed_after_cy3 =
    (retesting_testcases_failed_after_cy1 / 4) * retesting_ten_mins;
  // =========Cycle 4 ============
  let total_time_defect_reporting_cy4 = (testcases / 8) * defect_reporting;
  let total_effort_for_test_execution_cy4 =
    total_time_defect_reporting_cy4 + total_time_in_hrs;
  let retesting_testcases_failed_after_cy4 =
    (retesting_testcases_failed_after_cy1 / 8) * retesting_ten_mins;
  // =========Cycle 5 ==========
  let total_time_defect_reporting_cy5 = (testcases / 16) * defect_reporting;
  let total_effort_for_test_execution_cy5 =
    total_time_defect_reporting_cy5 + total_time_in_hrs;
  let retesting_testcases_failed_after_cy5 =
    (retesting_testcases_failed_after_cy1 / 16) * retesting_ten_mins;
  let final_mail_result = "";
  let final_user_result = `<table class="table" style="border: 1px solid rgb(238, 234, 234);">
                        <thead>
                          <tr bgcolor="#dcdcdc">
                            <th style="vertical-align:middle;text-align: center;">Cycle</th>
                            <th style="vertical-align:middle;text-align: center;">Total Effort for Defect Reporting (in hrs)</th>
                            <th style="vertical-align:middle;text-align: center;">Total effort for Test Execution (in hrs)</th>
                            <th style="vertical-align:middle;text-align: center;">Retesting of Test Case failed (in hrs)</th>
                          </tr>
                        </thead>
                      <tbody>`;

  let col_tot_exe = 0;
  let col_tot_defect = 0;
  let col_tot_retest = 0;
  let col_prepare = 0;
  let divider = Math.round(no_of_cycles * (5 - complexity_factor), 2);
  let prepare_for_each_cycle =
    (tot_efforts_in_hr +
      test_env_setup +
      test_data_preparation +
      test_config_setup) /
    divider;

  if (no_of_cycles >= 1) {
    var final_retesting = retesting_testcases_failed_after_cy1;
    var final_defect_reporting = total_time_defect_reporting;
    var final_total_effort_text_excution = total_effort_for_test_execution_cy1;

    col_tot_exe = col_tot_exe + final_total_effort_text_excution;
    col_tot_defect = col_tot_defect + final_defect_reporting;
    col_tot_retest = col_tot_retest + final_retesting;
    col_prepare = col_prepare + prepare_for_each_cycle;

    console.log("cycle1\n");
    console.log("final_retesting", final_retesting);
    console.log("final_defect_reporting", final_defect_reporting);
    console.log(
      "final_total_effort_text_excution",
      final_total_effort_text_excution
    );
    console.log("col_tot_exe", col_tot_exe);
    console.log("col_tot_defect", col_tot_defect);
    console.log("col_tot_retest", col_tot_retest);
    console.log("col_prepare", col_prepare);
    console.log("prepare_for_each_cycle", prepare_for_each_cycle);

    final_user_result += `<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">1</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_defect_reporting,
                            2
                          )}</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_total_effort_text_excution,
                            2
                          )}</td>
                          <td style="text-align: center;">${Math.round(
                            final_retesting,
                            2
                          )}</td>
                        </tr>`;
    final_mail_result += `<tr>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">1</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${prepare_for_each_cycle}</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_total_effort_text_excution,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_defect_reporting,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_retesting,
                                      2
                                    )}</h3>           
                                </td>
                            </tr>`;
  }
  if (no_of_cycles >= 2) {
    var final_retesting = retesting_testcases_failed_after_cy3;
    var final_defect_reporting = total_time_defect_reporting_cy2;
    var final_total_effort_text_excution = total_effort_for_test_execution_cy2;

    col_tot_exe = col_tot_exe + final_total_effort_text_excution;
    col_tot_defect = col_tot_defect + final_defect_reporting;
    col_tot_retest = col_tot_retest + final_retesting;
    col_prepare = col_prepare + prepare_for_each_cycle;
    console.log("cycle2\n");
    console.log("final_retesting", final_retesting);
    console.log("final_defect_reporting", final_defect_reporting);
    console.log(
      "final_total_effort_text_excution",
      final_total_effort_text_excution
    );
    console.log("col_tot_exe", col_tot_exe);
    console.log("col_tot_defect", col_tot_defect);
    console.log("col_tot_retest", col_tot_retest);
    console.log("col_prepare", col_prepare);
    console.log("prepare_for_each_cycle", prepare_for_each_cycle);

    final_user_result += `<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">2</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_defect_reporting,
                            2
                          )}</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_total_effort_text_excution,
                            2
                          )}</td>
                          <td style="text-align: center;">${Math.round(
                            final_retesting,
                            2
                          )}</td>
                        </tr>`;
    final_mail_result += `<tr style="background: #efefef;">
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">2</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${prepare_for_each_cycle}</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_total_effort_text_excution,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_defect_reporting,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_retesting,
                                      2
                                    )}</h3>           
                                </td>
                            </tr>`;
  }
  if (no_of_cycles >= 3) {
    var final_retesting = retesting_testcases_failed_after_cy4;
    var final_defect_reporting = total_time_defect_reporting_cy3;
    var final_total_effort_text_excution = total_effort_for_test_execution_cy3;

    col_tot_exe = col_tot_exe + final_total_effort_text_excution;
    col_tot_defect = col_tot_defect + final_defect_reporting;
    col_tot_retest = col_tot_retest + final_retesting;
    col_prepare = col_prepare + prepare_for_each_cycle;

    final_user_result += `<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">3</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_defect_reporting,
                            2
                          )}</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_total_effort_text_excution,
                            2
                          )}</td>
                          <td style="text-align: center;">${Math.round(
                            final_retesting,
                            2
                          )}</td>
                        </tr>`;
    final_mail_result += `<tr>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">3</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> ${prepare_for_each_cycle} </h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> ${Math.round(
                                      final_total_effort_text_excution,
                                      2
                                    )} </h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> ${Math.round(
                                      final_defect_reporting,
                                      2
                                    )} </h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> ${Math.round(
                                      final_retesting,
                                      2
                                    )} </h3>           
                                </td>
                            </tr>`;
  }
  if (no_of_cycles >= 4) {
    var final_retesting = retesting_testcases_failed_after_cy5;
    var final_defect_reporting = total_time_defect_reporting_cy4;
    var final_total_effort_text_excution = total_effort_for_test_execution_cy4;

    col_tot_exe = col_tot_exe + final_total_effort_text_excution;
    col_tot_defect = col_tot_defect + final_defect_reporting;
    col_tot_retest = col_tot_retest + final_retesting;
    col_prepare = col_prepare + prepare_for_each_cycle;

    final_user_result += `<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">4</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_defect_reporting,
                            2
                          )}</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_total_effort_text_excution,
                            2
                          )}</td>
                          <td style="text-align: center;">${Math.round(
                            final_retesting,
                            2
                          )}</td>
                        </tr>`;
    final_mail_result += `<tr style="background: #efefef;">
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">4</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${prepare_for_each_cycle}</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_total_effort_text_excution,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_defect_reporting,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_retesting,
                                      2
                                    )}</h3>           
                                </td>
                            </tr>`;
  }
  if (no_of_cycles >= 5) {
    var final_retesting = retesting_testcases_failed_after_cy5 / 2;
    var final_defect_reporting = total_time_defect_reporting_cy5;
    var final_total_effort_text_excution = total_effort_for_test_execution_cy5;

    col_tot_exe = col_tot_exe + final_total_effort_text_excution;
    col_tot_defect = col_tot_defect + final_defect_reporting;
    col_tot_retest = col_tot_retest + final_retesting;

    col_prepare = col_prepare + prepare_for_each_cycle;

    final_user_result += `<tr>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">5</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_defect_reporting,
                            2
                          )}</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            final_total_effort_text_excution,
                            2
                          )}</td>
                          <td style="text-align: center;">${Math.round(
                            final_retesting,
                            2
                          )}</td>
                        </tr>`;

    final_mail_result += `<tr>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">5</h3>           
                                </td>
                                <td class="heding-table" align="center">
                                  <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${prepare_for_each_cycle}</h3>
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_total_effort_text_excution,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_defect_reporting,
                                      2
                                    )}</h3>           
                                </td>
                                <td class="heding-table" align="center">        
                                    <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                                      final_retesting,
                                      2
                                    )}</h3>           
                                </td>
                            </tr>`;
  }

  final_user_result += `<tr bgcolor="#dcdcdc">
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">Total</td>                        
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            col_tot_exe,
                            2
                          )}</td>
                          <td style="border-right:1px solid rgb(221, 221, 221);text-align: center;">${Math.round(
                            col_tot_defect,
                            2
                          )}</td>
                          <td style="text-align: center;">${Math.round(
                            col_tot_retest,
                            2
                          )}</td>
                        </tr>`;
  final_user_result += `</tbody>
                  </table>`;

  final_mail_result += `<tr style="background: #ef4d30;">
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">Total</h3>           
                          </td>
                          <td class="heding-table" align="center">
                            <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">${Math.round(
                              col_prepare,
                              2
                            )}</h3>
                          </td>
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">${Math.round(
                                col_tot_exe,
                                2
                              )}</h3>           
                          </td>
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">${Math.round(
                                col_tot_defect,
                                2
                              )}</h3>           
                          </td>
                          <td class="heding-table" align="center">        
                              <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">${Math.round(
                                col_tot_retest,
                                2
                              )}</h3>           
                          </td>
                      </tr>`;

  let mail_currency = "USD";
  let mail_currency_sym = "$";
  let timeline_value = Math.round(
    (col_tot_exe + col_tot_defect + col_tot_retest + col_prepare) / 8 / 20,
    2
  );
  let total_effort_mail = Math.round(
    col_tot_exe + col_tot_defect + col_tot_retest + col_prepare,
    2
  );
  let total_effort_mail_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">${total_effort_mail}&nbsp;<B style="font-size: 19px; margin-left: -18px;color: #585858;font-weight: 500;">&nbsp; Hrs.</B></h2>`;
  let pri_api_url =
    "https://softbreaksapi.azurewebsites.net/api/JobPosts/MarketPriceForJobs";

  let pri_data = {
    SkillID: 28,
  };

  try {
    const { user_location_data, fpc_calculation_data } =
      await getUserLocationDetails(req,res);
    const getMarketPriceFromSkillIDData = await getMarketPriceFromSkillID(
      pri_api_url,
      pri_data,
      res
    );
    let {
      user_geoplugin_currencyConverter,
      currency_set,
      curr_sym_set,
      currencyCode_set,
      user_currencyCode,
      user_currencysymbol,
    } = fpc_calculation_data;

    let min_cost;
    let max_cost;
    if (
      getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"][
        "MinRange"
      ] != "" &&
      getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"][
        "MaxRange"
      ] != ""
    ) {
      min_cost =
        getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"][
          "MinRange"
        ];
      max_cost =
        getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"][
          "MaxRange"
        ];
    } else {
      min_cost = 3.78;
      max_cost = 13.33;
    }

    let total_cost_min_db = Math.round(total_effort_mail * min_cost, 2);
    let total_cost_max_db = Math.round(total_effort_mail * max_cost, 2);
    let total_cost_min = Math.round(total_effort_mail * min_cost, 2);
    let total_cost_max = Math.round(total_effort_mail * max_cost, 2);
    let mail_currency_sym;
    if (currency_set == 1 && currencyCode_set == 1) {
      total_cost_min = Math.round(
        total_cost_min_db * user_geoplugin_currencyConverter
      );
      total_cost_max = Math.round(
        total_cost_max_db * user_geoplugin_currencyConverter
      );
      if (curr_sym_set == 1) {
        mail_currency_sym = user_currencysymbol;
      }

      mail_currency = user_currencyCode;
    }
    let grandtotalEfforts = "";
    category = "";
    dis_kind = "";
    let effort_details = "";
    let cost_range = `${number_format(total_cost_min)} - ${number_format(
      total_cost_max
    )}`;
    mail_currency_sym = "";
    console.log("total_cost_min -->", total_cost_min);
    console.log("total_cost_max -->", total_cost_max);
    console.log("cost_range -->", cost_range);

    let mail_total_cost_display;
    let user_total_cost_display;
    let db_country_cost;
    let total_cost = 0;
    console.log("mail_currency_sym-->", mail_currency_sym);

    if (mail_currency_sym != "") {
      mail_total_cost_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">' . ${mail_currency_sym} . ${number_format(
        total_cost
      )} . '</h2>`;
    } else {
      mail_total_cost_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">${cost_range} &nbsp;&nbsp;<B style="font-size: 19px; margin-left: 0px;color: #585858;font-weight: 500;">&nbsp;&nbsp;${mail_currency}</B></h2>`;

      user_total_cost_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">${cost_range}' . '<B style="font-size: 19px; margin-left: -18px;color: #585858;font-weight: 500;">&nbsp;${mail_currency}</B></h2>`;
    }
    db_country_cost = `${number_format(
      total_cost_min
    )} ${mail_currency} - ${number_format(total_cost_max)} ${mail_currency}`;

    console.log("tot_efforts_in_hr", tot_efforts_in_hr);

    let get_template_directory_uri = `<?php echo get_template_directory_uri() ?>`;

    console.log("dis_kind", dis_kind);

    // Returns a string with the first letter in upper case of each word.
    function ucwords(str) {
      return (str + "").replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
      });
    }

    let exact_result = `<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#000000;font-size:14px;font-family:Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;">
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
                          <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/logo1.png" alt="Thank you for trusting us to help you find a solution" width="170" height="50"
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
                        <img src="<?php echo get_template_directory_uri(); ?>/images/Graphic.png" alt="Thank you for trusting us to help you find a solution">
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
                <h2 class="title" style="margin: 7px 0px;font-weight: 500;font-size: 20px;color: #585858;text-align:center;">${mail_title}</h2>
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
                <p style="font-size: 17px;color: #989696;line-height: 25px;padding: 1px 20px;font-weight: 500;text-align: left;">Dear ${fpc_user_name}</p>
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
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${ucwords(
                 dis_category
               )}</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of Screens</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${no_of_screens}</h3> 
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of External Interface</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${no_ext_interface_text}</h3> 
              </td>
            </tr>`;

    if (kind_of_testing && kind_of_testing != null) {
      exact_result += `<tr>
                        <td class="heding-table" align="center">
                          <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> Testing type</h3> 
                        </td>
                        <td class="heding-table" align="center">
                          <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${dis_kind}</h3> 
                        </td>
                      </tr>`;
    }

    exact_result += `<tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;"> Platforms</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${dis_platfrom}</h3> 
              </td>
            </tr>
             <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">How Complex</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${complex_text}</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of Cycles</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${no_of_cycles}</h3> 
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
               <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">No Of Test Cases</h3> 
              </td>
              <td class="heding-table" align="center">
               <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                 testcases
               )}</h3> 
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
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                  tot_efforts_in_hr,
                  2
                )}</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Environment Setup (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                  test_env_setup,
                  2
                )}</h3> 
              </td>
            </tr>
            <tr style="background: #efefef;">
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Data Preparation (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                  test_data_preparation,
                  2
                )}</h3> 
              </td>
            </tr>
            <tr>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">Test Configuration Setup (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${Math.round(
                  test_config_setup,
                  2
                )}</h3> 
              </td>
            </tr>
            <tr style="background: #ef4d30;">
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">Total Test Preparation Effort (in hrs)</h3> 
              </td>
              <td class="heding-table" align="center">
                <h3 style="font-weight: 500;font-size: 16px;color: #fff;padding: 14px 0;margin: 0;">${Math.round(
                  tot_efforts_in_hr +
                    test_env_setup +
                    test_data_preparation +
                    test_config_setup,
                  2
                )}</h3> 
              </td>
            </tr>
            <tr>
              <td colspan="2" style="    PADDING: 0px;">
                &nbsp;
              </td>
            </tr>`;

    // Added 12 more line as per the tabel
    exact_result += `

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
                            ${final_mail_result}
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
            </tr>`;

    let grand_cost = `
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
                        <td align="right" style="float: right;background: #ececec;border-bottom-left-radius: 50px;border-top-left-radius: 50px;padding: 12px;border: 1px solid #d5312d;border-right: none;">`;

    let timeline_val;

    if (typeof grandtotalEfforts !== "undefined") {
      console.log("condi execute 111");
      if (grandtotalEfforts < 800) {
        timeline_val = 1;
      } else if (grandtotalEfforts >= 800 && grandtotalEfforts <= 1500) {
        timeline_val = 2;
      } else if (grandtotalEfforts > 1500) {
        timeline_val = 3;
      }
    }

    grand_cost += `<h2 class="title" style="margin: 10px 0px;font-weight: 100;font-size: 22px;color: #000000;padding: 0px 16px;">TIMELINE  &nbsp; <b style="color:#000000;font-weight: 500;">${timeline_value} Month (approx.)</b></h2>
                                
    </td>
      </tr>
        </tbody>
          </table>
            </td>
              </tr>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
              <tr>
              <td colspan="2" align="center" style="padding: 15px 27px 0px;font-size: 12px;FONT-WEIGHT: 500;">
              <h2 class="title" style="font-weight: 500;font-size: 20px;color: #585858;margin: 10px 3px 0px;">TOTAL AMOUNT IS</h2>
            </td>
        </tr>
      <tr>
    <td colspan="2" align="center" style="FONT-WEIGHT: 500;">`;

    grand_cost +=
      mail_total_cost_display +
      `</td>                  
                  </tr>
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
                    <td colspan="2" align="center" style="padding: 0px 100px;FONT-WEIGHT: 500;">`;

    grand_cost +=
      total_effort_mail_display +
      `</td>                  
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
                              <img src="<?php echo get_template_directory_uri(); ?>/images/web_mail_icn.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
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
                              <img src="<?php echo get_template_directory_uri(); ?>/images/phone_mail_icn.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
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
                                <img src="<?php echo get_template_directory_uri(); ?>/images/whatsapp_icn.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
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
                                <img src="<?php echo get_template_directory_uri(); ?>/images/skype.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16), 0 3px 6px rgba(199, 23, 23, 0.23);">
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
                    </tr>`;

    let user_details = `<tr>
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
                     <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${fpc_user_name}</h3> 
                    </td>
                  </tr>
                  <tr>
                    <td class="heding-table" align="center">
                      <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Mail</h3> 
                    </td>
                    <td class="heding-table" align="center">
                      <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${fpc_user_email}</h3> 
                    </td>
                  </tr>
                  <tr style="background: #efefef;">
                    <td class="heding-table" align="center">
                     <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Mobile</h3> 
                    </td>
                    <td class="heding-table" align="center">
                     <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${fpc_user_mobile}</h3> 
                    </td>
                  </tr>
                  <tr>
                    <td class="heding-table" align="center">
                     <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Location</h3> 
                    </td>
                    <td class="heding-table" align="center">
                     <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${user_location_data}</h3> 
                    </td>
                  </tr> 
                  <tr style="background: #efefef;">
                    <td class="heding-table" align="center">
                      <h3 style="font-weight: 500;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">User Message</h3> 
                    </td>
                    <td class="heding-table" align="center">
                      <h3 style="font-weight: normal;font-size: 16px;color: #616161;padding: 14px 0;margin: 0;">${fpc_user_message}</h3> 
                    </td>
                  </tr>
                  <tr>
                  <td colspan="2" style="    PADDING: 0px;">
                    &nbsp;
                  </td>
                </tr>`;

    let result_footer = `<tr>
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
                       <img src="<?php echo get_template_directory_uri(); ?>/images/Graphic.png" alt="" style="transform: rotate(180deg);">
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
                                <img src="<?php echo get_template_directory_uri(); ?>/images/location.png" alt="" style="background: white;border: 1px solid transparent;border-radius: 50px;box-shadow: 0 3px 6px rgba(239, 4, 4, 0.16);">
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
        </body>`;

    // Final api response
    // res.status(200).json({
    //   result: true,
    //   error: false,
    //   message: "success",
    //   data: { ...user_location_data },
    // });
    res.status(200).send(grand_cost);
  } catch (error) {
    res.status(error.statusCode || 500).json({
      result: false,
      error: true,
      message: error.message,
      data: null,
    });
  }
};

// testBytes input fields : -

// category[]: Others
// text01: testDomain
// fpc_no_of_screens: 1
// ext_int_radio[]: 4
// fpc_complexity: 49
// fpc_platforms[]: Mobile
// fpc_complexity: 0
// cycles_radio[]: 3
// fpc_user_name: test
// fpc_user_email: test@gm.com
// fpc_user_mobile: 4999999
// fpc_user_message: test
// sms_check: yes
// action: fpc_cal
