ALTER TABLE provider_availability
ADD UNIQUE KEY uniq_provider_date
(provider_id, change_date);
ALTER TABLE `provider_availability` CHANGE `day_of_week` `day_of_week` TINYINT NULL;

CREATE TABLE contact_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
