# Database Setup Instructions

## Creating Content Management Tables

To fix the error "relation 'cpd_content' does not exist", you need to run the SQL script to create all the new tables.

### Method 1: Using pgAdmin (Easiest)

1. Open **pgAdmin**
2. Connect to your PostgreSQL server
3. Navigate to your database (usually `edu-pro`)
4. Right-click on your database → **Query Tool**
5. Open the file `database_content_tables.sql` in the query editor
6. Click **Execute** (or press F5)
7. You should see "Query returned successfully"

### Method 2: Using Command Line

```bash
# Navigate to your project directory
cd C:\xampp\htdocs\internal-education-worker-report

# Run the SQL script (replace with your database name and credentials)
psql -U postgres -d edu-pro -f database_content_tables.sql
```

### Method 3: Copy and Paste SQL

If the above methods don't work, you can copy the entire content of `database_content_tables.sql` and paste it into pgAdmin Query Tool, then execute it.

## Tables That Will Be Created

After running the script, these tables will be created:

1. ✅ `teacher_colleges` - For teacher college information
2. ✅ `internal_workers` - For internal worker data
3. ✅ `districts` - For district education office data
4. ✅ `provinces` - For province-level activities
5. ✅ `cpd_content` - For CPD resources, programs, and certificates

## Verify Tables Were Created

After running the script, you can verify by running this query in pgAdmin:

```sql
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
AND table_name IN ('teacher_colleges', 'internal_workers', 'districts', 'provinces', 'cpd_content')
ORDER BY table_name;
```

You should see all 5 tables listed.

## Troubleshooting

If you get a foreign key error, make sure you've already run `database_setup_postgresql.sql` first, as the new tables reference the `users` table.

