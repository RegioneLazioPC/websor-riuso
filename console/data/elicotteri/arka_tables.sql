
-- This script only contains the table creation statements and does not fully represent the table in the database. It's still missing: indices, triggers. Do not use it as a backup.

-- Table Definition
CREATE TABLE "public"."utl_arka" (
    "id" int4,
    "device_id" int4,
    "device_name" varchar(200),
    "device_group_id" int8,
    "device_group_name" varchar(200),
    "utc_timestamp" timestamp,
    "local_timestamp" timestamp,
    "event_code" varchar(1),
    "event_name" varchar(200),
    "latitude" float8,
    "longitude" float8,
    "speed" numeric,
    "course" numeric,
    "altitude" numeric,
    "locality" varchar(200),
    "diff_seconds" numeric,
    "diff_meters" numeric,
    "host_ip" varchar(200)
);


CREATE VIEW "public"."utl_arka_geometry" AS SELECT start.reference_day,
    start.device_id,
    start.device_group_name,
    start.device_name,
    start.local_timestamp AS start_local_timestamp,
    start.utc_timestamp AS start_utc_timestamp,
    start.id AS start_id,
    stop.local_timestamp AS stop_local_timestamp,
    stop.utc_timestamp AS stop_utc_timestamp,
    stop.id AS stop_id,
    ((stop.local_timestamp - start.local_timestamp))::character varying(15) AS ore_di_volo,
    geometry.geom
   FROM ( SELECT (ga.local_timestamp)::date AS reference_day,
            ga.id,
            ga.device_id,
            ga.device_name,
            ga.device_group_name,
            ga.local_timestamp,
            ga.utc_timestamp
           FROM utl_arka ga
          WHERE (NOT (EXISTS ( SELECT 1
                   FROM utl_arka gb
                  WHERE ((gb.device_id = ga.device_id) AND ((gb.local_timestamp)::date = (ga.local_timestamp)::date) AND (gb.local_timestamp < ga.local_timestamp) AND ((ga.local_timestamp - gb.local_timestamp) < '00:15:00'::interval)))))) start,
    LATERAL ( SELECT (s.local_timestamp)::date AS reference_day,
            s.id,
            s.device_id,
            s.device_name,
            s.device_group_name,
            s.local_timestamp,
            s.utc_timestamp
           FROM utl_arka s
          WHERE ((s.local_timestamp >= start.local_timestamp) AND ((s.local_timestamp)::date = start.reference_day) AND (s.device_id = start.device_id) AND (NOT (EXISTS ( SELECT 1
                   FROM utl_arka sb
                  WHERE ((sb.device_id = s.device_id) AND ((sb.local_timestamp)::date = (s.local_timestamp)::date) AND (sb.local_timestamp > s.local_timestamp) AND ((sb.local_timestamp - s.local_timestamp) < '00:15:00'::interval))))))
         LIMIT 1) stop,
    LATERAL ( SELECT st_makeline(geo.geo) AS geom
           FROM ( SELECT st_setsrid(st_makepoint(gea.longitude, gea.latitude), 4326) AS geo
                   FROM utl_arka gea
                  WHERE ((gea.device_id = start.device_id) AND ((gea.local_timestamp >= start.local_timestamp) AND (gea.local_timestamp <= stop.local_timestamp)))
                  ORDER BY gea.local_timestamp) geo) geometry
  ORDER BY start.local_timestamp DESC;
