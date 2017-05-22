
-- nette commits - initial data

INSERT IGNORE INTO `project` (`id`, `name`, `slug`, `sort`) VALUES
('8d6fe94a-42c8-4bcf-89f0-b70725701999',	'Framework',	'framework',	1),
('0d41f3b0-6a21-4744-854e-ad0556848fa6',	'Documentation',	'docs',	2),
('f9015c35-1092-49c4-bad6-f1ad1cfd3660',	'Promo',	'promo',	3),
('8c83d4f1-dd34-4724-88b9-a67be607e363',	'Tools',	'tools',	4),
('83579ec5-30f3-4fa3-b768-4c30743bdf11',	'Tester',	'tester',	5);


INSERT IGNORE INTO `repository` (`name`, `project_id`) VALUES

-- Framework
('nette/application',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/bootstrap',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/caching',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/component-model',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/database',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/deprecated',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/di',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/finder',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/forms',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/http',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/latte',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/mail',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/neon',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/nette',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/php-generator',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/reflection',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/robot-loader',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/routing',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/safe-stream',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/schema',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/security',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/tokenizer',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/tracy',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),
('nette/utils',	'8d6fe94a-42c8-4bcf-89f0-b70725701999'),

-- Documentation
('nette/docs',	'0d41f3b0-6a21-4744-854e-ad0556848fa6'),

-- Promo
('nette/examples',	'f9015c35-1092-49c4-bad6-f1ad1cfd3660'),
('nette/sandbox',	'f9015c35-1092-49c4-bad6-f1ad1cfd3660'),
('nette/tutorial-quickstart',	'f9015c35-1092-49c4-bad6-f1ad1cfd3660'),
('nette/web-project',	'f9015c35-1092-49c4-bad6-f1ad1cfd3660'),

-- Tools
('nette/code-checker',	'8c83d4f1-dd34-4724-88b9-a67be607e363'),
('nette/coding-standard',	'8c83d4f1-dd34-4724-88b9-a67be607e363'),
('nette/command-line',	'8c83d4f1-dd34-4724-88b9-a67be607e363'),
('nette/type-fixer',	'8c83d4f1-dd34-4724-88b9-a67be607e363'),

-- Tester
('nette/tester',	'83579ec5-30f3-4fa3-b768-4c30743bdf11');
