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


exports.getDomainList = async (req, res) => {
    try {
        const BASE_URL = process.env.BASE_URL;
        const domainList = [
            {
                d_name:"Automotive Industry",
                d_slug:"automotive-industry",
                d_url:`${BASE_URL}/public/icons/automotive-industry.png`
            },
            {
                d_name:"Banking and Finance",
                d_slug:"banking-and-finance",
                d_url:`${BASE_URL}/public/icons/banking-and-finance.png`
            },
            {
                d_name:"E-Commerce",
                d_slug:"e-commerce",
                d_url:`${BASE_URL}/public/icons/e-commerce.png`
            },
            {
                d_name:"E-Learning",
                d_slug:"e-learning",
                d_url:`${BASE_URL}/public/icons/e-learning.png`
            },
            {
                d_name:"Fashion",
                d_slug:"fashion",
                d_url:`${BASE_URL}/public/icons/fashion.png`
            },
            {
                d_name:"GPS",
                d_slug:"gps",
                d_url:`${BASE_URL}/public/icons/gps.png`
            },
            {
                d_name:"Healthcare",
                d_slug:"healthcare",
                d_url:`${BASE_URL}/public/icons/healthcare.png`
            },
            {
                d_name:"Insurance",
                d_slug:"insurance",
                d_url:`${BASE_URL}/public/icons/insurance.png`
            },
            {
                d_name:"IT and Consulting",
                d_slug:"it-and-consulting",
                d_url:`${BASE_URL}/public/icons/it-and-consulting.png`
            },
            {
                d_name:"Manufacturing",
                d_slug:"manufacturing",
                d_url:`${BASE_URL}/public/icons/manufacturing.png`
            },
            {
                d_name:"News and Publication",
                d_slug:"news-and-publication",
                d_url:`${BASE_URL}/public/icons/news-and-publication.png`
            },
            {
                d_name:"Others",
                d_slug:"others",
                d_url:`${BASE_URL}/public/icons/others.png`
            },
            {
                d_name:"Retail",
                d_slug:"retail",
                d_url:`${BASE_URL}/public/icons/retail.png`
            },
            {
                d_name:"Telecom",
                d_slug:"telecom",
                d_url:`${BASE_URL}/public/icons/telecom.png`
            },
            {
                d_name:"Travel and Logistics",
                d_slug:"travel-and-logistics",
                d_url:`${BASE_URL}/public/icons/travel-and-logistics.png`
            },
        ];
        res.status(200).json(domainList);
    } catch (error) {
        res.status(500).json(error.message)
    }
}