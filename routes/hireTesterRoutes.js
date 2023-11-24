const router = require('express').Router();
const hireTesterControllers = require('../controllers/hireTesterControllers');

router.post('/submit-enquiry', hireTesterControllers.submitEnquiry);

module.exports = router;