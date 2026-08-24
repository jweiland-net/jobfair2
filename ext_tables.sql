#
# Table structure for table 'tx_jobboard_domain_model_job'
#
CREATE TABLE tx_jobboard_domain_model_job
(
	address int(11) unsigned DEFAULT '0' NOT NULL,
	salary_min decimal(10, 2) DEFAULT '0.00' NOT NULL,
	salary_max decimal(10, 2) DEFAULT '0.00' NOT NULL
);

#
# Table structure for table 'tx_jobboard_domain_model_salarygrade'
#
CREATE TABLE tx_jobboard_domain_model_salarygrade
(
	flat_amount decimal(10, 2) DEFAULT '0.00' NOT NULL
);

#
# Table structure for table 'tx_jobboard_domain_model_salarystep'
#
CREATE TABLE tx_jobboard_domain_model_salarystep
(
	amount decimal(10, 2) DEFAULT '0.00' NOT NULL
);
