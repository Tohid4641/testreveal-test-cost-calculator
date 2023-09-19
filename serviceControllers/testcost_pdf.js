const { getConnection, db } = require("../db/mysql/createPool");

exports.testcost_pdf = async(enquiry_id, pdf_file_path, expiry_date) => {
    try {
        const connection = await getConnection();
        console.log("MySQL connected :::",connection.threadId);

        const db_result_pdf = await db.query(connection, `SELECT * FROM wp_testcost_pdf WHERE enquiry_id = ${enquiry_id}`)
        // const result = await db.query(connection, `SELECT * FROM wp_testcost_pdf`)

        if(db_result_pdf == [] || db_result_pdf.length == 0){
            // Rows is empty : Inserted
            const inserted = await db.query(connection, `INSERT INTO wp_testcost_pdf(enquiry_id, pdf_file_path, expire_date) VALUES (?,?,?);` ,[enquiry_id,pdf_file_path,expiry_date])

            if(inserted){
                console.log("MySQL testcost pdf inserted")
                connection.release();
                console.log("MySQL disconnected :::",connection.threadId)
                return inserted
            }else{
                return fasle
            }
        }else{
            // Rows is not empty : Updated
            const updated = await db.query(connection, `UPDATE wp_testcost_pdf SET enquiry_id = ${enquiry_id}, pdf_file_path = ${pdf_file_path}, expire_date = ${expiry_date}  WHERE enquiry_id = ${enquiry_id}`)

            if(updated){
                console.log("MySQL testcost pdf updated")
                connection.release();
                console.log("MySQL disconnected :::",connection.threadId)
                return updated
            }else{
                return fasle
            }
        }    
  
    } catch (error) {
        console.log("MySQL Error :::",error.message);
    }
}