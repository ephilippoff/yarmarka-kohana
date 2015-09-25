ALTER TABLE kupon_group ADD COLUMN contacts character varying(1000);
ALTER TABLE kupon_group ADD COLUMN address character varying(1000);
ALTER TABLE kupon_group ADD COLUMN address_details character varying(1000);
ALTER TABLE kupon_group ADD COLUMN support_info character varying(1000);
ALTER TABLE kupon_group ADD COLUMN expiration_date timestamp without time zone;


//seeds - Тестовые данные (/kupon/print/0)


INSERT INTO kupon_group(
            id, title, description, price, object_id, contacts, address, 
            address_details, support_info, expiration_date)
    VALUES (0, 'Тест', 'Тестовое описание', '1000', 3974787, 'контакты', 'адрес', 
            'режим работы', 'подробная информация', '2015-12-01');

INSERT INTO kupon(
            id, code, price, invoice_id, count, "number", state, order_id, 
            kupon_group_id, access_key)
    VALUES (0, 1111, 1000, NULL, 1, 1111111, 'avail', NULL, 
            0, NULL);

INSERT INTO object_movement(
            id, begin_state, end_state, count, object_id, kupon_id, date, 
            order_id, description)
    VALUES (0, 'initial', 'avail', 1, NULL, 0, NOW(), 
            NULL, NULL);
