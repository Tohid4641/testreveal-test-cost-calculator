const router = require("express").Router();
const testCostCalcRoutes = require("./testCostCalcRoutes");
const hireTesterRoutes = require("./hireTesterRoutes");
const securityTestingRoutes = require("./securityTestingRoutes");
const controller = require("../controllers/controller");

router.get('/', (req, res) => res.render('Home', { title: 'Testreveal Backend' }));
router.use("/test-cost-calculator", testCostCalcRoutes);
router.use("/hire-tester", hireTesterRoutes);
router.use("/security-testing-services", securityTestingRoutes);

// Global API's
router.get("/get-client-location", controller.getClientLocation);


module.exports = router;
