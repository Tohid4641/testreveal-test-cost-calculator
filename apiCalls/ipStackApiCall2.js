const request = require("request");
require("dotenv").config();

exports.ipStackApiCall = async (ipAddress) => {

  var options = {
    method: "GET",
    url: `https://api.ipstack.com/${ipAddress}?access_key=${process.env.IPSTACK_ACCESS_KEY}`,
  };

  // console.log("process.env.IPSTACK_ACCESS_KEY",process.env.IPSTACK_ACCESS_KEY);


  return new Promise((resolve, reject) => {
    request(options, function (error, response) {
      if (error) {
        reject(error);
      }else{
        let res = JSON.parse(response.body)

        if(res.success == false){
          resolve({
              currency : {
                code : 'USD',
              },
              city_name : 'Not Available',
              flag_code : 'Not Available',
              flag_url : '',
              country_name : 'Not Available',
            });
        }else{
          resolve(res)
        }
      }
    });
  });
};