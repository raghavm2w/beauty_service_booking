ALTER TABLE provider_availability
ADD UNIQUE KEY uniq_provider_date
(provider_id, change_date);
ALTER TABLE `provider_availability` CHANGE `day_of_week` `day_of_week` TINYINT NULL;
