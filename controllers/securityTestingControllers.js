const { ipStackApiCall } = require("../apiCalls/ipStackApiCall2");
const { officeCallerLead } = require("../apiCalls/officeCallerLead");

exports.sampleController = async (req, res) => {
    try {
        const {
            platform,webApps,mobileApps,appPlatform,screens,serverCalls
        } = req.body;
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