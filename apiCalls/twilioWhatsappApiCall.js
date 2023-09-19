const dotenv = require("dotenv");
dotenv.config({ path: "../config.env" });

const { TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_FROM } = process.env;
const client = require("twilio")(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);


exports.twilioWhatsappApiCall = async (name, number, PDF_URL) => {

  try {
    const response = await client.messages.create({
      from: `whatsapp:${TWILIO_FROM}`,
      to: `whatsapp:+91${number}`,
      body: `Hi ${name}, find high-level Effort and Test Cost estimation based on your preferences here ${PDF_URL} Testreveal.`,
      mediaUrl:[PDF_URL]
    });

    return response;
  } catch (error) {
    return error;
  }
};
