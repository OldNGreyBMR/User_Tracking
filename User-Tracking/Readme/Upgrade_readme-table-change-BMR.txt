#2024-03-09 
ALTER TABLE `zen_user_tracking` CHANGE `ip_address` `ip_address` VARCHAR(96);
ALTER TABLE `zen_user_tracking` CHANGE `customers_host_address` `customers_host_address` varchar(96) NOT NULL;
