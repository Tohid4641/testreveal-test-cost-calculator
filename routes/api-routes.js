const {testCostCalculator} = require('../controllers/api-controllers');
const { getClientLocation, getDomainList } = require('../controllers/api-inter-controllers');
const { sendMessage } = require('../controllers/smsController');
const router = require('express').Router();

/* GET home page. */
router.get('/test-cost-calculator', function(req, res, next) {
    res.render('Home', { title: 'Testreveal Backend' });
});

router.post('/test-cost-calculator',testCostCalculator);

router.get('/test-cost-calculator/getClientLocation', getClientLocation);

router.get('/test-cost-calculator/getDomainList', getDomainList);

router.post('/test-cost-calculator/sms', sendMessage);

module.exports = router;