SELECT fvc.id, tr.id as tid, cu.tradingName, fvc.fixedContribution, fvc.fixedCost, fvc.numberOfDays, drv.value, bu.name, fvc.startDate, fvc.endDate, td.description, tr.fleetnum
FROM udo_customer AS cu
LEFT JOIN udo_fandvcontract AS fvc ON (fvc.customer_id = cu.id)
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId = fvc.variableCostRate_id AND drv.type = "Rate")
LEFT JOIN udo_businessunit AS bu ON (bu.id = fvc.businessUnit_id)
LEFT JOIN udo_rates AS ra ON (ra.id = fvc.variableCostRate_id)
LEFT JOIN udo_truckdescription AS td ON (td.id = ra.truckDescription_id)
LEFT JOIN udo_fandvcontracttruck_link AS trl ON (trl.fandVContract_id = fvc.id)
LEFT JOIN udo_truck AS tr ON (tr.id = trl.truck_id)
WHERE cu.active = 1 AND cu.primaryCustomer = 1 AND cu.useFandVContract = 1 AND fvc.startDate >= "2014-03-31 22:00:00" AND fvc.endDate <= "2014-05-11 21:59:59" AND cu.id=17692;
