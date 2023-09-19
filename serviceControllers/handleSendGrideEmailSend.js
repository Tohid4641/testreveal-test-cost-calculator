const wpFeSanitizeTitle = require("./sanitize_title()");
const sgMail = require('@sendgrid/mail')

exports.handleSendGrideEmailSend = async (fpc_user_name,fpc_user_email,fpc_user_mobile, kind_of_testing, customer_mail, admin_mail) => {

    let unique_user_id = wpFeSanitizeTitle(fpc_user_name) + "_" + new Date().getTime();
    let client_mail = fpc_user_email;
    // let client_mail = "tauhid.s@cloudxperte.com";
    let unique_mobile = fpc_user_mobile;
    let unique_user_name = fpc_user_name;
    let to = 'info@testbytes.net';
    let admin_ID = "info@testbytes.net";
    let subject = '';
    let mail_sent = 0;
    let customer_sent;
    let admin_sent;
    let json_result;

    if (typeof kind_of_testing !== "undefined") {
        subject = `Testreveal - Mobile App Testing Calculation for ${client_mail}`;
    }
    else {
        subject = `Testreveal - Test Effort Calculation for ${client_mail}`;
    }

    
    sgMail.setApiKey(process.env.SENDGRID_APY_KEY);

    const messageForCustomer = {
        to: client_mail, // Change to your recipient
        from: admin_ID, // Change to your verified sender
        subject: subject,
        text: 'testing',
        html: customer_mail,
    }
    
    const messageForAdmin = {
        // to: "sktohid111@gmail.com", // Change to your recipient
        to: admin_ID, // Change to your recipient
        from: admin_ID, // Change to your verified sender
        subject: subject,
        text: 'testing',
        html: admin_mail,
    }

    try {
        customer_sent = await sgMail.send(messageForCustomer);
        // console.log("customer_sent Response",customer_sent[0].statusCode);
        // console.log("customer_sent Response",customer_sent[0].headers);
        
        admin_sent = await sgMail.send(messageForAdmin);
        // console.log("admin_sent Response",admin_sent[0].statusCode);
        // console.log("admin_sent Response",admin_sent[0].headers);
        
        if ((customer_sent[0].statusCode == 200 || customer_sent[0].statusCode == 202 || customer_sent[0].statusCode == "200" || customer_sent[0].statusCode == "202") && (admin_sent[0].statusCode == 200 || admin_sent[0].statusCode == 202 || admin_sent[0].statusCode == "200" || admin_sent[0].statusCode == "202")) {
            return {
                unique_user_id,
                mail_sent : 1,
                mailSubject:subject,
                json_result : 'Please Check Your Mail.',
            }
        }
        
    } catch (error) {
        return {
            unique_user_id,
                mail_sent : 0,
                message:error,
                json_result : 'Something Went wrong.',
            }
    }

}




