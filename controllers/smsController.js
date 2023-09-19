const MessagingResponse = require("twilio").twiml.MessagingResponse;

exports.sendMessage = (req, res) => {
    const twiml = new MessagingResponse();
    twiml.message("Thanks for signing up!");
    res.end(twiml.toString());
}