/*
====================================================================================================
CONSTANTS / MODULES
====================================================================================================
*/
const ejs = require("ejs");
const path = require("path");
const moment = require("moment");
const number_format = require("number_format-php");
const requestIp = require("request-ip");
const  wpFeSanitizeTitle  = require("../serviceControllers/sanitize_title()");
const { getMarketPriceApiCall } = require("../apiCalls/getMarketPriceApiCall");
const { getUserLocationDetails } = require("../serviceControllers/getUserLocationDetails");
const { handleSendGrideEmailSend } = require("../serviceControllers/handleSendGrideEmailSend");
const { testcost_enquiries } = require("../serviceControllers/testcost_enquiries");
const { html_to_pdf } = require("../serviceControllers/html_to_pdf");
const { ipstackApiCall } = require("../apiCalls/ipstackApiCall");
const { testcost_pdf } = require("../serviceControllers/testcost_pdf");
const { sendTextMsgApiCall } = require("../apiCalls/sendTextMsgApiCall");
const currencyExchangeRate = require("currency-exchange-rate");
const fs = require('fs');
const { twilioWhatsappApiCall } = require("../apiCalls/twilioWhatsappApiCall");

/*
====================================================================================================
TESTREVEAL (TEST-COST-CALCULATOR) API
====================================================================================================
*/
exports.testCostCalculator = async (req, res) => {
  console.log("Testreveal - Test cost calculator API running...");
  try {
    // const smsResponse = await sendTextMsgApiCall(917414969691);
    // console.log("smsResponse", smsResponse);
    /*
    ==============================================================================================================================
    REQUIRE TESTREVEAL (TEST-COST-CALCULATOR) ALL STEPS INPUTS
    ==============================================================================================================================
    */
    let {
      category,
      text01,
      fpc_no_of_screens,
      kind_of_testing, // body input : "kind_of_testing":["Functional Testing"],
      ext_int_radio,
      fpc_platforms,
      cycles_radio,
      sms_check,
      action,
      /*
      ===================
      INPUTS OF STEP 6/6
      ===================
      */
      fpc_user_name,    
      fpc_user_email,
      fpc_user_mobile,
      fpc_user_message,
      /*
      =======================
      INPUTS OF USER LOCATION
      =======================
      */
      country_code,
      country_name,
      region_name,
      city,
      currency,
    } = req.body;
    /*
    ====================================================================================================
    INPUTS VALIDATION
    ====================================================================================================
    */
    if(
      Array.isArray(category) && Array.isArray(ext_int_radio) &&
      Array.isArray(fpc_platforms) && Array.isArray(cycles_radio) && 
      typeof text01 === "string" && typeof fpc_user_name === "string" && 
      typeof fpc_user_email === "string" && typeof fpc_user_message === "string" &&
      typeof sms_check === "string" && typeof action === "string" &&
      typeof fpc_no_of_screens === "number" && typeof fpc_user_mobile === "number"
    ){
      if(
        !category.length || !ext_int_radio.length || !fpc_platforms.length || !cycles_radio.length ||
         fpc_user_name.trim() === "" || fpc_user_email.trim() === "" || 
        fpc_user_message.trim() === "" || sms_check.trim() === "" || action.trim() === "" ||
        fpc_no_of_screens === '' || fpc_user_mobile === '' ||
        fpc_no_of_screens === 0 || fpc_user_mobile === 0
      ){
        let emptyError = new Error("please fill all fields properly!")
        emptyError.statusCode = 202;
        throw emptyError
      }
    }else{
      let invalidTypeErr = new Error("invalid fields type!")
      invalidTypeErr.statusCode = 202;
      throw invalidTypeErr
    }

    // Validation of User Location Inputs
    if(country_code.trim() === "" || country_name.trim() === "" ||
      region_name.trim() === "" || city.trim() === "" || currency === {} || currency.code.trim() === ""){
        let locationEmptyError = new Error("Somthing went wrong! Please fill the form again.");
        locationEmptyError.statusCode = 500;
        throw locationEmptyError
    }
    /*
    ====================================================================================================
    DEFAULTS
    ====================================================================================================
    */
    let mail_title = "TEST EFFORT CALCULATOR";
    let templatesData = {};                    // make a object for storing template data
    let mail_currency = "USD";              
    let mail_currency_sym = "$";
    let ipAddress = requestIp.getClientIp(req) || req.header["x-forwarded-for"] || req.socket.remoteAddress || "UNKNOWN";           
    /*
    =============================================================================================================================
    DETERMINE INPUTS OF STEP 1/6
    =============================================================================================================================
    */
    if (category?.includes("Others")) {
      var dis_category = text01;
      var db_category = text01;
    } else {
      var dis_category = category.join();
      var db_category = dis_category;
    }
    /*
    ==============================================================================================================================
    DETERMINE INPUTS OF STEP 2/6
    ==============================================================================================================================
    */
    let no_of_screens = parseInt(fpc_no_of_screens);
    /*
    =============================================================================================================================
    DETERMINE INPUTS OF STEP 3/6
    =============================================================================================================================
    */
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
    // COMPLEXITY
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
    // TESTING TYPE
    if (typeof kind_of_testing !== "undefined") {
      mail_title = "Mobile App Testing Calculator";
      var estimation_for = "mobile_app_testing";
      var kind_count = kind_of_testing.length;
      var dis_kind = kind_of_testing.join();

      if (kind_of_testing.includes("Functional Testing")) {
        kind_count -= 1;
      }
      if (kind_count != 0) {
        if (kind_of_testing.includes("Manual Testing")) {
          kind_count -= 1;
        }
      }

      no_ext_interface = no_ext_interface + kind_count;
    }
    /*
    =============================================================================================================================
    DETERMINE INPUTS OF STEP 4/6
    =============================================================================================================================
    */
    let platforms_arr = [...fpc_platforms];

    if (platforms_arr?.includes('Both')) {
      var index = platforms_arr.indexOf('Both');
      if (index > -1) {
        platforms_arr.splice(index, 1);
        platforms_arr.push("Web"," Mobile")
      }
      var no_of_platforms = 1;
      var dis_platfrom = platforms_arr.join();
    } else {
      var no_of_platforms = platforms_arr.length;
      var no_of_platforms = 0.5;
      var dis_platfrom = platforms_arr.join();
    }
    /* 
    ====================================================================================================================================
    START FUNCTION POINT CALCULATIONS
    ====================================================================================================================================
      Function Point Analysis(FPA) = TotalCounts(un-adjusted FP) x complexity_adjustment_factor
      where : 
        - TotalCounts(un-adjusted FP) = Σ[counts/inputs/infoDomainValues x Avarage table values]

        - Here, counts/inputs/infoDomainValues : 1.no_of_screens, 2.no_ext_interface, 3.no_of_platforms

          Function Units	Low  Avg	 High
          --------------------------------
            EI	           3	  4	    6
            EO	           4	  5	    7
            EQ	           3	  4	    6S
            ILF	           7	  10 	  15
            EIF	           5	  7	    10
          --------------------------------

        - complexity_adjustment_factor = [0.65 + 0.01 Σ(fi)] 
        
        - functional_val Σ(fi) = 14 questions x complexity_value 

        - complexity_value : predefind in our system different for simple, medium, complex and v.complex
    */
    let functional_val = 14 * complexity_value;
    let complexity_adjustment_factor = 0.65 + (0.01 * functional_val);
    let unadjusted_functional_point =
      (complexity_value * no_of_screens) +
      (complexity_value * no_ext_interface) +
      (complexity_value * no_of_platforms);
    let functional_point = unadjusted_functional_point * complexity_adjustment_factor;
    let efforts_in_hr = complexity_value * functional_point;
    let efforts_in_day = efforts_in_hr / 8;
    let efforts_in_month = efforts_in_day / 20;
    let tot_efforts_in_hr = efforts_in_hr * no_of_platforms;
    let tot_efforts_in_day = tot_efforts_in_hr / 8;
    let tot_efforts_in_month = tot_efforts_in_day / 20;

    let test_env_setup = (28 / 50) * (complexity_factor * 5.05);
    let test_data_preparation = (28 / 50) * (complexity_factor * 5.05);
    let test_config_setup = (28 / 50) * (complexity_factor * 5.05);
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
    let total_time_in_hrs = testcases * factor_in_hrs;
    let total_time_defect_reporting = (testcases / 2) * defect_reporting;
    /*
    =============================================================================================================================
    DETERMINE INPUTS OF STEP 5/6
    =============================================================================================================================
    */
    let no_of_cycles = parseInt(cycles_radio[0]);
    // Cycle 1
    let total_effort_for_test_execution_cy1 = total_time_defect_reporting + total_time_in_hrs;
    let retesting_testcases_failed_after_cy1 = (testcases / 2) * retesting_ten_mins;
    // Cycle 2
    let total_time_defect_reporting_cy2 = (testcases / 4) * defect_reporting;
    let total_effort_for_test_execution_cy2 = total_effort_for_test_execution_cy1;
    // Cycle 3
    let total_time_defect_reporting_cy3 = (testcases / 4) * defect_reporting;
    let total_effort_for_test_execution_cy3 = total_time_defect_reporting_cy3 + total_time_in_hrs;
    let retesting_testcases_failed_after_cy3 = (retesting_testcases_failed_after_cy1 / 4) * retesting_ten_mins;
    // Cycle 4 
    let total_time_defect_reporting_cy4 = (testcases / 8) * defect_reporting;
    let total_effort_for_test_execution_cy4 = total_time_defect_reporting_cy4 + total_time_in_hrs;
    let retesting_testcases_failed_after_cy4 = (retesting_testcases_failed_after_cy1 / 8) * retesting_ten_mins;
    // Cycle 5 
    let total_time_defect_reporting_cy5 = (testcases / 16) * defect_reporting;
    let total_effort_for_test_execution_cy5 = total_time_defect_reporting_cy5 + total_time_in_hrs;
    let retesting_testcases_failed_after_cy5 = (retesting_testcases_failed_after_cy1 / 16) * retesting_ten_mins;
    
    let divider = Math.round(no_of_cycles * (5 - complexity_factor)).toFixed(2);
    let prepare_for_each_cycle = (tot_efforts_in_hr +test_env_setup +test_data_preparation +test_config_setup) /divider;
        
    // COLLUMNS DATA OF CYCYLES
    let col_tot_exe = 0;
    let col_tot_defect = 0;
    let col_tot_retest = 0;
    let col_prepare = 0;
    if (no_of_cycles >= 1) {
      var final_retesting_cyc1 = retesting_testcases_failed_after_cy1;
      var final_defect_reporting_cyc1 = total_time_defect_reporting;
      var final_total_effort_text_excution_cyc1 = total_effort_for_test_execution_cy1;

      col_tot_exe += final_total_effort_text_excution_cyc1;
      col_tot_defect += final_defect_reporting_cyc1;
      col_tot_retest += final_retesting_cyc1;
      col_prepare += prepare_for_each_cycle;

    }
    if (no_of_cycles >= 2) {
      var final_retesting_cyc2 = retesting_testcases_failed_after_cy3;
      var final_defect_reporting_cyc2 = total_time_defect_reporting_cy2;
      var final_total_effort_text_excution_cyc2 =
        total_effort_for_test_execution_cy2;

      col_tot_exe += final_total_effort_text_excution_cyc2;
      col_tot_defect += final_defect_reporting_cyc2;
      col_tot_retest += final_retesting_cyc2;
      col_prepare += prepare_for_each_cycle;
    }
    if (no_of_cycles >= 3) {
      var final_retesting_cyc3 = retesting_testcases_failed_after_cy4;
      var final_defect_reporting_cyc3 = total_time_defect_reporting_cy3;
      var final_total_effort_text_excution_cyc3 =
        total_effort_for_test_execution_cy3;

      col_tot_exe += final_total_effort_text_excution_cyc3;
      col_tot_defect += final_defect_reporting_cyc3;
      col_tot_retest += final_retesting_cyc3;
      col_prepare += prepare_for_each_cycle;
    }
    if (no_of_cycles >= 4) {
      var final_retesting_cyc4 = retesting_testcases_failed_after_cy5;
      var final_defect_reporting_cyc4 = total_time_defect_reporting_cy4;
      var final_total_effort_text_excution_cyc4 =
        total_effort_for_test_execution_cy4;

      col_tot_exe +=  final_total_effort_text_excution_cyc4;
      col_tot_defect +=  final_defect_reporting_cyc4;
      col_tot_retest +=  final_retesting_cyc4;
      col_prepare +=  prepare_for_each_cycle;
    }
    if (no_of_cycles >= 5) {
      var final_retesting_cyc5 = retesting_testcases_failed_after_cy5 / 2;
      var final_defect_reporting_cyc5 = total_time_defect_reporting_cy5;
      var final_total_effort_text_excution_cyc5 =
        total_effort_for_test_execution_cy5;

      col_tot_exe += final_total_effort_text_excution_cyc5;
      col_tot_defect += final_defect_reporting_cyc5;
      col_tot_retest += final_retesting_cyc5;
      col_prepare += prepare_for_each_cycle;
    }

    let timeline_value = ((col_tot_exe + col_tot_defect + col_tot_retest + col_prepare) / 8 / 20 ).toFixed(2);
    let total_effort_mail = (col_tot_exe + col_tot_defect + col_tot_retest + col_prepare).toFixed(2);

    /*
    =============================================================================================================================
    Main API Logic
    =============================================================================================================================
    */
    // const { user_location_data, fpc_calculation_data } = await getUserLocationDetails(ipAddress.substr(7));
    const getMarketPriceFromSkillIDData = await getMarketPriceApiCall();
    // const getLocation = await ipstackApiCall(ipAddress.substr(7))
    // console.log("getLocation", getLocation)

    // currencyConvertor API Implementation
    const currencyConverter = await currencyExchangeRate.getCurrencyExchangeRate({ fromCurrency:"USD", toCurrency: currency.code ? currency.code : mail_currency })

    const user_location_data = {
      user_location: city+' '+region_name+' '+' '+country_name,
      currencyCode_set: currency.code ? 1 : 0,
      currency_set: currencyConverter ? 1 : 0,
      location_set: '',
      user_city:city,
      user_region:region_name,
      user_CountryName:country_name,
      user_countryCode:country_code
    }
    const fpc_calculation_data = {
      currencyConverter,
      currency_set: currencyConverter ? 1 : 0,
      user_currencyCode:currency.code,
      currencyCode_set: currency.code ? 1 : 0,
      curr_sym_set:currency.symbol,
      user_currencysymbol:currency.symbol,
    }

    // let { 
    //   user_geoplugin_currencyConverter,currency_set,
    //   curr_sym_set,currencyCode_set,user_currencyCode,
    //   user_currencysymbol,
    // } = fpc_calculation_data;

    let min_cost;
    let max_cost;
    if (
      typeof getMarketPriceFromSkillIDData !== "string" &&
      getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"]["MinRange"] != "" && 
      getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"]["MaxRange"] != ""
      ) 
    {
      min_cost = getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"]["MinRange"];
      max_cost = getMarketPriceFromSkillIDData["JobSkillsList"][0]["MarketPriceRange"]["MaxRange"];
    } else {
      min_cost = 3.78;
      max_cost = 13.33;
    }

    let total_cost_min_db = Math.round(total_effort_mail * min_cost).toFixed(2);
    let total_cost_max_db = Math.round(total_effort_mail * max_cost).toFixed(2);
    let total_cost_min = Math.round(total_effort_mail * min_cost).toFixed(2);
    let total_cost_max = Math.round(total_effort_mail * max_cost).toFixed(2);
    // let mail_currency_sym;
    if (user_location_data.currency_set == 1 && user_location_data.currencyCode_set == 1) {
      total_cost_min = Math.round(total_cost_min_db * currencyConverter);
      total_cost_max = Math.round(total_cost_max_db * currencyConverter);
      if (fpc_calculation_data.curr_sym_set == 1) {
        mail_currency_sym = fpc_calculation_data.user_currencysymbol;
      }
      mail_currency = fpc_calculation_data.user_currencyCode;
    }
    let db_country_cost;
    let cost_range = `${number_format(total_cost_min)} - ${number_format(total_cost_max)}`;
    let total_cost = 0;
    let effort_details = "";
    let grandtotalEfforts = "";
    category = "";
    dis_kind = "";
    mail_currency_sym = "";

    if (mail_currency_sym != "") {
      // mail_total_cost_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">' . ${mail_currency_sym} . ${number_format(
      //   total_cost
      // )} . '</h2>`;
      templatesData = {
        ...templatesData,
        mail_currency_sym,
        total_cost_mail_currency_sym: number_format(total_cost),
      };
    } else {
      templatesData = {
        ...templatesData,
        mail_currency_sym,
        cost_range,
        mail_currency,
      };
      // mail_total_cost_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">${cost_range} &nbsp;&nbsp;<B style="font-size: 19px; margin-left: 0px;color: #585858;font-weight: 500;">&nbsp;&nbsp;${mail_currency}</B></h2>`;

      // user_total_cost_display = `<h2 class="title" style="font-weight: 500;font-size: 50px;color: #2d9c00;margin: 0;">${cost_range}' . '<B style="font-size: 19px; margin-left: -18px;color: #585858;font-weight: 500;">&nbsp;${mail_currency}</B></h2>`;
    }
    db_country_cost = `${number_format(total_cost_min)} ${mail_currency} - ${number_format(total_cost_max)} ${mail_currency}`;

    // Returns a string with the first letter in upper case of each word.
    function ucwords(str) {
      return (str + "").replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
      });
    }

    let timeline_val;

    if (typeof grandtotalEfforts !== "undefined") {

      if (grandtotalEfforts < 800) {
        timeline_val = 1;
      } else if (grandtotalEfforts >= 800 && grandtotalEfforts <= 1500) {
        timeline_val = 2;
      } else if (grandtotalEfforts > 1500) {
        timeline_val = 3;
      }

    }
    /*
    ===============================================================================================
    COLLECT THE DATA FOR TEMPLATE
    =============================================================================================== 
    */
    templatesData = {
      ...templatesData,
      // TEST EFFORT CALCULATOR (Email template data)

      // USER DETAILS
      fpc_user_name,
      fpc_user_email,
      fpc_user_mobile,
      fpc_user_message,
      user_location_data,
      

      // SPECIFICATIONS
      dis_category: ucwords(dis_category),
      no_of_screens,
      no_ext_interface_text,
      kind_of_testing,
      dis_kind,
      dis_platfrom,
      complex_text,
      no_of_cycles,
      testcases: Math.round(testcases),

      // Test Preparation Effort
      tot_efforts_in_hr: tot_efforts_in_hr.toFixed(2),
      test_env_setup: test_env_setup.toFixed(2),
      test_data_preparation: test_data_preparation.toFixed(2),
      test_config_setup: test_config_setup.toFixed(2),
      tot_test_preparation_effort: (
        tot_efforts_in_hr +
        test_env_setup +
        test_data_preparation +
        test_config_setup
      ).toFixed(2),

      prepare_for_each_cycle: prepare_for_each_cycle.toFixed(2),

      // Test Execution Effort :- last row total calculation
      col_prepare: col_prepare.toFixed(2),
      col_tot_defect: col_tot_defect.toFixed(2),
      col_tot_exe: col_tot_exe.toFixed(2),
      col_tot_retest: col_tot_retest.toFixed(2),

      timeline_value,

      efforts_in_month: efforts_in_month.toFixed(1),
      total_effort_mail,
    };

    // Test Execution Effort :- cycles of i
    for (let i = 1; i <= no_of_cycles; i++) {
      templatesData = {
        ...templatesData,
        [`final_total_effort_text_excution_cyc${i}`]: eval(
          "final_total_effort_text_excution_cyc" + i
        ).toFixed(2),
        [`final_defect_reporting_cyc${i}`]: eval(
          "final_defect_reporting_cyc" + i
        ).toFixed(2),
        [`final_retesting_cyc${i}`]: eval("final_retesting_cyc" + i).toFixed(2),
      };
    }
    /*
    ===============================================================================================
    COLLECT TEMPLATES RESPONSE
    =============================================================================================== 
    */
    // Admin Mail Template
    const admin_mail_path = path.join(__dirname ,"../templates/admin_mail.ejs");
    const admin_mail = await ejs.renderFile(admin_mail_path,{...templatesData});

    // Customer Mail Template
    const customer_mail_path = path.join(__dirname ,"../templates/customer_mail.ejs");
    const customer_mail = await ejs.renderFile(customer_mail_path,{...templatesData});

    // Customer PDF Template
    const customer_pdf_path = path.join(__dirname ,"../templates/customer_pdf.ejs");
    const customer_pdf = await ejs.renderFile(customer_pdf_path,{...templatesData});

    // JSON Response Status Template
    const json_res_status_path = path.join(__dirname ,"../templates/json_res_status.ejs");

    // if (typeof effort_details !== "undefined") {
    //   // admin_mail += effort_details
    //   // customer_mail += effort_details
    // }else{
    //   // admin_mail
    //   // customer_mail
    // }
    /*
    ===============================================================================================
    SENDING EMAIL RESPONSE
    =============================================================================================== 
    */
    const mail_response = await handleSendGrideEmailSend(fpc_user_name,fpc_user_email,fpc_user_mobile, kind_of_testing, customer_mail, admin_mail)
    /*
    ===============================================================================================
    SAVE ENQUIRIES DATA / DATA INTO MYSQL
    =============================================================================================== 
    */
    const db_result = await testcost_enquiries(
      dis_category,fpc_user_name,fpc_user_email,fpc_user_mobile,fpc_user_message,user_location_data.user_location,
      user_location_data.user_city=='' ? undefined : user_location_data.user_city,
      user_location_data.user_region == '' ? undefined : user_location_data.user_region,
      user_location_data.user_CountryName,
      user_location_data.user_countryCode,
      no_of_screens,no_ext_interface_text,kind_of_testing,no_ext_interface,dis_platfrom,
      complex_text,no_of_cycles,testcases.toFixed(2),tot_efforts_in_hr,timeline_value,
      total_cost_min_db,total_cost_max_db,db_country_cost,total_effort_mail,
      mail_response.mail_sent,
      mail_response.unique_user_id
    );

    const { insertId } = db_result;

    // res.render("test.ejs", { ...templatesData });
    // res.render("admin-mail.ejs", { ...templatesData });
    // res.render("final_user_result.ejs", { ...templatesData });
    // res.render("json-res-status.ejs", { ...templatesData });
    // res.status(200).send(grand_cost);

    // SEND READY EMAIL TEMPLATE DATA IN RESPONSE
    // const dirname = path.join(__dirname ,"../templates/test.ejs");
    // const EmailResp = await ejs.renderFile(dirname,{...templatesData});
    // const html = ejs.render("admin_mail.ejs",{...templatesData})
    /*
    ===============================================================================================
    PDF CREATION
    =============================================================================================== 
    */
    var sms_res = "pdf api is not working";
    var db_result_pdf;
    if (typeof sms_check !== "undefined") {
      if (sms_check == 'yes') {
        let pdf_user_name = fpc_user_name;
        let pdf_user_mobile = fpc_user_mobile;
        let file_name = wpFeSanitizeTitle(pdf_user_name) + new Date().getTime();

        // Create PDF and write on server
        sms_res = await html_to_pdf(file_name, customer_pdf);

        let today = moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
        let expiry_date = moment(new Date()).add(10, "days").format('YYYY-MM-DD HH:mm:ss');
        let pdf_file_path = `https://api.testreveal.com:3013/api/public/pdfs/${file_name}.pdf`;

        if(sms_res == "PDF created successfully"){
          sms_res+=` checkout ::: ${pdf_file_path}`;
        }

        // Save PDF into MySQL
        db_result_pdf = await testcost_pdf(insertId, pdf_file_path, expiry_date);

        // if(country_name == "India"){
        //   // send text message if location is INDIA
        //   const smsResponse = await sendTextMsgApiCall(917414969691);
        //   console.log("smsResponse", smsResponse);
        // }

        // send whatsapp message
        const messResp = await twilioWhatsappApiCall(fpc_user_name, fpc_user_mobile, new URL(pdf_file_path));
        if(messResp){
          sms_res+=`\nsuccessfully send whatsapp message to +91${fpc_user_mobile}`
        }
        console.log(sms_res)
      }
      else {
        sms_res = "sms not selected";
      }
    }else {
      sms_res = "sms not there";
    }
    /*
    =============================================================================================== 
    */
    // res.render(json_res_status_path, { ...templatesData , mail_response:mail_response.json_result });
    const json_res_status = await ejs.renderFile(json_res_status_path,{...templatesData , mail_response:mail_response.json_result});

    // Final api response
    res.status(200).json({
      result: true,
      error: false,
      message: "success",
      data: json_res_status
    });
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
