-- Verify that the new columns were added successfully
-- Run this query to check all columns in the booth table

SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_TYPE
FROM 
    INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'booth'
    AND COLUMN_NAME IN ('z_index', 'font_size', 'border_width', 'border_radius', 'opacity')
ORDER BY 
    ORDINAL_POSITION;

-- Alternative: Simple check to see if columns exist
DESCRIBE `booth`;

