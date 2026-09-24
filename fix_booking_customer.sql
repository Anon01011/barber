-- Fix Booking 7 to use the correct customer (Ketan Mhatre - ID 1)
-- This will allow the package balance to be detected when opening POS from this booking

UPDATE bookings 
SET customer_id = 1 
WHERE id = 7;

-- Verify the change
SELECT id, customer_id, service_id, package_id, created_at 
FROM bookings 
WHERE id = 7;
