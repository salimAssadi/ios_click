-- Departments
INSERT INTO departments (name_ar, name_en, parent_id, description, level) VALUES
('الإدارة العليا', 'Top Management', NULL, 'Top level management department', 1),
('إدارة الموارد البشرية', 'HR Department', 1, 'Human Resources Management', 2),
('إدارة تكنولوجيا المعلومات', 'IT Department', 1, 'Information Technology Department', 2),
('إدارة المالية', 'Finance Department', 1, 'Financial Management', 2),
('قسم التوظيف', 'Recruitment Section', 2, 'Recruitment and Hiring', 3),
('قسم التطوير', 'Development Section', 3, 'Software Development', 3),
('قسم الشبكات', 'Network Section', 3, 'Network and Infrastructure', 3),
('قسم المحاسبة', 'Accounting Section', 4, 'Accounting and Bookkeeping', 3);

-- Positions
INSERT INTO positions (department_id, title_ar, title_en, reports_to_id, description) VALUES
(1, 'المدير التنفيذي', 'CEO', NULL, 'Chief Executive Officer'),
(1, 'نائب المدير التنفيذي', 'Deputy CEO', 1, 'Deputy Chief Executive Officer'),
(2, 'مدير الموارد البشرية', 'HR Manager', 1, 'Human Resources Manager'),
(3, 'مدير تكنولوجيا المعلومات', 'IT Manager', 1, 'Information Technology Manager'),
(4, 'المدير المالي', 'CFO', 1, 'Chief Financial Officer'),
(5, 'مسؤول التوظيف', 'Recruitment Officer', 3, 'Recruitment and Hiring Officer'),
(6, 'مطور برمجيات أول', 'Senior Developer', 4, 'Senior Software Developer'),
(7, 'مهندس شبكات', 'Network Engineer', 4, 'Network Infrastructure Engineer'),
(8, 'محاسب رئيسي', 'Senior Accountant', 5, 'Senior Accountant');
