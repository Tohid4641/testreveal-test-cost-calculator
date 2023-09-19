const request = require("request");

exports.ipstackApiCall = async (ipAddress) => {
  console.log("ipAddress" ,ipAddress);
  var options = {
    method: "GET",
    url: `https://api.ipstack.com/${ipAddress}?access_key=${process.env.IPSTACK_ACCESS_KEY}`,
  };


  return new Promise((resolve, reject) => {
    request(options, function (error, response) {
      if (error) {
        reject(error);
      }else{
        console.log("ipstack api response", response.body)
        resolve(JSON.parse(response.body));
      }
    });
  });
};
