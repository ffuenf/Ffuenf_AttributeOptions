-- add table prefix if you have one
DELETE FROM core_config_data WHERE path like 'attributeoptions/%';
DELETE FROM core_resource WHERE code = 'attributeoptions_setup';