-- Migration: Populate initial menu items
-- Run this after creating the menu_items table to populate with default menu items

-- Insert default top row menu items (only if they don't exist)
INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ໜ້າຫຼັກ', '🏠', 'index.html', 'top', 1, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'index.html' AND menu_row = 'top');

INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ກິດຈະກຳ', '📌', 'activities.html', 'top', 2, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'activities.html' AND menu_row = 'top');

INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ເອກະສານ', '📄', 'documents.html', 'top', 3, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'documents.html' AND menu_row = 'top');

INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ວິທະຍາໄລຄູ', '🏫', 'teacher-college.html', 'top', 4, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'teacher-college.html' AND menu_row = 'top');

INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ຜູ້ເຮັດວຽກສຶກສານິເທດພາຍໃນ', '👨‍🏫', 'internal-worker.html', 'top', 5, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'internal-worker.html' AND menu_row = 'top');

-- Insert default bottom row menu items (only if they don't exist)
INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ຫ້ອງການສຶກສາທິການເເລະກິລາເມືອງ', '🏛', 'district-education.html', 'bottom', 1, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'district-education.html' AND menu_row = 'bottom');

INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'ພະເເນກສຶກສາທິການເເລະກິລາແຂວງ', '🗺', 'province-activities.html', 'bottom', 2, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'province-activities.html' AND menu_row = 'bottom');

INSERT INTO menu_items (menu_text, menu_icon, menu_url, menu_row, display_order, is_active)
SELECT 'CPD', '🎓', 'cpd.html', 'bottom', 3, TRUE
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_url = 'cpd.html' AND menu_row = 'bottom');

