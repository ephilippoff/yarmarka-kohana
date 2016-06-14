update contacts
set verified_user_id = NULL
where not exists (select id from "user" as u where contacts.verified_user_id = u.id);

ALTER TABLE contacts
  ADD CONSTRAINT contacts_verified_user_fkey FOREIGN KEY (verified_user_id)
      REFERENCES "user" (id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;

ALTER TABLE order_item_temp ADD COLUMN service_name character varying(255);
ALTER TABLE order_item_temp ALTER COLUMN service_name SET NOT NULL;
