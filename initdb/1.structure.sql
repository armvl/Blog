DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS "post_tags";
DROP TABLE IF EXISTS "post";
DROP TABLE IF EXISTS "tag";
DROP SEQUENCE IF EXISTS "account_id_seq";
DROP SEQUENCE IF EXISTS "post_id_seq";
DROP SEQUENCE IF EXISTS "tag_id_seq";
DROP FUNCTION IF EXISTS "insert_test_data";


CREATE SEQUENCE "account_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "post_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "tag_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;


CREATE TABLE "account" (
  "id" BIGINT DEFAULT nextval('account_id_seq') NOT NULL,
  "uid" UUID DEFAULT gen_random_uuid(),
  "name" VARCHAR(1000) DEFAULT '',
  "create" TIMESTAMPTZ DEFAULT now() NOT NULL,
  "update" TIMESTAMPTZ DEFAULT now() NOT NULL,
  PRIMARY KEY("id")
) WITH (OIDS=FALSE);

CREATE TABLE "post" (
  "id" BIGINT DEFAULT nextval('post_id_seq') NOT NULL,
  "uid" UUID DEFAULT gen_random_uuid(),
  "account_id" INT NOT NULL,
  "title" VARCHAR(300) default '',
  "entry" TEXT DEFAULT '',
  "content" TEXT DEFAULT '',
  "create" TIMESTAMPTZ DEFAULT now() NOT NULL,
  "update" TIMESTAMPTZ DEFAULT now() NOT NULL,
  PRIMARY KEY("id")
) WITH (OIDS=FALSE);

CREATE TABLE "tag" (
  "id" BIGINT DEFAULT nextval('tag_id_seq') NOT NULL,
  "label" VARCHAR(300) default '',
  PRIMARY KEY("id")
) WITH (OIDS=FALSE);

CREATE TABLE "post_tags" (
  "tag_id" BIGINT NOT NULL REFERENCES "tag"("id") ON DELETE CASCADE,
  "post_id" BIGINT NOT NULL REFERENCES "post"("id") ON DELETE CASCADE
) WITH (OIDS=FALSE);


CREATE FUNCTION insert_test_data (posts_count INT) RETURNS VOID AS $$
DECLARE
  postId INT;
  accountId INT;
  postTitle VARCHAR = 'Lorem Ipsum';
  postEntry TEXT = '<p>Латинский фрагмент содержит хоть и устаревшие, но настоящие слова. Таким образом, распределение по количеству букв и пробелов в Lorem Ipsum взято из реального языка. Благодаря этому специалист может предугадать размер текста, который будет вставлен в блок. Если просто скопировать одно и то же слово или фразу, то точность будет гораздо ниже. Кроме того, Lorem Ipsum выглядит достаточно эстетично, так как написан на настоящем языке.</p>';
  postContent TEXT = '<h3>Appreciation of diversity</h3><p>Getting used to an entirely different culture can be challenging. While it’s also nice to learn about cultures online or from books, nothing comes close to experiencing cultural diversity in person. You learn to appreciate each and every single one of the differences while you become more culturally fluid.<blockquote><p>The real voyage of discovery consists not in seeking new landscapes, but having new eyes.<p><strong>Marcel Proust</strong></blockquote><h3>The three greatest things you learn from traveling</h3><p>Like all the great things on earth traveling teaches us by example. Here are some of the most precious lessons I’ve learned over the years of traveling.<figure class="ck-widget image image-style-side"><img alt="A lone wanderer looking at Mount Bromo volcano in Indonesia."sizes=100vw src=https://ckeditor.com/docs/ckeditor5/latest/assets/img/volcano.jpg srcset="https://ckeditor.com/docs/ckeditor5/latest/assets/img/volcano.jpg, https://ckeditor.com/docs/ckeditor5/latest/assets/img/volcano_2x.jpg 2x"><figcaption aria-label="Caption for image: A lone wanderer looking at Mount Bromo volcano in Indonesia."class="ck-editor__editable ck-editor__nested-editable" data-placeholder="Enter image caption"role=textbox>Leaving your comfort zone might lead you to such beautiful sceneries like this one.</figcaption></figure><h3>Appreciation of diversity</h3><p>Getting used to an entirely different culture can be challenging. While it’s also nice to learn about cultures online or from books, nothing comes close to experiencing cultural diversity in person. You learn to appreciate each and every single one of the differences while you become more culturally fluid.<blockquote><p>The real voyage of discovery consists not in seeking new landscapes, but having new eyes.<p><strong>Marcel Proust</strong></blockquote><h3>Improvisation</h3><p>Life doesnt allow us to execute every single plan perfectly. This especially seems to be the case when you travel. You plan it down to every minute with a big checklist. But when it comes to executing it, something always comes up and you’re left with your improvising skills. You learn to adapt as you go. Here’s how my travel checklist looks now:<ul><li>buy the ticket<li>start your adventure</ul><figure class="ck-widget image image-style-side"><img alt="Three monks ascending the stairs of an ancient temple."sizes=100vw src=https://ckeditor.com/docs/ckeditor5/latest/assets/img/umbrellas.jpg srcset="https://ckeditor.com/docs/ckeditor5/latest/assets/img/umbrellas.jpg, https://ckeditor.com/docs/ckeditor5/latest/assets/img/umbrellas_2x.jpg 2x"><figcaption aria-label="Caption for image: Three monks ascending the stairs of an ancient temple."class="ck-editor__editable ck-editor__nested-editable" data-placeholder="Enter image caption"role=textbox>Three monks ascending the stairs of an ancient temple.</figcaption></figure><h3>Confidence</h3><p>Going to a new place can be quite terrifying. While change and uncertainty make us scared, traveling teaches us how ridiculous it is to be afraid of something before it happens. The moment you face your fear and see there is nothing to be afraid of, is the moment you discover bliss.</p>';
BEGIN
  INSERT INTO account (uid, name) VALUES ('0aecb25f-f8d6-4e6f-abf7-47cca8594a06', 'test account') RETURNING id INTO accountId;
  INSERT INTO "tag" (label) VALUES ('sfsdfsdf'),('vbnvbnvbn'),('werwerwer'),('vbnvbnvbn'),('werwerwer'),('asddfg'),('sfsdfsdf'),('asddfg'),('drftg'),('sfsdfsdf'),('vbnvbnvbn'),('werwerwer'),('asddfg'),('drftg'),('sdfsdf ffrt'),('drftg'),('werwerwer'),('asddfg'),('drftg'),('sdfsdf ffrt'),('sfsdfsdf'),('vbnvbnvbn'),('sdfsdf ffrt');

  FOR i in 1..posts_count LOOP
    INSERT INTO "post" (account_id, title, entry, content)
      VALUES (accountId, postTitle || ' ' || i, postEntry, postContent) RETURNING id INTO postId;

    FOR j in 1..(23 * random()) LOOP
      INSERT INTO "post_tags" (post_id, tag_id) VALUES (postId, j);
    END LOOP;

    RAISE NOTICE 'Post "%" created', postTitle || ' ' || i;
  END LOOP;
END;
$$ LANGUAGE plpgsql;


ALTER SEQUENCE "account_id_seq" OWNED BY account."id";
ALTER SEQUENCE "post_id_seq" OWNED BY "post"."id";


CREATE UNIQUE INDEX "post_tags_idx" ON "post_tags"(tag_id, post_id);


CREATE INDEX "account_uid_idx" ON account USING btree ("uid");
CREATE INDEX "post_uid_idx" ON "post" USING btree ("uid");

-- select insert_test_data(10);

