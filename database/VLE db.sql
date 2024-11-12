CREATE TABLE "admin" ("id" integer primary key autoincrement not null, "user_id" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE "assignments" ("id" integer primary key autoincrement not null, "name" varchar not null, "course_id" integer not null, "chapter_id" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("course_id") references "courses"("id") on delete cascade, foreign key("chapter_id") references "chapters"("id") on delete cascade);

CREATE TABLE "batches" ("id" integer primary key autoincrement not null, "name" varchar not null, "end_year" integer not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "chapters" ("id" integer primary key autoincrement not null, "course_id" integer not null, "title" varchar not null, "order" integer not null default '0', "user_id" integer, "created_at" datetime, "updated_at" datetime, "video_path" varchar, foreign key("course_id") references "courses"("id") on delete cascade, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE "courses" ("id" integer primary key autoincrement not null, "title" varchar not null, "description" text, "major_id" varchar, "user_id" integer, "created_at" datetime, "updated_at" datetime, "image" varchar, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE "departments" ("id" integer primary key autoincrement not null, "name" varchar not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "events" ("id" integer primary key autoincrement not null, "title" varchar not null, "description" text, "category" varchar not null, "passcode" varchar not null, "recruiter_id" integer not null, "date" date not null, "time" time not null, "image" varchar, "created_at" datetime, "updated_at" datetime, "link" varchar not null, foreign key("recruiter_id") references "users"("id"));

CREATE TABLE "experiences" ("id" integer primary key autoincrement not null, "user_id" integer not null, "title" varchar not null, "years" integer not null, "description" text not null, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

CREATE TABLE "job_recruiters" ("id" integer primary key autoincrement not null, "user_id" integer not null, "company_name" varchar not null, "created_at" datetime, "updated_at" datetime, "phone" varchar, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

CREATE TABLE "majors" ("id" integer primary key autoincrement not null, "name" varchar not null, "department_id" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("department_id") references "departments"("id") on delete cascade);

CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

CREATE TABLE "questions" ("id" integer primary key autoincrement not null, "quiz_id" integer not null, "question" varchar not null, "option1" varchar not null, "option2" varchar not null, "option3" varchar not null, "option4" varchar not null, "correct_answer" varchar not null, "created_at" datetime, "updated_at" datetime, foreign key("quiz_id") references "quizzes"("id") on delete cascade);

CREATE TABLE "quizzes" ("id" integer primary key autoincrement not null, "chapter_id" integer not null, "title" varchar not null, "created_at" datetime, "updated_at" datetime, foreign key("chapter_id") references "chapters"("id") on delete cascade);

CREATE TABLE "roles" ("id" integer primary key autoincrement not null, "name" varchar not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

CREATE TABLE "skills" ("id" integer primary key autoincrement not null, "user_id" integer not null, "name" varchar not null, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE sqlite_sequence(name,seq);

CREATE TABLE "student_enrolled_courses" ("id" integer primary key autoincrement not null, "user_id" integer not null, "course_id" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("course_id") references "courses"("id") on delete cascade);

CREATE TABLE "students" ("id" integer primary key autoincrement not null, "user_id" integer not null, "batch_id" integer not null, "major_id" integer not null, "created_at" datetime, "updated_at" datetime, "status" varchar, foreign key("user_id") references "users"("id"), foreign key("batch_id") references "batches"("id"), foreign key("major_id") references "majors"("id"));

CREATE TABLE "teachers" ("id" integer primary key autoincrement not null, "user_id" integer not null, "department_id" integer not null, "created_at" datetime, "updated_at" datetime, "phone" varchar, foreign key("user_id") references "users"("id") on delete cascade, foreign key("department_id") references "departments"("id") on delete cascade);

CREATE TABLE "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "role_id" integer not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "profile_picture" varchar, foreign key("role_id") references "roles"("id"));

