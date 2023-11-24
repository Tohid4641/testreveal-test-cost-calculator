const { ipstackApiCall } = require("../apiCalls/ipstackApiCall");
const requestIp = require("request-ip");


exports.getClientLocation = async (req, res) => {
    let ipAddress = requestIp.getClientIp(req) || req.header["x-forwarded-for"] || req.socket.remoteAddress || "UNKNOWN";           
    
    try {
        const getLocation = await ipstackApiCall(ipAddress.substr(7))
        console.log("getLocation", getLocation)
        res.status(200).json(getLocation)
    } catch (error) {
        res.status(500).json(error.message)
    }
}