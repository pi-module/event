CREATE TABLE `{extra}` (
  `id`                   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `time_start`           INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `time_end`             INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `source_url`           VARCHAR(255)     NOT NULL DEFAULT '',
  `organizer_name`       VARCHAR(255)     NOT NULL DEFAULT '',
  `address`              VARCHAR(255)     NOT NULL DEFAULT '',
  `price`                DECIMAL(16, 2)   NOT NULL DEFAULT '0.00',
  `offer_url`            VARCHAR(255)     NOT NULL DEFAULT '',
  `registration_details` TEXT,
  # guide module fields
  `guide_owner`          INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `guide_category`       VARCHAR(255)     NOT NULL DEFAULT '',
  `guide_location`       VARCHAR(255)     NOT NULL DEFAULT '',
  `guide_item`           VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
);