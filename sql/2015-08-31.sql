delete from object_contacts where not exists (select id from contacts where object_contacts.contact_id = contacts.id)

ALTER TABLE object_contacts
  ADD CONSTRAINT object_contacts_contact_fk FOREIGN KEY (contact_id)
      REFERENCES contacts (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;


ALTER TABLE contacts
  ADD CONSTRAINT contacts_unique UNIQUE(contact_type_id, contact_clear);
