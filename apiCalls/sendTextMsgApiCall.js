const { default: axios } = require("axios");
const request = require("request");


exports.sendTextMsgApiCall = async (number) => {
    console.log("Inside sendTextMsgApiCall")
  var options = {
    url:'https://api.textlocal.in/send/?apikey=1+YaNM3RT8w-GIC14I6FnWMApAtjKNwQDJiUxl6kwp&numbers=917414969691&sender=RDBYTS&message='+encodeURIComponent('Dear Customer, our executive will contact you shortly for your Inquiry on Redbytes Business app. Visit https://bit.ly/3CAvBhY or https://bit.ly/CCappestmate'),
    // url:'https://api.textlocal.in/send/?apikey=1+YaNM3RT8w-GIC14I6FnWMApAtjKNwQDJiUxl6kwp&numbers=917414969691&sender=RDBYTS&message='+encodeURIComponent('Dear Customer, our executive would get on Quick call with you for your Inquiry on Redbytes App.Visit https://bit.ly/3kQhhft or https://bit.ly/rbslsqckcall'),
    method: "GET",
    headers: {
      'Content-Type': 'application/json'
    },
  };
  return new Promise((resolve, reject) => {
    request(options, function (error, response) {
      if (error) {
        reject(error);
      }else{
        // console.log("smsApicall resp",response.body)
        resolve(JSON.parse(response.body));
      }
    });
  });
};

  
