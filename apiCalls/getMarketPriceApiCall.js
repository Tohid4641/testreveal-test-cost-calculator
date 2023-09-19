const request = require("request");

exports.getMarketPriceApiCall = async () => {
  var options = {
    method: "POST",
    url: "https://softbreaksapi.azurewebsites.net/api/JobPosts/MarketPriceForJobs",
    headers: {
      "Content-Type": "application/json",

      Accept: "application/json",
    },
    body: JSON.stringify({ SkillID: 28 }),
  };

  return new Promise((resolve, reject) => {
    request(options, function (error, response) {
      if (error) {
        reject(error);
      } else {
        // console.log('res',response.body)
        // resolve(JSON.parse(response.body));
        resolve(response.body);
      }
    });
  });
};

