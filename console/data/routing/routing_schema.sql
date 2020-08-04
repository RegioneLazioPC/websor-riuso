-- -------------------------------------------------------------
-- TablePlus 2.10.2(272)
--
-- https://tableplus.com/
--
-- Database: websor_test_2
-- Generation Time: 2020-05-24 15:51:09.0160
-- -------------------------------------------------------------


-- This script only contains the table creation statements and does not fully represent the table in the database. It's still missing: indices, triggers. Do not use it as a backup.

-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS routing.configuration_id_seq;

-- Table Definition
CREATE TABLE "routing"."configuration" (
    "id" int4 NOT NULL DEFAULT nextval('routing.configuration_id_seq'::regclass),
    "tag_id" int4,
    "tag_key" text,
    "tag_value" text,
    "priority" float8,
    "maxspeed" float8,
    "maxspeed_forward" float8,
    "maxspeed_backward" float8,
    "force" bpchar,
    PRIMARY KEY ("id")
);

-- This script only contains the table creation statements and does not fully represent the table in the database. It's still missing: indices, triggers. Do not use it as a backup.

-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS routing.osm_pointsofinterest_pid_seq;

-- Table Definition
CREATE TABLE "routing"."osm_pointsofinterest" (
    "pid" int8 NOT NULL DEFAULT nextval('routing.osm_pointsofinterest_pid_seq'::regclass),
    "osm_id" int8,
    "vertex_id" int8,
    "edge_id" int8,
    "side" bpchar,
    "fraction" float8,
    "length_m" float8,
    "tag_name" text,
    "tag_value" text,
    "name" text,
    "the_geom" geometry,
    "new_geom" geometry,
    PRIMARY KEY ("pid")
);

-- This script only contains the table creation statements and does not fully represent the table in the database. It's still missing: indices, triggers. Do not use it as a backup.

-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS routing.osm_ways_gid_seq;

-- Table Definition
CREATE TABLE "routing"."osm_ways" (
    "gid" int8 NOT NULL DEFAULT nextval('routing.osm_ways_gid_seq'::regclass),
    "osm_id" int8,
    "tag_id" int4,
    "length" float8,
    "length_m" float8,
    "name" text,
    "source" int8,
    "target" int8,
    "source_osm" int8,
    "target_osm" int8,
    "cost" float8,
    "reverse_cost" float8,
    "cost_s" float8,
    "reverse_cost_s" float8,
    "rule" text,
    "one_way" int4,
    "oneway" text,
    "x1" float8,
    "y1" float8,
    "x2" float8,
    "y2" float8,
    "maxspeed_forward" float8,
    "maxspeed_backward" float8,
    "priority" float8 DEFAULT 1,
    "the_geom" geometry,
    PRIMARY KEY ("gid")
);

-- This script only contains the table creation statements and does not fully represent the table in the database. It's still missing: indices, triggers. Do not use it as a backup.

-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS routing.osm_ways_vertices_pgr_id_seq;

-- Table Definition
CREATE TABLE "routing"."osm_ways_vertices_pgr" (
    "id" int8 NOT NULL DEFAULT nextval('routing.osm_ways_vertices_pgr_id_seq'::regclass),
    "osm_id" int8,
    "eout" int4,
    "lon" numeric(11,8),
    "lat" numeric(11,8),
    "cnt" int4,
    "chk" int4,
    "ein" int4,
    "the_geom" geometry,
    PRIMARY KEY ("id")
);

ALTER TABLE "routing"."osm_ways" ADD FOREIGN KEY ("tag_id") REFERENCES "routing"."configuration"("tag_id");
ALTER TABLE "routing"."osm_ways" ADD FOREIGN KEY ("target_osm") REFERENCES "routing"."osm_ways_vertices_pgr"("osm_id");
ALTER TABLE "routing"."osm_ways" ADD FOREIGN KEY ("source_osm") REFERENCES "routing"."osm_ways_vertices_pgr"("osm_id");
ALTER TABLE "routing"."osm_ways" ADD FOREIGN KEY ("source") REFERENCES "routing"."osm_ways_vertices_pgr"("id");
ALTER TABLE "routing"."osm_ways" ADD FOREIGN KEY ("target") REFERENCES "routing"."osm_ways_vertices_pgr"("id");
