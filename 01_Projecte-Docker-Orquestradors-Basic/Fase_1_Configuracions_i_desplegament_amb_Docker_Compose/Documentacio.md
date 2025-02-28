# <p align="center">  Introducció a Docker Compose i la seva sintaxis </p>
------------
aaaa

# <p align="center">  Introducció a Docker Compose i la seva sintaxis </p>
------------
El primer pass ha de ser instal·lar (en cas de que no ho este ja), el docker, ho farem amb la següent comanda:
```
sudo apt-get install docker.io
```
![Imatge1](Imatges/1.png)
<br>
Seguidament haurem de activar i revisar el estat del docker:
```
sudo systemctl start docker
sudo systemctl enable docker
```
![Imatge2](Imatges/2.png)
<br>
Després revisem la versió del docker, amb la següent comanda:
```
docker --version
```
![Imatge3](Imatges/3.png)
<br>
Ara el que hem de fer es instal·lar el docker compose.
```
sudo apt-get install docker-compose
```
![Imatge4](Imatges/4.png)
<br>
Revisem la versio per si de cas:
```
docker-compose up -d
```
També podem utilitzar aquesta comanda:
```
docker-compose --version
```
El següent pas es revisar els contenidors:
```
docker-compose ps
```
![Imatge5](Imatges/5.png)


