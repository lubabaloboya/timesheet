ALTER TABLE `special_documents` ADD `specialDocumentDateExpiry` DATE NULL DEFAULT NULL AFTER `specialDocumentStatus`;

UPDATE countries SET countryID=countryID+1000;
UPDATE countries SET countryID=countryID-999;
INSERT INTO `countries` (`countryID`, `countryName`) VALUES ('1', 'South Africa');
DELETE FROM `countries` WHERE `countries`.`countryID` = 224;


ALTER TABLE `visa_documentation` CHANGE `visaDocumentationStatus` `visaDocumentationStatus` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->uploaded, 2->accepted';

ALTER TABLE `visa_documentation` CHANGE `visaDocumentationDateUploaded` `visaDocumentationDateAdded` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `visa_documentation` ADD `visaDocumentationDateUploaded` DATETIME NULL DEFAULT NULL AFTER `visaDocumentationDateAdded`;
ALTER TABLE `visa_documentation` ADD `visaDocumentationExtension` VARCHAR(10) NULL DEFAULT NULL AFTER `visaDocumentationVisaID`;

CREATE TABLE `immigration`.`company_documents` ( `companyDocumentationID` INT NOT NULL AUTO_INCREMENT , `companyDocumentationCompanyID` INT NOT NULL , `companyDocumentationExtension` VARCHAR(10) NOT NULL , `companyDocumentationDateUploaded` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`companyDocumentationID`), INDEX (`companyDocumentationCompanyID`)) ENGINE = InnoDB;

