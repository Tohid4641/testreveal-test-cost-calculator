const ejs = require('ejs');
const path = require('path');
exports.testFunction = async(req, res) => {
    const dirname = path.join(__dirname ,"../views/test.ejs");

    try {
        
        res.render("admin-mail.ejs")
        // res.status(200).json({
        //     message:"success",
        //     data:EmailResp
        // });
    } catch (error) {
        res.status(500).json(error.message)
    }
}