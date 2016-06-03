SELECT drv.id as drv_id, drv.beginDate, drv.endDate, drv.objectInstanceId as rate_id, drv.type, lf.name as locationFrom, lt.name as locationTo
FROM udo_rates AS ra
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId=ra.id AND drv.type="Rate")
LEFT JOIN udo_route AS ro ON (ro.id=ra.route_id)
LEFT JOIN udo_location AS lf ON (lf.id=ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id=ro.locationTo_id)
WHERE ra.objectInstanceId=28803;
