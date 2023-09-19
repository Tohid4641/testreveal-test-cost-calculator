const { getConnection, db } = require("../db/mysql/createPool");

exports.testcost_enquiries = async(...data) => {
    try {
        const connection = await getConnection();
        console.log("MySQL connected :::",connection.threadId);

        const result = await db.query(connection, `INSERT INTO wp_testcost_enquiries(app_category, user_name, user_email, user_mobile, user_comment, user_location, user_city, user_state, user_country, user_country_code, no_of_screens, no_ext_interface, kind_of_testing, tot_no_ext_interface, choosen_platform, complexity, no_of_cycles, no_of_testcases, test_preparation_effort, timeline, min_cost, max_cost, country_cost, grand_tot_effort, mail_sent, sendgrid_unique_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);` ,data)

        if(result){
            console.log("MySQL testcost enquiries inserted")
            connection.release();
            console.log("MySQL disconnected :::",connection.threadId)
            return result
        }else{
            return fasle
        }
  
    } catch (error) {
        console.log("MySQL not connected :::",error.message);
    }
}