const getMarketPriceApiCall = require("../apiCalls/getMarketPriceApiCall");


exports.getMarketPriceFromSkillID = () => {
    
  return new Promise((resolve, reject) => {
    getMarketPriceApiCall(function (err, response) {
      if (err) {
        reject(err);
        // reject(new Error("Somthing went wrong!"));
        // console.log("getMarketPriceApiCall Error :",err)  // check if somthing went wrong error occured
      }else {
        resolve(response);
      }
    });

  });
}


