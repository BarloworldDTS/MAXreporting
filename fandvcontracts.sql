SELECT fvc.id, cu.tradingName, fvc.fixedContribution, fvc.fixedCost, fvc.numberOfDays, drv1.value AS rate, bu.name AS buname, fvc.startDate, fvc.endDate, td.description, drv2.value AS dpm, drv3.value AS dpt, drv4.value AS fc, drv5.value AS fval, drv6.value AS ed, drv7.value AS eek, rat.name as rat
FROM udo_customer AS cu
LEFT JOIN udo_fandvcontract AS fvc ON (fvc.customer_id = cu.id)
LEFT JOIN daterangevalue AS drv1 ON (drv1.objectInstanceId = fvc.variableCostRate_id AND drv1.type = "Rate")
LEFT JOIN daterangevalue AS drv2 ON (drv2.objectInstanceId = fvc.variableCostRate_id AND drv2.type = "DaysPerMonth")
LEFT JOIN daterangevalue AS drv3 ON (drv3.objectInstanceId = fvc.variableCostRate_id AND drv3.type = "DaysPerTrip")
LEFT JOIN daterangevalue AS drv4 ON (drv4.objectInstanceId = fvc.variableCostRate_id AND drv4.type = "FuelConsumptionForRoute")
LEFT JOIN daterangevalue AS drv5 ON (drv5.objectInstanceId = fvc.variableCostRate_id AND drv5.type = "Fleet")
LEFT JOIN daterangevalue AS drv6 ON (drv6.objectInstanceId = fvc.variableCostRate_id AND drv6.type = "ExpectedDistance")
LEFT JOIN daterangevalue AS drv7 ON (drv7.objectInstanceId = fvc.variableCostRate_id AND drv7.type = "ExpectedEmptyKms")
LEFT JOIN udo_businessunit AS bu ON (bu.id = fvc.businessUnit_id)
LEFT JOIN udo_rates AS ra ON (ra.id = fvc.variableCostRate_id)
LEFT JOIN udo_ratetype AS rat ON (rat.id = ra.rateType_id)
LEFT JOIN udo_truckdescription AS td ON (td.id = ra.truckDescription_id)
WHERE cu.active = 1 AND cu.primaryCustomer = 1 AND cu.useFandVContract = 1 AND fvc.startDate >= "2014-06-30 22:00:00" AND fvc.endDate <= "2014-08-10 21:59:59" ORDER BY cu.tradingName ASC;
