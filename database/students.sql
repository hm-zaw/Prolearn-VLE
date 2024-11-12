CREATE TABLE "students" ("id" integer primary key autoincrement not null, "user_id" integer not null, "batch_id" integer not null, "major_id" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id"), foreign key("batch_id") references "batches"("id"), foreign key("major_id") references "majors"("id"));

