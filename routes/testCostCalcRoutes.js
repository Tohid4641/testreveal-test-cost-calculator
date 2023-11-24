const testCostCalController = require('../controllers/testCostCalcControllers');
const controller = require('../controllers/controller');
const { sendMessage } = require('../controllers/smsController');
const router = require('express').Router();

router.post('/',testCostCalController.testCostCalculator);
router.get('/getClientLocation', controller.getClientLocation);
router.get('/getDomainList', testCostCalController.getDomainList);
router.post('/sms', sendMessage);

module.exports = router;