Ce projet semble être un système de gestion d'entreprise, probablement pour gérer les absences, les activités, les rendez-vous, les clients, et les documents. Il peut s'agir d'un CRM (Customer Relationship Management) ou d'un ERP (Enterprise Resource Planning).

-- absence
CREATE TABLE daybyday.absences(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  reason VARCHAR(255) NOT NULL,
  `comment` TEXT,
  start_at TIMESTAMP NOT NULL,
  end_at TIMESTAMP NOT NULL,
  medical_certificate TINYINT(1),
  user_id INT UNSIGNED,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;

--  activite et evenement dans le systeme
CREATE TABLE daybyday.activities(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  log_name VARCHAR(255),
  causer_id BIGINT UNSIGNED,
  causer_type VARCHAR(255),
  `text` VARCHAR(255) NOT NULL,
  source_type VARCHAR(255) NOT NULL,
  source_id BIGINT UNSIGNED,
  ip_address VARCHAR(64) NOT NULL,
  properties JSON,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;

-- rendez-vous
CREATE TABLE daybyday.appointments(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  `description` VARCHAR(255),
  source_type VARCHAR(255),
  source_id BIGINT UNSIGNED,
  color VARCHAR(10) NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  client_id INT UNSIGNED,
  start_at TIMESTAMP,
  end_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX appointments_source_type_source_id_index USING BTREE ON
  daybyday.appointments(source_type, source_id)
;

-- heure de debut entreprise
CREATE TABLE daybyday.business_hours(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `day` VARCHAR(255) NOT NULL,
  open_time TIME,
  close_time TIME,
  settings_id INT UNSIGNED,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 8
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.clients(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  address VARCHAR(255),
  zipcode VARCHAR(255),
  city VARCHAR(255),
  company_name VARCHAR(255) NOT NULL,
  vat VARCHAR(255),
  company_type VARCHAR(255),
  client_number BIGINT,
  user_id INT UNSIGNED NOT NULL,
  industry_id INT UNSIGNED NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;

-- Permet aux utilisateurs d'ajouter des commentaires liés à d'autres entités comme les activités ou les documents.
CREATE TABLE daybyday.comments(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  source_type VARCHAR(255) NOT NULL,
  source_id BIGINT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX comments_source_type_source_id_index USING BTREE ON
  daybyday.comments(source_type, source_id)
;

-- contact des clients
CREATE TABLE daybyday.contacts(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  primary_number VARCHAR(255),
  secondary_number VARCHAR(255),
  client_id INT UNSIGNED NOT NULL,
  is_primary TINYINT(1) NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;

-- liaison user et departement
CREATE TABLE daybyday.department_user(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  department_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 2
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;

-- ireo karazana asa anaty entreprise
CREATE TABLE daybyday.departments(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 2
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.documents(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  size VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  original_filename VARCHAR(255) NOT NULL,
  mime VARCHAR(255) NOT NULL,
  integration_id VARCHAR(255),
  integration_type VARCHAR(255) NOT NULL,
  source_type VARCHAR(255) NOT NULL,
  source_id BIGINT UNSIGNED NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX documents_source_type_source_id_index USING BTREE ON
  daybyday.documents(source_type, source_id)
;


CREATE TABLE daybyday.industries(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 26
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.integrations(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  client_id INT,
  user_id INT,
  client_secret INT,
  api_key VARCHAR(255),
  api_type VARCHAR(255),
  org_id VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic DEFAULT CHARACTER SET = `utf8mb3`
  COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.invoice_lines(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  `comment` TEXT NOT NULL,
  price INT NOT NULL,
  invoice_id INT UNSIGNED,
  offer_id INT UNSIGNED,
  `type` VARCHAR(255),
  quantity INT,
  product_id VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.invoices(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  invoice_number BIGINT,
  sent_at DATETIME,
  due_at DATETIME,
  integration_invoice_id VARCHAR(255),
  integration_type VARCHAR(255),
  source_type VARCHAR(255),
  source_id BIGINT UNSIGNED,
  client_id INT UNSIGNED NOT NULL,
  offer_id INT UNSIGNED,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX invoices_source_type_source_id_index USING BTREE ON
  daybyday.invoices(source_type, source_id)
;


CREATE TABLE daybyday.leads(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  status_id INT UNSIGNED NOT NULL,
  user_assigned_id INT UNSIGNED NOT NULL,
  client_id INT UNSIGNED NOT NULL,
  user_created_id INT UNSIGNED NOT NULL,
  qualified TINYINT(1) NOT NULL DEFAULT 0,
  result VARCHAR(255),
  deadline DATETIME NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX leads_qualified_index USING BTREE ON daybyday.leads(qualified);


CREATE TABLE daybyday.mails(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(255) NOT NULL,
  body VARCHAR(255),
  template VARCHAR(255),
  email VARCHAR(255),
  user_id INT UNSIGNED,
  send_at TIMESTAMP,
  sent_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.migrations(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  migration VARCHAR(255) NOT NULL,
  batch INT NOT NULL,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 43
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.notifications(
  id CHAR(36) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  notifiable_type VARCHAR(255) NOT NULL,
  notifiable_id BIGINT UNSIGNED NOT NULL,
  `data` TEXT NOT NULL,
  read_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic DEFAULT CHARACTER SET = `utf8mb3`
  COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX notifications_notifiable_type_notifiable_id_index USING BTREE ON
  daybyday.notifications(notifiable_type, notifiable_id)
;


CREATE TABLE daybyday.offers(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  sent_at DATETIME,
  source_type VARCHAR(255),
  source_id BIGINT UNSIGNED,
  client_id INT UNSIGNED NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX offers_source_type_source_id_index USING BTREE ON daybyday.offers
  (source_type, source_id)
;


CREATE TABLE daybyday.password_resets(
email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL
) ENGINE = InnoDB ROW_FORMAT = Dynamic DEFAULT CHARACTER SET = `utf8mb3`
  COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX password_resets_email_index USING BTREE ON daybyday.password_resets
  (email)
;


CREATE INDEX password_resets_token_index USING BTREE ON daybyday.password_resets
  (token)
;


CREATE TABLE daybyday.payments(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  amount INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  payment_source VARCHAR(255) NOT NULL,
  payment_date DATE NOT NULL,
  integration_payment_id VARCHAR(255),
  integration_type VARCHAR(255),
  invoice_id INT UNSIGNED NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.permission_role(
permission_id INT UNSIGNED NOT NULL, role_id INT UNSIGNED NOT NULL,
  PRIMARY KEY(permission_id, role_id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic DEFAULT CHARACTER SET = `utf8mb3`
  COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.permissions(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  display_name VARCHAR(255),
  `description` VARCHAR(255),
  `grouping` VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id),
  CONSTRAINT permissions_name_unique UNIQUE(`name`)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 42
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.products(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  external_id VARCHAR(255) NOT NULL,
  `description` TEXT,
  `number` VARCHAR(255) NOT NULL,
  default_type VARCHAR(255) NOT NULL,
  archived TINYINT(1) NOT NULL,
  integration_type VARCHAR(255),
  integration_id BIGINT UNSIGNED,
  price INT NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX products_integration_type_integration_id_index USING BTREE ON
  daybyday.products(integration_type, integration_id)
;


CREATE TABLE daybyday.projects(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  status_id INT UNSIGNED NOT NULL,
  user_assigned_id INT UNSIGNED NOT NULL,
  user_created_id INT UNSIGNED NOT NULL,
  client_id INT UNSIGNED NOT NULL,
  invoice_id INT UNSIGNED,
  deadline DATE NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.role_user(
user_id INT UNSIGNED NOT NULL, role_id INT UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, role_id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic DEFAULT CHARACTER SET = `utf8mb3`
  COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.roles(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  display_name VARCHAR(255),
  `description` VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 5
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.settings(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_number INT NOT NULL,
  invoice_number INT NOT NULL,
  country VARCHAR(255),
  company VARCHAR(255),
  currency VARCHAR(3) NOT NULL DEFAULT 'USD',
  vat SMALLINT NOT NULL DEFAULT 725,
  max_users INT NOT NULL,
  `language` VARCHAR(2) NOT NULL DEFAULT 'EN',
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 2
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.statuses(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  source_type VARCHAR(255) NOT NULL,
  color VARCHAR(255) NOT NULL DEFAULT '#000000',
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 16
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.subscriptions(
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  stripe_id VARCHAR(255) NOT NULL,
  stripe_status VARCHAR(255) NOT NULL,
  stripe_plan VARCHAR(255) NOT NULL,
  quantity INT NOT NULL,
  trial_ends_at TIMESTAMP,
  ends_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE INDEX subscriptions_user_id_stripe_status_index USING BTREE ON
  daybyday.subscriptions(user_id, stripe_status)
;


CREATE TABLE daybyday.tasks(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  status_id INT UNSIGNED NOT NULL,
  user_assigned_id INT UNSIGNED NOT NULL,
  user_created_id INT UNSIGNED NOT NULL,
  client_id INT UNSIGNED NOT NULL,
  project_id INT UNSIGNED,
  deadline DATE NOT NULL,
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


CREATE TABLE daybyday.users(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  external_id VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  `password` VARCHAR(60) NOT NULL,
  address VARCHAR(255),
  primary_number VARCHAR(255),
  secondary_number VARCHAR(255),
  image_path VARCHAR(255),
  remember_token VARCHAR(100),
  `language` VARCHAR(2) NOT NULL DEFAULT 'EN',
  deleted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY(id),
  CONSTRAINT users_email_unique UNIQUE(email)
) ENGINE = InnoDB ROW_FORMAT = Dynamic AUTO_INCREMENT = 2
  DEFAULT CHARACTER SET = `utf8mb3` COLLATE = `utf8mb3_unicode_ci`
;


ALTER TABLE daybyday.absences
  ADD CONSTRAINT absences_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE Cascade
      ON UPDATE Cascade
;


ALTER TABLE daybyday.appointments
  ADD CONSTRAINT appointments_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.appointments
  ADD CONSTRAINT appointments_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.business_hours
  ADD CONSTRAINT business_hours_settings_id_foreign
    FOREIGN KEY (settings_id) REFERENCES daybyday.settings (id) ON DELETE Cascade
      ON UPDATE Cascade
;


ALTER TABLE daybyday.clients
  ADD CONSTRAINT clients_industry_id_foreign
    FOREIGN KEY (industry_id) REFERENCES daybyday.industries (id)
      ON DELETE No action ON UPDATE No action
;


ALTER TABLE daybyday.clients
  ADD CONSTRAINT clients_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.comments
  ADD CONSTRAINT comments_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.contacts
  ADD CONSTRAINT contacts_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.department_user
  ADD CONSTRAINT department_user_department_id_foreign
    FOREIGN KEY (department_id) REFERENCES daybyday.departments (id)
      ON DELETE Cascade ON UPDATE No action
;


ALTER TABLE daybyday.department_user
  ADD CONSTRAINT department_user_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.invoice_lines
  ADD CONSTRAINT invoice_lines_invoice_id_foreign
    FOREIGN KEY (invoice_id) REFERENCES daybyday.invoices (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.invoice_lines
  ADD CONSTRAINT invoice_lines_offer_id_foreign
    FOREIGN KEY (offer_id) REFERENCES daybyday.offers (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.invoices
  ADD CONSTRAINT invoices_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.invoices
  ADD CONSTRAINT invoices_offer_id_foreign
    FOREIGN KEY (offer_id) REFERENCES daybyday.offers (id) ON DELETE Set null
      ON UPDATE No action
;


ALTER TABLE daybyday.leads
  ADD CONSTRAINT leads_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.leads
  ADD CONSTRAINT leads_status_id_foreign
    FOREIGN KEY (status_id) REFERENCES daybyday.statuses (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.leads
  ADD CONSTRAINT leads_user_assigned_id_foreign
    FOREIGN KEY (user_assigned_id) REFERENCES daybyday.users (id)
      ON DELETE No action ON UPDATE No action
;


ALTER TABLE daybyday.leads
  ADD CONSTRAINT leads_user_created_id_foreign
    FOREIGN KEY (user_created_id) REFERENCES daybyday.users (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.mails
  ADD CONSTRAINT mails_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.offers
  ADD CONSTRAINT offers_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.payments
  ADD CONSTRAINT payments_invoice_id_foreign
    FOREIGN KEY (invoice_id) REFERENCES daybyday.invoices (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.permission_role
  ADD CONSTRAINT permission_role_permission_id_foreign
    FOREIGN KEY (permission_id) REFERENCES daybyday.permissions (id)
      ON DELETE Cascade ON UPDATE Cascade
;


ALTER TABLE daybyday.permission_role
  ADD CONSTRAINT permission_role_role_id_foreign
    FOREIGN KEY (role_id) REFERENCES daybyday.roles (id) ON DELETE Cascade
      ON UPDATE Cascade
;


ALTER TABLE daybyday.projects
  ADD CONSTRAINT projects_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.projects
  ADD CONSTRAINT projects_invoice_id_foreign
    FOREIGN KEY (invoice_id) REFERENCES daybyday.invoices (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.projects
  ADD CONSTRAINT projects_status_id_foreign
    FOREIGN KEY (status_id) REFERENCES daybyday.statuses (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.projects
  ADD CONSTRAINT projects_user_assigned_id_foreign
    FOREIGN KEY (user_assigned_id) REFERENCES daybyday.users (id)
      ON DELETE No action ON UPDATE No action
;


ALTER TABLE daybyday.projects
  ADD CONSTRAINT projects_user_created_id_foreign
    FOREIGN KEY (user_created_id) REFERENCES daybyday.users (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.role_user
  ADD CONSTRAINT role_user_role_id_foreign
    FOREIGN KEY (role_id) REFERENCES daybyday.roles (id) ON DELETE Cascade
      ON UPDATE Cascade
;


ALTER TABLE daybyday.role_user
  ADD CONSTRAINT role_user_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES daybyday.users (id) ON DELETE Cascade
      ON UPDATE Cascade
;


ALTER TABLE daybyday.tasks
  ADD CONSTRAINT tasks_client_id_foreign
    FOREIGN KEY (client_id) REFERENCES daybyday.clients (id) ON DELETE Cascade
      ON UPDATE No action
;


ALTER TABLE daybyday.tasks
  ADD CONSTRAINT tasks_project_id_foreign
    FOREIGN KEY (project_id) REFERENCES daybyday.projects (id) ON DELETE Set null
      ON UPDATE No action
;


ALTER TABLE daybyday.tasks
  ADD CONSTRAINT tasks_status_id_foreign
    FOREIGN KEY (status_id) REFERENCES daybyday.statuses (id) ON DELETE No action
      ON UPDATE No action
;


ALTER TABLE daybyday.tasks
  ADD CONSTRAINT tasks_user_assigned_id_foreign
    FOREIGN KEY (user_assigned_id) REFERENCES daybyday.users (id)
      ON DELETE No action ON UPDATE No action
;


ALTER TABLE daybyday.tasks
  ADD CONSTRAINT tasks_user_created_id_foreign
    FOREIGN KEY (user_created_id) REFERENCES daybyday.users (id) ON DELETE No action
      ON UPDATE No action
;

