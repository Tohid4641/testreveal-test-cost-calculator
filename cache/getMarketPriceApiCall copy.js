const request = require("request");

const getMarketPriceApiCall = async (callback) => {
  var options = {
    method: "POST",
    url: "https://softbreaksapi.azurewebsites.net/api/JobPosts/MarketPriceForJobs",
    headers: {
      "Content-Type": "application/json",

      Accept: "application/json",
    },
    body: JSON.stringify({SkillID: 28,}),
  };
  request(options, function (error, response) {
    if (error && !response) {
      callback(error.message, null);
    }
    if (response) {
      res = JSON.parse(response.body); // json result
      callback(null, res);
    }
  });
};

module.exports = getMarketPriceApiCall;
