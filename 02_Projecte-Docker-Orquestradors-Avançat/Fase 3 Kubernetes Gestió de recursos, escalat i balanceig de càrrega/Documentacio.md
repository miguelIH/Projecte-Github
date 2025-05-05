# <p align="center"> Kubernetes: Gestió de Recursos, Probes i Alta Disponibilitat </p>
------------
En aquesta fase hem començat a aplicar bones pràctiques relacionades amb l'optimització de recursos i la supervisió de l'estat dels pods dins del nostre entorn Kubernetes. Aquesta part se centra en tres aspectes bàsics: **assignació de recursos, probes de salut, i verificació de l'estat real del pod.**

## Assignació de recursos (CPU i Memòria)
Per tal d'evitar que els pods utilitzin més recursos dels que els pertoquen o que es quedin sense recursos mínims, hem afegit la secció resources dins del fitxer webserver-deployment.yaml.
<br>
Aquest apartat permet definir:
•	requests: els recursos **mínims** que el pod necessita per executar-se.
•	limits: els recursos **màxims** que pot arribar a consumir.
<br>
![Imatge1](Imatges/1.png)
<br>
Això ens permet controlar l'assignació justa i equilibrada dels recursos del node.
## Probes de salut: Liveness i Readiness
També hem afegit dues probes molt importants per a la gestió d'aplicacions estables:
•	**livenessProbe:** comprova que el servei continua viu. Si falla, el pod es reinicia.
•	**readinessProbe:** comprova si el pod està llest per rebre peticions. Si falla, es deixa de derivar-li trànsit.
Aquestes probes són especialment útils en escenaris reals per garantir un entorn estable i amb menys downtime.
<br>
![Imatge2](Imatges/2.png)
<br>
## Verificació del Pod
Un cop desplegat, hem comprovat que els recursos i les probes estiguessin aplicades correctament mitjançant la comanda:
```
kubectl describe pod webserver-9b87d
```
A la sortida es poden veure clarament:
•	Els requests i limits aplicats
•	L’estat actual del pod com a Running
•	Les probes de liveness i readiness configurades correctament
<br>
![Imatge3](Imatges/3.png)
<br>
![Imatge4](Imatges/4.png)
<br>
## Kubernetes: Escalat Manual i Automàtic
En aquesta segona part de la Fase 3, ens hem centrat a configurar l'escalabilitat dels nostres serveis web desplegats a Kubernetes. Hem treballat tant l’escalat manual com l’automàtic basat en consum de CPU.
## Escalat manual
Primer hem practicat l'escalat manual del nostre servei webserver augmentant el nombre de rèpliques.
Comanda utilitzada:
```
kubectl scale deployment webserver --replic
```
![Imatge5](Imatges/5.png)
<br>
Amb aquesta comanda, hem passat de 3 a 5 rèpliques manualment.
Hem pogut comprovar els canvis amb:
```
kubectl get deployments
```
![Imatge6](Imatges/6.png)
Veient que ara el Deployment webserver tenia 5 pods actius.
## Creació de l’HPA
Després, hem configurat l’escalat automàtic amb HPA perquè Kubernetes pugui adaptar el nombre de rèpliques del webserver de manera dinàmica segons el consum de CPU.
![Imatge7](Imatges/7.png)
## Explicació del YAML
**scaleTargetRef**: indica que volem escalar el Deployment anomenat webserver.
**minReplicas**: el nombre mínim de pods serà 2.
**maxReplicas**: el nombre màxim de pods serà 6.
**averageUtilization**: si el consum de CPU supera el 50%, Kubernetes crearà nous pods automàticament.
## Desplegament de l’HPA
Hem aplicat la configuració amb:
```
kubectl apply -f hpa-webserver.yaml
```
I hem comprovat que l’HPA estava funcionant amb:
```
kubectl get hpa
```
Aquesta comanda ens ha permès veure l’ús real de CPU i memòria de tots els pods. En el nostre cas, els pods del webserver consumeixen entre 2m i 3m de CPU, molt per sota del 50% que hem definit com a límit al HPA. Això explica per què l’autoscaler no ha incrementat el nombre de rèpliques.
```
kubectl top pods
```





