const request = require("request");

const getGeoDetails = async (remote_ip,callback) => {
    var options = {
      'method': 'GET',
      'url': `http://www.geoplugin.net/json.gp?ip=${remote_ip}`
    };
    request(options, function (error, response) {
      if (error) {
        callback(error.message,null)
      }
      res = JSON.parse(response.body);    // json result
      callback(null,res)
    });

  }

module.exports = getGeoDetails