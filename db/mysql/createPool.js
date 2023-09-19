const mysql = require("mysql");
const { makeDb } = require("mysql-async-simple");
const dotenv = require("dotenv");
dotenv.config({ path: "./config.env" });

// const { DB_HOST, DB_USER, TESTBYTES_DB, TESTBYTES_DB_PASS } = process.env;

// Create connection

//Localhost_db
// const pool = mysql.createPool({
//   connectionLimit: 10,
//   host: "localhost",
//   user: "root",
//   password: '',
//   database: "testreveal_db",
// });

//testbyte_db
const pool = mysql.createPool({
  connectionLimit: 10,
  host: "3.92.124.239",
  user: "testbytes_dba",
  password: "tb!@#DB123$%^",
  database: "testbytes_db",
});

const getConnection = () => {
  return new Promise((resolve, reject) => {
    pool.getConnection(function (err, connection) {
      if (err) {
        return reject(err);
      }
      resolve(connection);
    });
  });
};

const db = makeDb();

module.exports = {
  db,
  pool,
  getConnection,
};
