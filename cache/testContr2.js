
const html_to_pdf = require('html-pdf-node');
const fs = require('fs')

exports.testFunction2 = async (req,res) => {

let options = { format: 'A4'};
let file = { content: "<h1>Welcome to html-pdf-node</h1>" };

html_to_pdf.generatePdf(file, options,(err,pdfBuffer) => {
    if(!err){
        console.log("PDF created")
        fs.writeFileSync('some.pdf', pdfBuffer)
        res.send(pdfBuffer)
    }
});

};
