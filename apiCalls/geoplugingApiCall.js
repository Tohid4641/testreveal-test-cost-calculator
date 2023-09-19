const request = require("request");

const getGeoDetails = async (remote_ip) => {
  var options = {
    method: "GET",
    url: `http://www.geoplugin.net/json.gp?ip=${remote_ip}`,
  };

  return new Promise((resolve, reject) => {
    request(options, function (error, response) {
      if (error) {
        reject(error);
      }else{
        // console.log("getGeoDetails",response.body)
        resolve(JSON.parse(response.body));
      }
    });
  });
};

module.exports = getGeoDetails;
