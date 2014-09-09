SELECT fvc.id, ro.id AS rid, rol.leadKms, cu.tradingName, fvc.fixedContribution, fvc.fixedCost, fvc.numberOfDays, drv.value, bu.name, fvc.startDate, fvc.endDate, td.description, lf.name AS lfn, lt.name AS ltn
FROM udo_customer AS cu
LEFT JOIN udo_fandvcontract AS fvc ON (fvc.customer_id = cu.id)
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId = fvc.variableCostRate_id AND drv.type = "Rate")
LEFT JOIN udo_businessunit AS bu ON (bu.id = fvc.businessUnit_id)
LEFT JOIN udo_rates AS ra ON (ra.id = fvc.variableCostRate_id)
LEFT JOIN udo_truckdescription AS td ON (td.id = ra.truckDescription_id)
LEFT JOIN udo_fandvcontractroute_link AS rol ON (rol.fandVContract_id = fvc.id)
LEFT JOIN udo_route AS ro ON (ro.id = rol.route_id)
LEFT JOIN udo_location AS lf ON (lf.id = ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id = ro.locationTo_id)
WHERE cu.active = 1 AND cu.primaryCustomer = 1 AND cu.useFandVContract = 1 AND fvc.startDate >= "2014-06-30 22:00:00" AND fvc.endDate <= "2014-08-10 21:59:59";
