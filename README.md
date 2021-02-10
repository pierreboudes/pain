
# pain


Pain is a web application for easing the task of matching courses and teachers in a teaching department at university.

## Run a demo
You can run a demo version of pain using docker, by performing the following steps
1. clone the repo and cd into
   ```sh
   git checkout https://github.com/pierreboudes/pain.git paindemo
   cd paindemo
   ```
2. create a fake `iconnect.php`just by copying `server/iconnect.php`
   ```sh
   cp server/iconnect.php iconnect.php
   ```
3. build and run with docker-compose
  ```sh
  docker-compose build
  docker-compose up
  ```
4. point your browser to localhost:9380/demo/pain enjoy or feel a little bit disappointed by charset problems, as I am, and by the lack of documentation. If you understand a bit of french you may find your may here https://mindsized.org/spip.php?rubrique55.
