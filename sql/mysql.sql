CREATE TABLE `{extra}` (
  `id`                INT(10) UNSIGNED          NOT NULL AUTO_INCREMENT,
  `uid`               INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `title`             VARCHAR(255)              NOT NULL DEFAULT '',
  `slug`              VARCHAR(255)              NOT NULL DEFAULT '',
  `status`            TINYINT(1) UNSIGNED       NOT NULL DEFAULT '0',
  `time_start`        INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `time_end`          INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `source_url`        VARCHAR(255)              NOT NULL DEFAULT '',
  `organizer_name`    VARCHAR(255)              NOT NULL DEFAULT '',
  `address`           VARCHAR(255)              NOT NULL DEFAULT '',
  `offer_url`         VARCHAR(255)              NOT NULL DEFAULT '',
  # register
  `register_details`  TEXT,
  `register_can`      TINYINT(1) UNSIGNED       NOT NULL DEFAULT '0',
  `register_stock`    INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `register_sales`    INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `register_price`    DECIMAL(16, 2)            NOT NULL DEFAULT '0.00',
  `register_type`     ENUM ('discount', 'full') NOT NULL DEFAULT 'discount',
  `register_discount` TEXT,
  # guide module fields
  `guide_owner`       INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `guide_category`    VARCHAR(255)              NOT NULL DEFAULT '',
  `guide_location`    VARCHAR(255)              NOT NULL DEFAULT '',
  `guide_item`        VARCHAR(255)              NOT NULL DEFAULT '',
  # google map fields
  `map_latitude`      VARCHAR(16)               NOT NULL DEFAULT '',
  `map_longitude`     VARCHAR(16)               NOT NULL DEFAULT '',
  `map_zoom`          INT(5) UNSIGNED           NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `guide_owner` (`guide_owner`),
  KEY `title` (`title`),
  KEY `time_start` (`time_start`),
  KEY `time_end` (`time_end`),
  KEY `status` (`status`)
);

CREATE TABLE `{order}` (
  `id`           INT(10) UNSIGNED          NOT NULL AUTO_INCREMENT,
  `uid`          INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `event`        INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `order_id`     INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `number`       INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `price`        DECIMAL(16, 2)            NOT NULL DEFAULT '0.00',
  `vat`          DECIMAL(16, 2)            NOT NULL DEFAULT '0.00',
  `total`        DECIMAL(16, 2)            NOT NULL DEFAULT '0.00',
  `time_order`   INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `time_start`   INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `time_end`     INT(10) UNSIGNED          NOT NULL DEFAULT '0',
  `status`       TINYINT(1) UNSIGNED       NOT NULL DEFAULT '0',
  `type`         ENUM ('discount', 'full') NOT NULL DEFAULT 'discount',
  `code_public`  VARCHAR(32)               NOT NULL DEFAULT '',
  `code_private` VARCHAR(32)               NOT NULL DEFAULT '',
  `extra`        TEXT,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `event` (`event`),
  KEY `time_order` (`time_order`),
  KEY `time_start` (`time_start`),
  KEY `time_end` (`time_end`),
  KEY `status` (`status`)
);

/* CREATE TABLE `{organizer}` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `title`   VARCHAR(255)     NOT NULL DEFAULT '',
  `website` VARCHAR(255)     NOT NULL DEFAULT '',
  `phone`   VARCHAR(255)     NOT NULL DEFAULT '',
  `address` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
); */