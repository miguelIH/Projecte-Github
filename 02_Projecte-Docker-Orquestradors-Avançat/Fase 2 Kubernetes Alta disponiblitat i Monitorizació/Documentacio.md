# <p align="center"> Kubernetes: Alta disponiblitat i Monitorizació </p>
------------
## Configuració avançada de pods i serveis
Hem creat un fitxer YAML anomenat webserver-deployment.yaml per desplegar el servei web amb 3 rèpliques. Aquesta vegada hem fet servir l’objecte Deployment i hem exposat el servei amb un Service de tipus NodePort:
<br>
![Imatge1](Imatges/1.png)
<br>
Hem aplicat la configuració amb:
```
kubectl apply -f webserver-deployment.yaml
```
![Imatge2](Imatges/2.png)
<br>
## Ús de volums persistents, ConfigMaps i Secrets
Per assegurar-nos que les dades de MySQL no es perden, hem començat a preparar un PersistentVolume i PersistentVolumeClaim, tot i que en Minikube només s’usa a nivell local.
També hem començat a treballar amb ConfigMap per gestionar configuracions PHP i Secrets per guardar contrasenyes de forma segura:
<br>
![Imatge3](Imatges/3.png)
<br>
## Estratègies d’alta disponibilitat
El Deployment amb 3 rèpliques ja ens dona alta disponibilitat bàsica. Per reforçar-ho, hem afegit toleràncies per si un node falla
## Monitorització amb Prometheus i Grafana
Per aquesta fase hem començat a instal·lar Prometheus i Grafana per veure com es comporten els pods en temps real. El primer en ser instal·lat es Prometheus que ho farem de la següent manera:
Creen el fitxer prometheus-deployment.yaml amb aquest contingut:
```
apiVersion: v1
kind: Service
metadata:
  name: prometheus
spec:
  selector:
    app: prometheus
  ports:
    - protocol: TCP
      port: 9090
      targetPort: 9090
  type: NodePort
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: prometheus
spec:
  replicas: 1
  selector:
    matchLabels:
      app: prometheus
  template:
    metadata:
      labels:
        app: prometheus
    spec:
      containers:
      - name: prometheus
        image: prom/prometheus
        ports:
        - containerPort: 9090
```
![Imatge4](Imatges/4.png)
<br>
Despres ho apiquem amb:
```
kubectl apply -f prometheus-deployment.yaml
```
![Imatge5](Imatges/5.png)
<br>
Consulta de l’URL amb Minikube:
```
minikube service prometheus --url
```
![Imatge5](Imatges/5.png)
<br>
També es pot accedir des del navegador:
Primer creen el pont per poder accedir desde el servidor a la maquina real.
```
sudo socat TCP-LISTEN:31116,reuseaddr,fork TCP:192.168.49.2:31116
```
![Imatge6](Imatges/6.png)
<br>
http://192.168.1.100:31116/
<br>
![Imatge7](Imatges/7.png)
<br>
## Grafana
Fitxer grafana-deployment.yaml:
```
apiVersion: apps/v1
kind: Deployment
metadata:
  name: grafana
spec:
  replicas: 1
  selector:
    matchLabels:
      app: grafana
  template:
    metadata:
      labels:
        app: grafana
    spec:
      containers:
      - name: grafana
        image: grafana/grafana:latest
        ports:
        - containerPort: 3000
        env:
        - name: GF_SECURITY_ADMIN_USER
          value: "admin"
        - name: GF_SECURITY_ADMIN_PASSWORD
          value: "admin"
```
![Imatge8](Imatges/8.png)
<br>
Fitxer grafana-service.yaml:
```
apiVersion: v1
kind: Service
metadata:
  name: grafana
spec:
  selector:
    app: grafana
  ports:
    - protocol: TCP
      port: 3000
      targetPort: 3000
  type: NodePort
```
![Imatge9](Imatges/9.png)
<br>
Desplegament:
![Imatge10](Imatges/10.png)
<br>
Després hem consultat l’URL:
![Imatge11](Imatges/11.png)
<br>
Ara farem el pont:
![Imatge12](Imatges/12.png)
<br>







