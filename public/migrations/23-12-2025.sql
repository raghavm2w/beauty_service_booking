INSERT INTO categories (name, description) VALUES ('Hair', 'Hair care and styling services including haircuts, coloring, treatments, and styling for men and women.'),
 ('Skin', 'Skin care services such as facials, cleanups, bleaching, and advanced skin treatments.'), ('Nails', 'Nail care services including manicure, pedicure, nail art, gel nails, and nail extensions.'), ('Makeup', 'Professional makeup services for parties, events, bridal occasions, and photoshoots.');

 -- Hair subcategories
INSERT INTO subcategories (category_id, name)
VALUES
((SELECT id FROM categories WHERE name = 'Hair'), 'Haircut'),
((SELECT id FROM categories WHERE name = 'Hair'), 'Hair Color'),
((SELECT id FROM categories WHERE name = 'Hair'), 'Hair Treatment');

-- Skin subcategories
INSERT INTO subcategories (category_id, name)
VALUES
((SELECT id FROM categories WHERE name = 'Skin'), 'Facial'),
((SELECT id FROM categories WHERE name = 'Skin'), 'Cleanup'),
((SELECT id FROM categories WHERE name = 'Skin'), 'Bleach');

-- Nails subcategories
INSERT INTO subcategories (category_id, name)
VALUES
((SELECT id FROM categories WHERE name = 'Nails'), 'Manicure'),
((SELECT id FROM categories WHERE name = 'Nails'), 'Pedicure'),
((SELECT id FROM categories WHERE name = 'Nails'), 'Nail Art');

-- Makeup subcategories
INSERT INTO subcategories (category_id, name)
VALUES
((SELECT id FROM categories WHERE name = 'Makeup'), 'Party Makeup'),
((SELECT id FROM categories WHERE name = 'Makeup'), 'Bridal Makeup');