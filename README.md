Websor - Sistema di gestione di Sala Operativa di Protezione Civile

Copyright (C) 2018-2020 - Regione Lazio - Agenzia Regionale di Protezione Civile

Rilasciato secondo licenza GNU Affero General Public License (License.md)



L'installazione comprende lavori originali o derivati di terze parti tra cui:

ISTAT - Basi Territoriali - http://www.istat.it - Licenza Creative Commons - Attribuzione - 3.0

IGM Toponimi - http://www.pcn.minambiente.it - Licenza Creative Commons Attribuzione - 3.0

OpenAddresses - http://openaddresses.io - Licenza nel file OPENADDRESSES-IO-LICENSE.md

OpenStreetMap - https://www.openstreetmap.org - Open Data Commons Open Database License (ODbL) 

YII Framework - https://www.yiiframework.com/ - BSD License



Istruzioni per l'installazione

#Installazione
- Clonare il repository

```bash
git clone <repository>
```

- Da terminale:

```bash
cd <directory>
git checkout 

#Assicurarsi che il branch corrente sia riuso
git branch

git pull origin

#Decomprimere i file più voluminosi
gzip -d addresses.csv.gz console/data/locations_shape/comuni_geom.dmp.gz console/data/locations_shape/geom_toponimi.sql.gz console/data/routing/routing_other.sql.gz



/path/to/php-bin/php init
composer install
```

- Creare Database
- Modificare file: `common/config/main-local.php` e `common/config/params-local.php` inserendo la configurazione del proprio ambiente


- Accedere al db
```sql 

CREATE EXTENSION postgis;
CREATE EXTENSION pgrouting;
CREATE EXTENSION hstore;
CREATE EXTENSION pg_trgm;
CREATE SCHEMA routing;

```

- Importare grafo stradale
```bash
psql -h hostname -d databasename -U username -f {path}/console/data/routing/routing_schema.sql

psql -h hostname -d databasename -U username -f {path}console/data/routing/routing_vertices.sql

psql -h hostname -d databasename -U username -f {path}console/data/routing/routing_pos.sql

psql -h hostname -d databasename -U username -f {path}console/data/routing/routing_other.sql
```

- Importare lo schema delle tabelle arka
```bash
psql -h hostname -d databasename -U username -f {path}console/data/elicotteri/arka_tables.sql
```

- lanciare le migration

```bash
./yii migrate --migrationPath=@yii/rbac/migrations/

./yii migrate
```

- Creare l'alberatura di permessi rbac:

```bash
./yii rbac/init
./yii rbac/update-permissions
```

- Creare utente amministratore e assegnargli un operatore:

```bash
./yii installer/addadmin -u="admin" -e="mail@mail.com" -p="password" -no="Nome" -co="Cognome" -opr="Dirigente" -mo="MATRICOLA" -ro="Admin" -wo="1"
```

- Popolare dati iniziali: comuni, province, regioni, nazioni, continenti, extra_segnalazioni

```bash
./yii installer/add-csv-data
```

- Inserimento loghi

Caricare loghi di dimensione minima 400x400

/backend/web/images/logo.png
/backend/web/images/logo_regione.gif




#Configurazione Layer geoserver


in `common\config\params-local.php`


### Tile layer

Es.
```php
[
    'name' => 'OSM SEIPCI',
    'type' => 'tile', 
    'identifier' => "osmlocal",
    'icon' => 'osm', // valori disponibili: osm, realvista
    'visible'=>true, // impostarne solo 1 visibile
    'source_type' => 'OlSourceOsm', // valori disponibili OlSourceOsm, TileWMS
    'source_config' => [ // array che viene passato a openlayer
        'attributions'=> ["WebSOR Maps"],
        'url' => "http://geoserver.mydomain.it/{z}/{x}/{y}.png"
    ]
],
[
    'name' => 'Ortofoto 2012 RealVista',
    'type' => 'tile',
    'identifier' => "realvista",
    'icon' => 'realvista',
    'visible'=>false, // impostarne solo 1 visibile
    'source_type' => 'TileWMS',
    'source_config' => [
        'url' => 'http://www.realvista.it/reflector/open/service',
        'params' => ['LAYERS' => 'rv1', 'TILED' => true, 'FORMAT' => 'image/jpeg']
    ]   
]
```

### Raggruppamenti

Inserire i layer raggruppati

Nel caso seguente viene creato un gruppo di nome Websor
Es.
```php 

[
    'name' => 'Websor',
    'type' => 'group', // tipo di layer
    'activable' => true,
    'layers' => (array) // i layer contenuti nel gruppo
]

```

Per i singoli layer:
```php

[
    'name' => 'Eventi',
    'type' => 'wms', // possibile wms|wfs|elicottero|image
    'visible' => true,
    'identifier' => "postgis:eventi",
    'url' => 'http://localhost:5001/geoserver/postgis/',
    'searchable' => true, // ricercabile con la ricerca per poligono, cerchio e punto
    'legendable' => true, // se impostato a true mostra la legenda quando il layer è attivo
    'refreshable' => true, // se impostato a true il layer viene ricaricato ogni 30 secondi (se attivo)
    'projection' => 'EPSG:4326', // importante se proiezioni diverse
    'geom_field' => 'the_geom', // da impostare se il campo che identifica la geometria nel layer è diverso da geom (case sensitive)
    'defaultSearchParams' => [ // parametri di ricerca di default su query CQL
        ['stato', '<>', '\'Chiuso\''] // {nome campo}, {operatore}, {valore}
    ]                    
],

```


