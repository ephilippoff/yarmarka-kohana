-- Column: url

-- ALTER TABLE category DROP COLUMN url;

ALTER TABLE category ADD COLUMN url character varying(255);

-- Column: url

-- ALTER TABLE attribute_element DROP COLUMN url;

ALTER TABLE attribute_element ADD COLUMN url character varying(250);