CREATE VIEW "public"."utl_arka_tempovolo" AS SELECT start.reference_day,
    start.device_id,
    start.device_group_name,
    start.device_name,
    start.local_timestamp AS start_local_timestamp,
    start.utc_timestamp AS start_utc_timestamp,
    start.id AS start_id,
    stop.local_timestamp AS stop_local_timestamp,
    stop.utc_timestamp AS stop_utc_timestamp,
    stop.id AS stop_id,
    (stop.local_timestamp - start.local_timestamp) AS ore_di_volo
   FROM (( SELECT (ga.local_timestamp)::date AS reference_day,
            'ON'::character varying(3) AS status,
            ga.id,
            ga.device_id,
            ga.device_name,
            ga.device_group_name,
            ga.local_timestamp,
            ga.utc_timestamp
           FROM utl_arka ga
          WHERE (NOT (EXISTS ( SELECT 1
                   FROM utl_arka gb
                  WHERE ((gb.device_id = ga.device_id) AND ((gb.local_timestamp)::date = (ga.local_timestamp)::date) AND (gb.local_timestamp < ga.local_timestamp) AND ((ga.local_timestamp - gb.local_timestamp) < '00:15:00'::interval)))))) start
     LEFT JOIN LATERAL ( SELECT (s.local_timestamp)::date AS reference_day,
            'OFF'::character varying(3) AS status,
            s.id,
            s.device_id,
            s.device_name,
            s.device_group_name,
            s.local_timestamp,
            s.utc_timestamp
           FROM utl_arka s
          WHERE ((s.local_timestamp >= start.local_timestamp) AND ((s.local_timestamp)::date = start.reference_day) AND (s.device_id = start.device_id) AND (NOT (EXISTS ( SELECT 1
                   FROM utl_arka sb
                  WHERE ((sb.device_id = s.device_id) AND ((sb.local_timestamp)::date = (s.local_timestamp)::date) AND (sb.local_timestamp > s.local_timestamp) AND ((sb.local_timestamp - s.local_timestamp) < '00:15:00'::interval))))))
         LIMIT 1) stop ON (((start.reference_day = stop.reference_day) AND (stop.device_id = start.device_id))))
  ORDER BY start.local_timestamp DESC;
CREATE MATERIALIZED VIEW "public"."utl_arka_mview_storico" AS SELECT utl_arka_geometry.reference_day,
    utl_arka_geometry.device_id,
    utl_arka_geometry.device_group_name,
    utl_arka_geometry.device_name,
    utl_arka_geometry.start_local_timestamp,
    utl_arka_geometry.start_utc_timestamp,
    utl_arka_geometry.start_id,
    utl_arka_geometry.stop_local_timestamp,
    utl_arka_geometry.stop_utc_timestamp,
    utl_arka_geometry.stop_id,
    utl_arka_geometry.ore_di_volo,
    utl_arka_geometry.geom
   FROM utl_arka_geometry
  WHERE (utl_arka_geometry.reference_day < ((now())::date - 1));
CREATE VIEW "public"."utl_arka_voli" AS SELECT utl_arka_mview_storico.reference_day,
    utl_arka_mview_storico.device_id,
    utl_arka_mview_storico.device_group_name,
    utl_arka_mview_storico.device_name,
    utl_arka_mview_storico.start_local_timestamp,
    utl_arka_mview_storico.start_utc_timestamp,
    utl_arka_mview_storico.start_id,
    utl_arka_mview_storico.stop_local_timestamp,
    utl_arka_mview_storico.stop_utc_timestamp,
    utl_arka_mview_storico.stop_id,
    utl_arka_mview_storico.ore_di_volo,
    utl_arka_mview_storico.geom
   FROM utl_arka_mview_storico
UNION ALL
 SELECT utl_arka_geometry.reference_day,
    utl_arka_geometry.device_id,
    utl_arka_geometry.device_group_name,
    utl_arka_geometry.device_name,
    utl_arka_geometry.start_local_timestamp,
    utl_arka_geometry.start_utc_timestamp,
    utl_arka_geometry.start_id,
    utl_arka_geometry.stop_local_timestamp,
    utl_arka_geometry.stop_utc_timestamp,
    utl_arka_geometry.stop_id,
    utl_arka_geometry.ore_di_volo,
    utl_arka_geometry.geom
   FROM utl_arka_geometry
  WHERE (utl_arka_geometry.start_local_timestamp > ( SELECT max(utl_arka_mview_storico.start_local_timestamp) AS max
           FROM utl_arka_mview_storico))
  ORDER BY 5 DESC;