ALTER TABLE `bookings` ADD `service_id` BIGINT UNSIGNED NOT NULL AFTER `updated_at`;
ALTER TABLE `bookings` ADD CONSTRAINT `fk_service_booking` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