ALTER TABLE `company_documentation` ADD FOREIGN KEY (`companyDocumentationCompanyID`) REFERENCES `immigration`.`companies`(`companyID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `company_documentation` ADD `companyDocumentationName` VARCHAR(255) NOT NULL AFTER `companyDocumentationCompanyID`;

ALTER TABLE `special_documents` CHANGE `specialDocumentStatus` `specialDocumentStatus` INT(11) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->Added, 2->Uploaded, 3->Accepted';
ALTER TABLE `special_documents` CHANGE `specialDocumentStatus` `specialDocumentStatus` INT(11) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->Uploaded';
ALTER TABLE `special_documents` DROP `specialDocumentDateAgreed`;
ALTER TABLE `special_documents` ADD FOREIGN KEY (`specialDocumentVisaID`) REFERENCES `immigration`.`visas`(`visaID`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE financial_documents ( financialDocumentID int(11) NOT NULL, financialDocumentVisaID int(11) NOT NULL, financialDocumentName varchar(255) NOT NULL, financialDocumentStatus int(11) NOT NULL DEFAULT '0' )
ALTER TABLE `financial_documents` CHANGE `financialDocumentStatus` `financialDocumentStatus` INT(11) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->Added, 2->Uploaded, 3->Accepted'
ALTER TABLE `financial_documents` CHANGE `financialDocumentStatus` `financialDocumentStatus` INT(11) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->Uploaded'
ALTER TABLE `financial_documents` ADD FOREIGN KEY (`financialDocumentVisaID`) REFERENCES `immigration`.`visas`(`visaID`) ON DELETE CASCADE ON UPDATE CASCADE
ALTER TABLE `financial_documents` ADD PRIMARY KEY(`financialDocumentID`);
ALTER TABLE `financial_documents` CHANGE `financialDocumentID` `financialDocumentID` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `financial_documents` ADD `financialDocumentDateExpiry` DATE NULL DEFAULT NULL AFTER `financialDocumentStatus`, ADD `financialDocumentDateAdded` TIMESTAMP NOT NULL AFTER `financialDocumentDateExpiry`, ADD `financialDocumentDateUploaded` DATETIME NULL DEFAULT NULL AFTER `financialDocumentDateAdded`;
ALTER TABLE `financial_documents` CHANGE `financialDocumentDateAdded` `financialDocumentDateAdded` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE financial_documents DROP COLUMN financialDocumentDateExpiry;

-- V1.2.2 16-02-17
ALTER TABLE `visas` ADD `visaCreatedBy` INT NOT NULL AFTER `visaDateDeclined`, ADD INDEX (`visaCreatedBy`);
ALTER TABLE `visas` ADD FOREIGN KEY (`visaCreatedBy`) REFERENCES `users`(`userID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- V1.2.0 03-05-2017
ALTER TABLE `financial_documents` ADD `financialDocumentExtension` VARCHAR(10) NOT NULL AFTER `financialDocumentVisaID`;
ALTER TABLE `financial_documents` CHANGE `financialDocumentExtension` `financialDocumentExtension` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `special_documents` ADD `specialDocumentExtension` VARCHAR(10) NULL DEFAULT NULL AFTER `specialDocumentName`;
ALTER TABLE `visas` ADD `visaExtension` VARCHAR(10) NULL DEFAULT NULL AFTER `visaStatus`;

# Feature IMM-25 16-05-2017
ALTER TABLE `visa_documentation` CHANGE `visaDocumentationStatus` `visaDocumentationStatus` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->Uploaded';

# Feature IMM-27 16-05-2017
ALTER TABLE `visas` ADD `visaDateAppointment` DATETIME NULL DEFAULT NULL AFTER `visaDateDeclined`;

# Feature IMM-27 19-05-2017
ALTER TABLE `visas` CHANGE `visaDateAppointment` `visaDateAppointment` DATE NULL DEFAULT NULL;

# Feature IMM-47 07-06-2017
ALTER TABLE `visa_documentation` ADD `visaDocumentationNotRequired` INT NULL DEFAULT '0' COMMENT '0-> Required, 1->Not Required' AFTER `visaDocumentationDateExpiry`;

# HotFix 10-07-2017
ALTER TABLE `expatriates` ADD `expatriatePassportExpiryDate` DATETIME NULL AFTER `expatriateJobDescription`;
ALTER TABLE `expatriates` CHANGE `expatriatePassportExpiryDate` `expatriatePassportExpiryDate` DATE NULL DEFAULT NULL;

# HotFix 18-01-2018
ALTER TABLE `visas` CHANGE `visaStatus` `visaStatus` INT(2) NOT NULL DEFAULT '1' COMMENT '1->Created, 2->Phase 1 complete, 3->Phase 2 complete, 4->Ready for submission, 5->Submitted, 6->Complete, 7->Declined, 8->Onhold, 9->Cancelled, 10->Awaiting Documents,';
UPDATE `drop_down_lists` SET `dropDownListValues` = '[[1, \"Urgent documents/information\", 1],[2, \"Documents required from the applicant\", 1],[3, \"Documents required from the employer/company\", 1],[4, \"Documents compiled by Xpatweb\", 1]]' WHERE `drop_down_lists`.`dropDownListID` = 2;
ALTER TABLE `visas` ADD `visaCountryID` INT NULL DEFAULT NULL AFTER `visaCreatedBy`;
ALTER TABLE `visa_types` ADD `visaCountryID` INT NULL DEFAULT NULL AFTER `visaTypeAlert`;

# Feature development
ALTER TABLE `visa_documentation_types` ADD `visaDocumentationTypeOrder` INT(11) NULL DEFAULT NULL AFTER `visaDocumentationTypeVisaTypeID`;
UPDATE `configurations` SET  `configurationValue` =  '{"#dashboard":[1,2, 3], "#profile":[4]}' WHERE  `configurations`.`configurationID` =8;

ALTER TABLE `countries` ADD `countryStatus` INT NOT NULL DEFAULT '0' AFTER `countryName`;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 1;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 10;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 37;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 97;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 161;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 172;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 182;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 270;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 134;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 241;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 171;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 271;
UPDATE `countries` SET `countryStatus` = '1' WHERE `countries`.`countryID` = 62;

UPDATE `visa_types` SET `visaCountryID`= 1


# Feature => IMM-101 Specialised document reminders 

INSERT INTO `roles` (`roleName`, `roleDefault`) VALUES ('Junior', '1');

DROP TABLE IF EXISTS `document_reminders`;
CREATE TABLE IF NOT EXISTS `document_reminders` (
  `documentReminderID` int(11) NOT NULL AUTO_INCREMENT,
  `documentReminderVisaDocumentationID` int(11) NOT NULL,
  `documentReminderFirstDate` date DEFAULT NULL,
  `documentReminderSecondDate` date DEFAULT NULL,
  `documentReminderStatus` smallint(1) NOT NULL DEFAULT '0' COMMENT '0->Added, 1->Uploaded',
  `documentReminderCreatedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`documentReminderID`),
  KEY `idx_documentReminderCreatedBy` (`documentReminderCreatedBy`) USING BTREE,
  KEY `idx_documentReminderVisaDocumentationID` (`documentReminderVisaDocumentationID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `document_reminders`
  ADD CONSTRAINT `document_reminders_ibfk_1` FOREIGN KEY (`documentReminderVisaDocumentationID`) REFERENCES `visa_documentation` (`visaDocumentationID`),
  ADD CONSTRAINT `document_reminders_ibfk_2` FOREIGN KEY (`documentReminderCreatedBy`) REFERENCES `users` (`userID`);
COMMIT;

ALTER TABLE `document_reminders` ADD UNIQUE(`documentReminderVisaDocumentationID`);

ALTER TABLE `document_reminders` CHANGE `documentReminderStatus` `documentReminderStatus` SMALLINT(1) NOT NULL DEFAULT '1' COMMENT '1->Added, 2->Updaded';


DROP TABLE IF EXISTS `document_status`;
CREATE TABLE IF NOT EXISTS `document_status` (
  `documentStatusID` smallint(1) NOT NULL AUTO_INCREMENT,
  `documentStatusName` varchar(11) NOT NULL,
  PRIMARY KEY (`documentStatusID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

INSERT INTO `document_status` (`documentStatusID`, `documentStatusName`) VALUES (NULL, 'Added'), (NULL, 'Updaded');

ALTER TABLE `document_reminders` ADD FOREIGN KEY (`documentReminderStatus`) REFERENCES `document_status`(`documentStatusID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `document_reminders` ADD `documentReminderCompletedAt` DATE NULL DEFAULT NULL AFTER `documentReminderCreatedBy`;
