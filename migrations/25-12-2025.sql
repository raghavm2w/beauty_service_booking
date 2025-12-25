CREATE TABLE provider_availability (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id BIGINT UNSIGNED NOT NULL,
    day_of_week TINYINT NOT NULL, -- 0 = Sun ... 6 = Sat
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
      -- Override fields
    is_recurring TINYINT(1) NOT NULL DEFAULT 1, -- 0- only for one day,1- recurring for every week
    change_date DATE NULL,         -- ONLY for overrides day
    status TINYINT DEFAULT 1, -- 1 = working, 0 = off
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (provider_id, day_of_week),
     CONSTRAINT fk_provider_availability_provider
        FOREIGN KEY (provider_id) REFERENCES users(id)
        ON DELETE CASCADE
);
ALTER TABLE provider_availability ADD CONSTRAINT uniq_provider_day_recurring UNIQUE (provider_id, day_of_week, is_recurring);
