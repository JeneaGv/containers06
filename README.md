# Crearea unei aplicații multi-container

# Scopul lucrării

<H1>Famialiarizarea cu gestiunea aplicației multi-container creat cu docker-compose.</H1>

# Sarcina 

<h1>Creați o aplicație php pe baza a trei containere: nginx, php-fpm, mariadb, folosind docker-compose.</h1>

# Execuție
<ol>
<li>
Cream un repozitoriu containers06 și copiați-l pe computerul dvs.Aceasta o facem cu ajutorul comenzii git clone URL la repositoriu

![image](https://github.com/user-attachments/assets/91599766-e855-4e08-bebe-734b6d85cde8)

  
</li>
  <li>
    În directorul containers06 cream directorul mounts/site. În acest director, rescriem site-ul pe php, creat în cadrul disciplinei php(in directorul crafti_app avem toata aplicatia).

![image](https://github.com/user-attachments/assets/f54d39f6-7503-48ce-9683-4a5da997fba7)

    
  </li>

  <li>

 Cream fișierul .gitignore cu ajutorul comenzii New-Item -Path . -Name ".gitignore" -ItemType "File" -Force 
 
 în rădăcina proiectului și adăugam următoarele linii:

" # Ignore files and directories
mounts/site/* "

![image](https://github.com/user-attachments/assets/acd9625f-2008-4409-b2dd-df428f278349)

![image](https://github.com/user-attachments/assets/8b9a06f4-1881-4bc6-8e00-93be77063e8b)


  </li>

  <li>

Cream în directorul containers06 fișierul nginx/default.conf cu următorul conținut:

![image](https://github.com/user-attachments/assets/5c2feef9-6dfd-48ba-9587-7bf1b3f9e430)

    
  </li>

  <li>

Cream în directorul containers06 fișierul docker-compose.yml cu următorul conținut:

version: '3.9'

services:
  frontend:
    image: nginx:1.19
    volumes:
      - ./mounts/sites/crafti_app:/var/www/html
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "80:80"
    networks:
      - internal
  backend:
    image: php:7.4-fpm
    volumes:
      - ./mounts/sites/crafti_app:/var/www/html
    networks:
      - internal
    env_file:
      - mysql.env
  database:
    image: mysql:8.0
    env_file:
      - mysql.env
    networks:
      - internal
    volumes:
      - db_data:/var/lib/mysql

networks:
  internal: {}

volumes:
  db_data: {}
    
  </li>

  <li>

 Cream fileul mysql.env în rădăcina proiectului și adăugați în el următoarele linii:

 MYSQL_ROOT_PASSWORD=secret
MYSQL_DATABASE=app
MYSQL_USER=user
MYSQL_PASSWORD=secret

  </li>
</ol>

# Pornire și testare

![image](https://github.com/user-attachments/assets/d28f43c5-5307-4232-be5e-a57956532c50)

![image](https://github.com/user-attachments/assets/4015fa3a-b8da-4342-b03a-b903922dfe9f)

introducem in bara de navigare adresa http://localhost:

![image](https://github.com/user-attachments/assets/4810a9f0-cb94-4f92-b7b2-d30e1d3cdf58)

# Raspunsuri la intrebari 

1. În ce ordine sunt pornite containerele?
Docker Compose pornește containerele conform dependințelor definite în fișierul docker-compose.yml. În acest caz specific, deși nu există directive explicite depends_on, Docker Compose va încerca să pornească containerele în ordinea definită în fișier:

frontend (nginx)

backend (php-fpm)

database (mysql)

2. Unde sunt stocate datele bazei de date?

Datele bazei de date sunt stocate într-un volum Docker numit db_data. 

Acest volum este definit în secțiunea volumes a fișierului docker-compose.yml și atașat la serviciul database

3.Cum se numesc containerele proiectului?

Containerele poarta nume de : 

![image](https://github.com/user-attachments/assets/ce1f178f-8ef5-4608-bc12-2b06d9807ca9)

4.Trebuie să adăugați încă un fișier app.env cu variabila de mediu APP_VERSION pentru serviciile backend și frontend. Cum se face acest lucru?

Pentru a adăuga un nou fișier de variabile de mediu, trebuie să:

Creezm fișierul app.env în rădăcina proiectului cu conținutul:
APP_VERSION=1.0

Modifici fișierul docker-compose.yml pentru a include acest fișier în configurația serviciilor:
yamlfrontend:
  image: nginx:1.19
  ...
  
  ...
  env_file:
    - app.env

backend:
  image: php:7.4-fpm
  ... 

  ...


  env_file:
    - mysql.env
    - app.env


Acest lucru va face ca variabila APP_VERSION să fie disponibilă în ambele containere.
