# letrading
Site internet du trading.fr

## Mercure launch server

<ul>
  <li>Windows : <br>JWT_KEY='myJWTKey' ADDR=':3000' ALLOW_ANONYMOUS=1 PUBLISH_ALLOWED_ORIGINS='http://127.0.0.1:3000' CORS_ALLOWED_ORIGINS="http://127.0.0.1:8000" ./mercure/mercure</li>
  <li>Mac : <br>JWT_KEY='myJWTKey' ADDR=':3000' ALLOW_ANONYMOUS=1 PUBLISH_ALLOWED_ORIGINS='http://127.0.0.1:3000' CORS_ALLOWED_ORIGINS="http://127.0.0.1:8000" ./mercureMac/mercure</li>
</ul>

## Mention + Emoji Forum
Les plugins disponibles dans <strong>public>lib>ckeditor</strong> sont à déplacer dans <strong>public>bundles>fosckeditor>plugins</strong>

Les plugins seront à déplacer dans le dossier après chaque lancement de <strong>composer update</strong>

## Lancement serveur ngrok
Ngrok est a configurer en local afin de faire le lien entre le site sur 127.0.0.1 et le dashboard de stripe

<ul>
<li>Installer ngrok sur <a href="https://ngrok.com/download">ngrok.com</a></li>
<li>Sur le cmd lancer <strong>./ngrok http 127.0.0.1:8000</strong></li>
<li>Aller sur <a href="http://127.0.0.1:4040/inspect/http">http://127.0.0.1:4040/inspect/http</a></li>
<li>Récuperer le lien http généré</li>
<li>Se rendre sur le <strong>Dashboard stripe > Développeurs > Webhooks > Ajouter un endpoint</strong></li>
<li>Puis coller l'url et Ajouter évenements à envoyer : 
 <strong>
 invoice.payment_succeeded<br>
 checkout.session.completed<br>
 customer.subscription.deleted
 </strong>
 </li>
</ul>


## To do list : bugs
<ul>
<li>Redimensionnement image avatar (forcer le format 400 X 400)</li>
</ul>
