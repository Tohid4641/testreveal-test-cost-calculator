// Constants
const express = require("express");
const app = express()
const bodyParser = require("body-parser");
const router = require("./routes/api-routes");
const dotenv = require("dotenv");
const morgan = require("morgan");
const cors = require('cors')

//dotenv file access require
dotenv.config({ path: './config.env' });

const LOCAL_PORT = process.env.LOCAL_PORT || 8850;
const PORT = process.env.PORT || 8750;

// EJS
app.set('view engine','ejs');
app.set('views','./templates');

// Middlewares
bodyParser.urlencoded({ extended: false });
app.use(bodyParser.json());
app.use(morgan("short"));
app.use(cors());

// Static Path For PDF's
app.use('/api/public',express.static('public'));

// Routes
app.use("/api", router);

// Error Handling
app.use((err, req, res, next) => {
  err.statusCode = err.statusCode || 500;
  err.message = err.message || "Internal Server Error";
  res.status(err.statusCode).json({
    message:err.message
  });
});

// Node Local Server
app.listen(LOCAL_PORT, () =>
  console.log(
    `---------------------------------------------------------------------
Server Listening :::: http://localhost:${LOCAL_PORT}/api
----------------------------------------------------------------------`
  )
);

// For Testreveal Server
// const https = require('https');
// const fs = require('fs');
// const options = {
//   key: fs.readFileSync('/etc/letsencrypt/live/api.testreveal.com/privkey.pem'),
//   cert: fs.readFileSync('/etc/letsencrypt/live/api.testreveal.com/fullchain.pem'),
// };
// const testreveal_server = https.createServer(options, app);
// testreveal_server.listen( PORT, () =>
//   console.log(
//     `---------------------------------------------------------------------
// Server Listening :::: https://api.testreveal.com:${PORT}
// -------------------------------------------------------------------`
//   )
// );

