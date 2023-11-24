const { ipStackApiCall } = require("../apiCalls/ipStackApiCall2");
const { officeCallerLead } = require("../apiCalls/officeCallerLead");

exports.submitEnquiry = async (req, res) => {
    try {
        const { 
            testersToHire,
            platform,
            status,
            screens,moreScreens,
            typesArr,
            userMessage,
            maxBgt,
            minBgt,
            expectation,moreExpectation,
            know,otherKnow,
            name,
            email,
            dialCode,
            phone
        } = req.body;
    /*
    ====================================================================================================
    INPUTS VALIDATION
    ====================================================================================================
    */
        const isArrayAndNotEmpty = arr => Array.isArray(arr) && arr.length > 0;
        const isNonEmptyString = str => typeof str === "string" && str.trim() !== "";
        const isValidNumber = num => typeof num === "number" && num !== '' && num !== 0;
        if(
            !(isNonEmptyString(testersToHire) &&
            isNonEmptyString(platform) &&
            isNonEmptyString(status) &&
            isNonEmptyString(screens) &&
            isArrayAndNotEmpty(typesArr) && 
            isNonEmptyString(userMessage) &&
            isValidNumber(maxBgt) &&
            isValidNumber(minBgt) &&
            isNonEmptyString(expectation) &&
            isNonEmptyString(know) &&
            isNonEmptyString(name) &&
            isNonEmptyString(email) &&
            isNonEmptyString(dialCode) &&
            isValidNumber(phone))
        ){
            throw new Error("Please check your inputs!");
        }
    /*
    ====================================================================================================
    GET USER LOCATION
    ====================================================================================================
    */
        let pure_ip = req.header("x-forwarded-for") || req.socket.remoteAddress || req.ip || req.connection.remoteAddress;

        // const ipAddress = "::ffff:106.193.146.135";
        // const ipAddress = "::ffff:106.193.146.135";

        const splitIP = pure_ip.split(':');

        const ipComponents = {
            prefix: splitIP[0],
            address: splitIP[splitIP.length - 1]
        };

        // console.log(ipComponents);

        let geoData = await ipStackApiCall(
            pure_ip == "::1" ? "47.29.129.0" : ipComponents.address
        );
    /*
    ====================================================================================================
    CRM ENTRY
    ====================================================================================================
    */

    const userLocation = `${geoData.city} ${geoData.region_name} ${geoData.country_name}`;
    const noOfScreens = `${screens == 'More' ? moreScreens : screens}`
    const whereKnow = `${know == 'Others' ? otherKnow : know}`
    const whenExpectation = `${expectation == 'More' ? moreExpectation : expectation}`
    const leadInfoArr = [
        `User Name : ${name}`,
        `User Email : ${email}`,
        `User Comments : ${userMessage}`,
        `User Location : ${userLocation || 'Not Available'}`,
        `User Mobile : ${phone}`,
        `How many testers you wish to hire ? : ${testersToHire}`,
        `Which platform is software/app/webapp based on ? : ${platform}`,
        `How many screens do you wish to test ? : ${noOfScreens || 'Not Available'}`,
        `What's the status of the product ? : ${status}`,
        `From where did you get to know about us ? : ${whereKnow || 'Not Available'}`,
        `Type of testing : ${typesArr.join(',')}`,
        `When do you expect to complete the project ? : ${whenExpectation || 'Not Available'}`,
        `Min. Budget : ${minBgt}`,
        `Max. Budget : ${maxBgt}`
    ]

    const saveLead = await officeCallerLead(
        name,
        email,
        phone,
        geoData.city,
        geoData.country_name,
        `Hire Tester Enquiry - Testreveal Website`,
        `Not Available`,
        leadInfoArr
    );
    
    console.log(JSON.parse(saveLead));

    if(JSON.parse(saveLead).error !== false){
        return res.status(400).json({
            result: false,
            error: true,
            message: "Somthing went wrong!",
        }); 
    }
    
    return res.status(200).json({
        result: true,
        error: false,
        message: "successfully sent your request!",
    });
    
    } catch (error) {
        return res.status(error.statusCode || 500).json({
            result: false,
            error: true,
            message: error.message,
        })
    }
}

exports.sampleController = async (req, res) => {
    try {
        res.status(200).json({
            result: true,
            error: false,
            message: "success",
            data: ["data"]
        });
    } catch (error) {
        res.status(error.statusCode || 500).json({
            result: false,
            error: true,
            message: error.message,
            data: [],
        })
    }
}