const html_pdf_node = require('html-pdf-node');
const fs = require('fs');
const path = require('path')

exports.html_to_pdf = async(file_name, customer_mail) => {
    // Example of options with args
    let options = { format: 'A4', printBackground:true, preferCSSPageSize:true};
    let file = { content: customer_mail };

    return new Promise(resolve => {

        // To convert HTML page to PDF using generatePdf method:
        html_pdf_node.generatePdf(file, options, (err,pdfBuffer) => {

            if(pdfBuffer && !err){
                fs.writeFile((path.join(__dirname ,`../public/pdfs/${file_name}.pdf`)), pdfBuffer, (err) => {
                    if (err){
                      resolve("failed to write content in pdf")
                    }else{
                        resolve("pdf created successfully")
                    }
                })
            }else{
                resolve("failed to create pdf")
            }
            
        });
        
    }); 
}