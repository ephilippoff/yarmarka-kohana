INSERT INTO category(
            title, parent_id, is_ready, weight, seo_name, url, text_required)
    VALUES ('Новости', 1, 1, 500, 'novosti', 'novosti', 1);


INSERT INTO attribute(
            title, type, seo_name)
    VALUES ('Категория', 'list', 'news-category');


INSERT INTO attribute_element(
            attribute, title,  seo_name, url)
    VALUES (349, 'Инфографика', 'infografika', 'infografika');
    
INSERT INTO attribute_element(
            attribute, title,  seo_name, url)
    VALUES (349, 'Авто', 'avto', 'avto');

INSERT INTO attribute_element(
            attribute, title,  seo_name, url)
    VALUES (349, 'Город', 'city', 'city');

INSERT INTO attribute_element(
            attribute, title,  seo_name, url)
    VALUES (349, 'Закон и порядок', 'lawandorder', 'lawandorder');

INSERT INTO attribute_element(
            attribute, title,  seo_name, url)
    VALUES (349, 'Общество', 'social', 'social');

INSERT INTO attribute_element(
            attribute, title,  seo_name, url)
    VALUES (349, 'Труд', 'work', 'work');
