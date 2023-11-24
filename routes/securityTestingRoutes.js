const router = require('express').Router();
const securityTestingControllers = require('../controllers/securityTestingControllers');

router.post('/submit-enquiry', securityTestingControllers.sampleController);

module.exports = router;